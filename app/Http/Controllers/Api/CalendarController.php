<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function getAppointments(Request $request)
    {
        $start = Carbon::parse($request->start);
        $end = Carbon::parse($request->end);

        $appointments = Appointment::with(['user', 'service', 'employee'])
            ->whereBetween('start_time', [$start, $end])
            ->get()
            ->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'title' => $appointment->service->name,
                    'start' => $appointment->start_time,
                    'end' => $appointment->end_time,
                    'is_at_home' => $appointment->is_at_home,
                    'client_name' => $appointment->user->name,
                    'service_name' => $appointment->service->name,
                    'address' => $appointment->is_at_home ? 
                        "{$appointment->address}, {$appointment->city}, {$appointment->postal_code}" : 
                        null,
                    'travel_buffer_minutes' => $appointment->is_at_home ? 
                        $appointment->service->getTravelBufferForEmployee($appointment->employee) : 
                        null,
                    'backgroundColor' => $appointment->is_at_home ? '#28a745' : '#007bff',
                    'borderColor' => $appointment->is_at_home ? '#28a745' : '#007bff',
                    'textColor' => '#ffffff'
                ];
            });

        return response()->json($appointments);
    }
} 