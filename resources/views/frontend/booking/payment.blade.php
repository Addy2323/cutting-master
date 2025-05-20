@extends('frontend.layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Payment for Appointment</div>

                <div class="card-body">
                    <div class="appointment-details mb-4">
                        <h5>Appointment Details</h5>
                        <p><strong>Service:</strong> {{ $appointment->service->title }}</p>
                        <p><strong>Date:</strong> {{ $appointment->date }}</p>
                        <p><strong>Time:</strong> {{ $appointment->time }}</p>
                        <p><strong>Amount:</strong> TZS {{ number_format($appointment->service->price, 2) }}</p>
                    </div>

                    <div class="payment-methods mb-4">
                        <h5>Select Payment Method</h5>
                        <div class="row">
                            @foreach($paymentMethods as $method)
                            <div class="col-md-3 mb-3">
                                <div class="payment-method-card" data-method="{{ $method->code }}">
                                    <img src="{{ $method->icon_url }}" alt="{{ $method->name }}" class="img-fluid payment-icon">
                                    <p class="text-center mt-2">{{ $method->name }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- M-Pesa Form -->
                    <div id="mpesa-form" class="payment-form" style="display: none;">
                        <form id="mpesa-payment-form">
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="tel" class="form-control" name="phone" placeholder="Enter M-Pesa number">
                            </div>
                            <button type="submit" class="btn btn-primary">Pay with M-Pesa</button>
                        </form>
                    </div>

                    <!-- Tigo Pesa Form -->
                    <div id="tigopesa-form" class="payment-form" style="display: none;">
                        <form id="tigo-payment-form">
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="tel" class="form-control" name="phone" placeholder="Enter Tigo Pesa number">
                            </div>
                            <button type="submit" class="btn btn-primary">Pay with Tigo Pesa</button>
                        </form>
                    </div>

                    <!-- Halotel Form -->
                    <div id="halotel-form" class="payment-form" style="display: none;">
                        <form id="halotel-payment-form">
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="tel" class="form-control" name="phone" placeholder="Enter Halotel number">
                            </div>
                            <button type="submit" class="btn btn-primary">Pay with Halotel</button>
                        </form>
                    </div>

                    <!-- Stripe Form -->
                    <div id="stripe-form" class="payment-form" style="display: none;">
                        <form id="stripe-payment-form">
                            <div id="payment-element" class="mb-4">
                                <!-- Stripe Elements will be inserted here -->
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <span id="button-text">Pay with Card</span>
                                <span id="spinner" class="spinner hidden"></span>
                            </button>
                        </form>
                    </div>

                    <div id="payment-message" class="hidden"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    // Initialize Stripe
    const stripe = Stripe('{{ config('services.stripe.key') }}');
    const elements = stripe.elements();
    const paymentElement = elements.create('payment');
    paymentElement.mount('#payment-element');

    // Handle payment method selection
    $('.payment-method-card').click(function() {
        const method = $(this).data('method');
        $('.payment-form').hide();
        $(`#${method}-form`).show();
    });

    // Handle M-Pesa payment
    $('#mpesa-payment-form').submit(async function(e) {
        e.preventDefault();
        const phone = $(this).find('input[name="phone"]').val();
        
        try {
            const response = await fetch('/payment/mpesa/initiate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    appointment_id: '{{ $appointment->id }}',
                    phone: phone
                })
            });

            const data = await response.json();
            if (data.success) {
                window.location.href = data.redirect_url;
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            $('#payment-message').text(error.message).removeClass('hidden');
        }
    });

    // Handle Tigo Pesa payment
    $('#tigo-payment-form').submit(async function(e) {
        e.preventDefault();
        const phone = $(this).find('input[name="phone"]').val();
        
        try {
            const response = await fetch('/payment/tigo/initiate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    appointment_id: '{{ $appointment->id }}',
                    phone: phone
                })
            });

            const data = await response.json();
            if (data.success) {
                window.location.href = data.redirect_url;
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            $('#payment-message').text(error.message).removeClass('hidden');
        }
    });

    // Handle Halotel payment
    $('#halotel-payment-form').submit(async function(e) {
        e.preventDefault();
        const phone = $(this).find('input[name="phone"]').val();
        
        try {
            const response = await fetch('/payment/halotel/initiate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    appointment_id: '{{ $appointment->id }}',
                    phone: phone
                })
            });

            const data = await response.json();
            if (data.success) {
                window.location.href = data.redirect_url;
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            $('#payment-message').text(error.message).removeClass('hidden');
        }
    });

    // Handle Stripe payment
    $('#stripe-payment-form').submit(async function(e) {
        e.preventDefault();
        const submitButton = $(this).find('button[type="submit"]');
        submitButton.prop('disabled', true);

        try {
            const response = await fetch('{{ route('payment.create-intent') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    appointment_id: '{{ $appointment->id }}'
                })
            });

            const data = await response.json();
            if (data.error) {
                throw new Error(data.error);
            }

            const { error } = await stripe.confirmPayment({
                elements,
                clientSecret: data.clientSecret,
                confirmParams: {
                    return_url: '{{ route('appointment.success') }}',
                }
            });

            if (error) {
                throw error;
            }
        } catch (error) {
            $('#payment-message').text(error.message).removeClass('hidden');
            submitButton.prop('disabled', false);
        }
    });
</script>
@endpush

@push('styles')
<style>
    .payment-method-card {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .payment-method-card:hover {
        border-color: #007bff;
        box-shadow: 0 0 10px rgba(0,123,255,0.2);
        transform: translateY(-2px);
    }

    .payment-icon {
        max-height: 40px;
        max-width: 100%;
        object-fit: contain;
        margin-bottom: 10px;
    }

    .payment-method-card p {
        margin: 0;
        font-size: 0.9rem;
        color: #333;
    }

    .spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255, 255, 255, .3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .hidden {
        display: none;
    }
</style>
@endpush
@endsection 