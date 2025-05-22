@extends('frontend.layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center">
                    <h1 class="text-success mb-4">
                        <i class="fas fa-check-circle"></i>
                    </h1>
                    <h2 class="card-title mb-4">Appointment Booked Successfully!</h2>
                    
                    <div class="booking-details mb-4">
                        <p><strong>Booking ID:</strong> {{ $appointment->booking_id }}</p>
                        <p><strong>Service:</strong> {{ $appointment->service->title }}</p>
                        <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($appointment->booking_date)->format('F d, Y') }}</p>
                        <p><strong>Time:</strong> {{ $appointment->booking_time }}</p>
                        <p><strong>Amount:</strong> TZS {{ number_format($appointment->amount) }}</p>
                        <p><strong>Status:</strong> 
                            <span class="badge {{ $appointment->status === 'Pending payment' ? 'bg-warning' : 
                                                ($appointment->status === 'Confirmed' ? 'bg-success' : 
                                                ($appointment->status === 'Cancelled' ? 'bg-danger' : 'bg-info')) }}">
                                {{ $appointment->status }}
                            </span>
                        </p>
                    </div>

                    <div class="alert alert-info">
                        <p class="mb-0">We have sent a confirmation email to {{ $appointment->email }}</p>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('home') }}" class="btn btn-primary">Return to Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
 