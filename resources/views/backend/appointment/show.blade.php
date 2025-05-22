@extends('adminlte::page')

@section('title', 'Appointment Details')

@section('content_header')
    <h1>Appointment Details</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Appointment Information</h3>
            <div class="card-tools">
                <a href="{{ route('appointments') }}" class="btn btn-default">
                    <i class="fas fa-arrow-left"></i> Back to Appointments
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 200px;">Booking ID</th>
                            <td>{{ $appointment->booking_id }}</td>
                        </tr>
                        <tr>
                            <th>Customer Name</th>
                            <td>{{ $appointment->name }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $appointment->email }}</td>
                        </tr>
                        <tr>
                            <th>Phone</th>
                            <td>{{ $appointment->phone }}</td>
                        </tr>
                        <tr>
                            <th>Service</th>
                            <td>{{ $appointment->service->title }}</td>
                        </tr>
                        <tr>
                            <th>Staff</th>
                            <td>{{ $appointment->employee->user->name }}</td>
                        </tr>
                        <tr>
                            <th>Date</th>
                            <td>{{ $appointment->booking_date }}</td>
                        </tr>
                        <tr>
                            <th>Time</th>
                            <td>{{ $appointment->booking_time }}</td>
                        </tr>
                        <tr>
                            <th>Amount</th>
                            <td>${{ number_format($appointment->amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge px-2 py-1" style="background-color: {{ $appointment->status === 'Confirmed' ? '#2ecc71' : 
                                    ($appointment->status === 'Pending payment' ? '#f39c12' : 
                                    ($appointment->status === 'Cancelled' ? '#e74c3c' : 
                                    ($appointment->status === 'Completed' ? '#27ae60' : 
                                    ($appointment->status === 'On Hold' ? '#95a5a6' : 
                                    ($appointment->status === 'Rescheduled' ? '#f1c40f' : '#3498db'))))) }}; color: white;">
                                    {{ $appointment->status }}
                                </span>
                            </td>
                        </tr>
                        @if($appointment->notes)
                        <tr>
                            <th>Notes</th>
                            <td>{{ $appointment->notes }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            @if(auth()->user()->hasRole('admin'))
            <div class="row mt-4">
                <div class="col-md-6">
                    <form action="{{ route('appointments.update.status') }}" method="POST">
                        @csrf
                        <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">
                        <div class="form-group">
                            <label for="status">Update Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="Pending payment" {{ $appointment->status === 'Pending payment' ? 'selected' : '' }}>Pending Payment</option>
                                <option value="Processing" {{ $appointment->status === 'Processing' ? 'selected' : '' }}>Processing</option>
                                <option value="Confirmed" {{ $appointment->status === 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="Cancelled" {{ $appointment->status === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                <option value="Completed" {{ $appointment->status === 'Completed' ? 'selected' : '' }}>Completed</option>
                                <option value="On Hold" {{ $appointment->status === 'On Hold' ? 'selected' : '' }}>On Hold</option>
                                <option value="Rescheduled" {{ $appointment->status === 'Rescheduled' ? 'selected' : '' }}>Rescheduled</option>
                                <option value="No Show" {{ $appointment->status === 'No Show' ? 'selected' : '' }}>No Show</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
@stop 