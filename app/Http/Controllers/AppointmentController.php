<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Events\BookingCreated;
use App\Events\StatusUpdated;
use App\Models\Service;


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
            'booking_time' => 'required',
            'notes' => 'nullable|string',
        ]);

        // Generate unique booking ID
        $bookingId = 'BK-' . strtoupper(uniqid());

        // Get service price
        $service = Service::findOrFail($request->service_id);
        $amount = $service->sale_price ?? $service->price;

        // Extract start time from the time range
        $timeRange = explode(' - ', $request->booking_time);
        $startTime = \Carbon\Carbon::createFromFormat('g:i A', trim($timeRange[0]))->format('H:i:s');

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
            'booking_time' => $startTime,
            'status' => 'Pending payment',
        ]);

        return redirect()->route('appointment.success', ['booking_id' => $bookingId])
            ->with('success', 'Appointment booked successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        //
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
