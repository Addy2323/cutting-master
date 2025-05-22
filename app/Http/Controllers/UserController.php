<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Service;
use App\Models\Holiday;
use App\Models\Employee;
use Hash;
use Session;
class UserController extends Controller
{


    public function index(Request $request)
    {
        // Get the role type from the request (either 'employee', 'customer', or 'moderator')
        $users = User::latest()->get();
        return view('backend.user.index', compact('users'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $days = [
            'monday',
            'tuesday',
            'wednesday',
            'thursday',
            'friday',
            'saturday',
            'sunday',
        ];

        //$roles = Role::where('name', '!=', 'admin')->get();
        $roles = Role::where('name', '!=', 'admin')->get();
        $services = Service::whereStatus(1)->get();
        return view('backend.user.create',compact('roles','services','days'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'phone' => 'required|string|unique:users,phone',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array|size:1|exists:roles,name', // Enforce single role
            'service' => 'nullable|array',
            'slot_duration' => 'nullable|integer|min:1',
            'break_duration' => 'nullable|integer|min:0',
            'days' => 'nullable|array',
            'is_employee' => 'nullable|boolean',
        ]);

        // Only admins can create users with admin role
        if (in_array('admin', $data['roles']) && !auth()->user()->isAdmin()) {
            return redirect()->back()->withErrors(['roles' => 'Only administrators can create admin users.']);
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? "",
            'email_verified_at' => now(),
            'password' => \Hash::make($data['password']),
        ]);

        // Assign the role to the user
        $user->assignRole($data['roles'][0]); // Assign only the first role

        // If user is an employee, create employee record and attach services
        if($request->is_employee)
        {
            $transformedData = $this->transformOpeningHours($data['days']);
            $data['days'] = $transformedData;

            $employee = Employee::create([
                'user_id'           => $user['id'],
                'days'              => $data['days'],
                'slot_duration'     => $data['slot_duration'],
                'break_duration'    => $data['break_duration'],
            ]);

            if (isset($data['service']) && is_array($data['service'])) {
                $employee->services()->attach($data['service']);
            }
        }

        return redirect()->back()->withSuccess('User has been created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        // Available days of the week
        $days = [
            'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday',
        ];

        // Available slot duration steps
        $steps = ['10', '15', '20', '30', '45', '60'];

        // Available break duration steps
        $breaks = ['5', '10', '15', '20', '25', '30'];

        // Get the user and the related employee data
        $user = User::with('employee.holidays')->findOrFail($id);

        //dd($user->employee->holidays);

        // Get the employee's availability (days) data if it exists and convert to an array
        $employeeDays = $user->employee->days ?? [];

        // Transform availability slots
        $employeeDays = $this->transformAvailabilitySlotsForEdit($employeeDays);

        //dd($employeeDays);

        // Get all roles excluding 'admin'
        $roles = Role::all();
       // $roles = Role::where('name', '!=', 'admin')->get();

        // Get all active services
        $services = Service::whereStatus(1)->get();

        // Return the view with data
        return view('backend.user.edit', compact('user', 'roles', 'services', 'days', 'steps', 'breaks', 'employeeDays'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'social.*' => 'sometimes',
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'nullable|array|size:1|exists:roles,name', // Enforce single role
            'service' => 'nullable|array',
            'slot_duration' => function ($attribute, $value, $fail) use ($request) {
                if ($request->is_employee && !$value) {
                    $fail('The ' . $attribute . ' field is required when the employee is true.');
                }
                if ($value && !is_numeric($value)) {
                    $fail('The ' . $attribute . ' field must be numeric.');
                }
            },
            'break_duration' => 'nullable|integer|min:0',
            'days' => 'nullable|array',
            'status' => 'nullable|numeric',
            'is_employee' => 'nullable|boolean',
            'holidays.date.*' => 'sometimes|required',
            'holidays.from_time' => 'nullable',
            'holidays.to_time' => 'nullable',
            'holidays.recurring' => 'nullable',
        ]);

        // Only admins can change roles
        if ($request->filled('roles') && !auth()->user()->isAdmin()) {
            return redirect()->back()->withErrors(['roles' => 'Only administrators can change user roles.']);
        }

        // Block users from changing their own role
        if (\Auth::id() === $user->id && $request->filled('roles')) {
            return redirect()->back()->withErrors(['roles' => 'You cannot change your own role.']);
        }

        // Always keep 'admin' role for super admin (user ID 1)
        if ($user->id === 1 && (!in_array('admin', $request->roles ?? []))) {
            return redirect()->back()->withErrors(['roles' => 'The first user must always have the admin role.']);
        }

        // Update user details
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone ?? $user->phone,
            'password' => $request->password ? \Hash::make($request->password) : $user->password,
            'status' => $user->id === 1 ? 1 : ($request->status ?? 0),
        ]);

        // Update role if provided
        if ($request->filled('roles')) {
            $user->syncRoles([$request->roles[0]]); // Sync with single role
        }

        // Update employee details if applicable
        if (!empty($data['is_employee'])) {
            if (!empty($data['days'])) {
                $data['days'] = $this->transformOpeningHours($data['days']);
            }

            $employee = Employee::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'days' => $data['days'] ?? null,
                    'slot_duration' => $data['slot_duration'] ?? null,
                    'break_duration' => $data['break_duration'] ?? null
                ]
            );

            if (!empty($data['service'])) {
                $employee->services()->sync($data['service']);
            }
        }

        return redirect()->route('user.index')->with('success', 'Profile has been updated successfully!');
    }



    // Custom method to log out a specific user
    protected function logoutUser(User $user)
    {
          // Check if the application is using the database session driver
        if (config('session.driver') === 'database') {
            // Delete all sessions for this user by matching the user ID
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user, Request $request)
    {
        if($user->id == 1)
        {
            return back()->withErrors('First admin user cannot be deleted.');
        }

        if ($user->id === $request->user()->id) {
            return back()->withErrors('You cannot delete yourself.');
        }

        $user->delete();
        return redirect()->back()->with('success', 'User has been successfully trashed!');
    }


    public function trashView(Request $request)
    {
        $users = User::onlyTrashed()->latest()->get();
        return view('backend.user.trash',compact('users'));
    }

    // restore data
    public function restore($id)
    {
        $user = User::withTrashed()->find($id);
        if(!is_null($user)){
            $user->restore();
        }
        return redirect()->back()->with("success", "User Restored Succesfully");
    }


    public function force_delete($id)
    {
        // Retrieve the trashed user with its associated employee, holidays, appointments, and bookings
        $user = User::withTrashed()->findOrFail($id);

        //for employee
        if($user->employee->appointments->count())
        {
            return back()->withErrors('User cannot be deleted permanently, already engaged in existing bookings!');
        }

        //for user
        if($user->appointments->count())
        {
            return back()->withErrors('User cannot be deleted permanently, already engaged in existing bookings!');
        }

        // Check if the user has an associated employee
        if ($user->employee) {
            // Delete all holidays related to the employee
            foreach ($user->employee->holidays as $holiday) {
                $holiday->forceDelete(); // Force delete each holiday
            }


            // Delete all appointments related to the employee
            // foreach ($user->employee->appointments as $appointment) {
            //     $appointment->forceDelete(); // Force delete each appointment
            // }

                 // Detach all services related to the employee (many-to-many relationship)
            if ($user->employee->services()->exists()) {
                $user->employee->services()->detach(); // Detach the services from the employee
            }

            // Finally, delete the employee data
            $user->employee->forceDelete();
        }

        // Delete the user's profile image if exists
        if ($user->image) {
            $destination = public_path('uploads/images/profile/' . $user->image);
            if (\File::exists($destination)) {
                \File::delete($destination);
            }
        }

        // Permanently delete the user from the database
        $user->forceDelete();

        return back()->withSuccess('User and all related data (employee, holidays, appointments, bookings) have been deleted permanently!');
    }



    public function password_update(Request $request, User $user)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password does not match!']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->back()->with('success', 'Password has been successfully Updated!');
    }

    public function updateProfileImage(Request $request, User $user)
    {
        $request->validate([
            'image' => 'required|image|mimes:png,jpg,jpeg,webp|max:2048',
            'delete_image' => 'nullable'
        ]);

        //remove old image
        $destination = public_path('uploads/images/profile/'. $user->image);
        if(\File::exists($destination))
        {
            \File::delete($destination);
        }

        $imageName = time().'.'.$request->image->getClientOriginalExtension();
        $request->image->move(public_path('uploads/images/profile/'),$imageName);
        $user->update([
            'image' => $imageName
        ]);

        return back()->withSuccess('Profile image has been updated successfully!');

    }


    //delete profile image
    public function deleteProfileImage(User $user)
    {
        $destination = public_path('uploads/images/profile/'.$user->image);
        if(\File::exists($destination))
        {
            \File::delete($destination);
        }

        $user->update([
            'image' => null
        ]);
        return back()->withSuccess('Profile image deleted!');
    }


    // Transform the data
    function transformOpeningHours($data)
    {
        $result = [];

        foreach ($data as $day => $times) {
            $dayHours = [];
            for ($i = 0; $i < count($times); $i += 2) {
                if (isset($times[$i + 1])) {
                    $dayHours[] = $times[$i] . '-' . $times[$i + 1];
                }
            }
            $result[$day] = $dayHours;
        }

        return $result;
    }



    protected function transformAvailabilitySlotsForEdit(array $employeeDays)
    {
        foreach ($employeeDays as $day => $slots) {
            $transformedSlots = [];
            foreach ($slots as $slot) {
                list($startTime, $endTime) = explode('-', $slot);
                $transformedSlots[] = $startTime;
                $transformedSlots[] = $endTime;
            }
            $employeeDays[$day] = $transformedSlots;
        }

        return $employeeDays;
    }






}
