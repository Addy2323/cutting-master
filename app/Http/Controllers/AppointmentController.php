<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Events\BookingCreated;
use App\Events\StatusUpdated;
use App\Models\Service;
use App\Models\Notification;
use App\Models\User;


class AppointmentController extends Controller
{

    public function index()
    {
        $appointments = Appointment::latest()->get();
        // dd($appointments); // for debugging only
        return view('backend.appointment.index', compact('appointments'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'service_id' => 'required|exists:services,id',
            'employee_id' => 'required|exists:employees,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => ['required', 'string', 'not_in:undefined - undefined,undefined,'],
            'notes' => 'nullable|string',
        ], [
            'booking_time.not_in' => 'Please select a valid time slot.'
        ]);

        if ($request->booking_time === 'undefined - undefined' || $request->booking_time === 'undefined' || empty($request->booking_time)) {
            return response()->json(['message' => 'Please select a valid time slot.'], 422);
        }

        // Generate unique booking ID
        $bookingId = 'BK-' . strtoupper(uniqid());

        // Get service price
        $service = Service::findOrFail($request->service_id);
        $amount = $service->sale_price ?? $service->price;
        $amount = str_replace(['TZS ', ','], '', $amount); // Remove currency formatting if present

        // Create appointment
        $appointment = Appointment::create([
            'user_id' => auth()->id() ?? null,
            'employee_id' => $request->employee_id,
            'service_id' => $request->service_id,
            'booking_id' => $bookingId,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'notes' => $request->notes,
            'amount' => $amount,
            'booking_date' => $request->booking_date,
            'booking_time' => $request->booking_time,
            'status' => 'Pending payment',
        ]);

        // Create notification for admin
        $admin = User::role('admin')->first();
        if ($admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'appointment',
                'title' => 'New Appointment',
                'message' => "New appointment booked by {$request->name}",
                'link' => route('appointments.show', $appointment->id)
            ]);
        }

        // Create notification for staff
        $staff = User::find($appointment->employee->user_id);
        if ($staff) {
            Notification::create([
                'user_id' => $staff->id,
                'type' => 'appointment',
                'title' => 'New Appointment',
                'message' => "You have a new appointment with {$request->name}",
                'link' => route('appointments.show', $appointment->id)
            ]);
        }

        return redirect()->route('appointment.success', ['booking_id' => $bookingId])
            ->with('success', 'Appointment booked successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        return view('backend.appointment.show', compact('appointment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Appointment $appointment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Appointment $appointment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        //
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'status' => 'required|string|in:Pending payment,Processing,Confirmed,Cancelled,Completed,On Hold,Rescheduled,No Show',
        ]);

        $appointment = Appointment::findOrFail($request->appointment_id);
        $appointment->status = $request->status;
        $appointment->save();

        event(new StatusUpdated($appointment));

        return redirect()->back()->with('success', 'Appointment status updated successfully.');
    }

    public function success(Request $request)
    {
        $bookingId = $request->booking_id;
        $appointment = Appointment::where('booking_id', $bookingId)->firstOrFail();
        
        return view('frontend.appointment.success', compact('appointment'));
    }

}
