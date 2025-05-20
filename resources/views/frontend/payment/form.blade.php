@extends('frontend.layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Payment Details</h4>
                </div>
                <div class="card-body">
                    <!-- Appointment Summary -->
                    <div class="booking-summary mb-4">
                        <h5 class="border-bottom pb-2">Booking Summary</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Service:</strong> {{ $appointment->service->title }}</p>
                                <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($appointment->booking_date)->format('F d, Y') }}</p>
                                <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($appointment->booking_time)->format('h:i A') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Staff:</strong> {{ $appointment->employee->user->name }}</p>
                                <p><strong>Amount:</strong> TZS {{ number_format($appointment->amount, 2) }}</p>
                                <p><strong>Reference:</strong> {{ $appointment->booking_id }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Form -->
                    <form id="payment-form" action="{{ route('payment.process') }}" method="POST">
                        @csrf
                        <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">
                        <input type="hidden" name="payment_method" id="payment_method" value="">
                        
                        <!-- Payment Methods -->
                        <div class="payment-methods mb-4">
                            <h5 class="border-bottom pb-2">Select Payment Method</h5>
                            
                            <!-- Mobile Money Options -->
                            <div class="payment-section mb-4">
                                <h6 class="mb-3">Mobile Money</h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="payment-option" data-method="mpesa">
                                            <i class="fas fa-mobile-alt fa-2x mb-2 text-success"></i>
                                            <p class="text-center mb-0">M-Pesa</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="payment-option" data-method="tigo">
                                            <i class="fas fa-mobile-alt fa-2x mb-2 text-primary"></i>
                                            <p class="text-center mb-0">Tigo Pesa</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="payment-option" data-method="airtel">
                                            <i class="fas fa-mobile-alt fa-2x mb-2 text-danger"></i>
                                            <p class="text-center mb-0">Airtel Money</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="payment-option" data-method="halotel">
                                            <i class="fas fa-mobile-alt fa-2x mb-2 text-info"></i>
                                            <p class="text-center mb-0">Halotel</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="payment-option" data-method="yaxmix">
                                            <i class="fas fa-mobile-alt fa-2x mb-2 text-warning"></i>
                                            <p class="text-center mb-0">Yax-Mix</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Other Payment Methods -->
                            <div class="payment-section">
                                <h6 class="mb-3">Other Payment Methods</h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="payment-option" data-method="card">
                                            <i class="fas fa-credit-card fa-2x mb-2 text-primary"></i>
                                            <p class="text-center mb-0">Credit Card</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="payment-option" data-method="bank">
                                            <i class="fas fa-university fa-2x mb-2 text-info"></i>
                                            <p class="text-center mb-0">Bank Transfer</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="payment-option" data-method="cash">
                                            <i class="fas fa-money-bill-wave fa-2x mb-2 text-success"></i>
                                            <p class="text-center mb-0">Cash</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Phone Number Input (for mobile money) -->
                        <div id="phone-input" class="mb-4" style="display: none;">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" placeholder="Enter your phone number">
                        </div>

                        <!-- Submit Button -->
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg">
                                Pay TZS {{ number_format($appointment->amount, 2) }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .payment-option {
        border: 2px solid #dee2e6;
        border-radius: 8px;
        padding: 20px 15px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background-color: white;
    }

    .payment-option:hover {
        border-color: #0d6efd;
        background-color: #f8f9fa;
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .payment-option.selected {
        border-color: #0d6efd;
        background-color: #e7f1ff;
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .payment-option i {
        display: block;
        margin: 0 auto 10px;
        transition: all 0.3s ease;
    }

    .payment-option:hover i {
        transform: scale(1.1);
    }

    .payment-option p {
        font-weight: 500;
        margin: 0;
    }

    .payment-section {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .payment-section h6 {
        color: #495057;
        font-weight: 600;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Handle payment method selection
        $('.payment-option').click(function() {
            $('.payment-option').removeClass('selected');
            $(this).addClass('selected');
            
            const method = $(this).data('method');
            $('#payment_method').val(method);
            
            // Show/hide phone input for mobile money
            if (['mpesa', 'tigo', 'airtel', 'halotel', 'yaxmix'].includes(method)) {
                $('#phone-input').show();
            } else {
                $('#phone-input').hide();
            }
        });

        // Handle form submission
        $('#payment-form').submit(function(e) {
            e.preventDefault();
            
            const method = $('#payment_method').val();
            if (!method) {
                alert('Please select a payment method');
                return;
            }

            // For mobile money, validate phone number
            if (['mpesa', 'tigo', 'airtel', 'halotel', 'yaxmix'].includes(method)) {
                const phone = $('#phone').val();
                if (!phone) {
                    alert('Please enter your phone number');
                    return;
                }
            }

            // Submit the form
            this.submit();
        });
    });
</script>
@endpush
@endsection 