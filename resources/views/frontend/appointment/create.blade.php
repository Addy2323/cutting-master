@extends('frontend.layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Book an Appointment</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('appointment.store') }}" id="appointmentForm">
                        @csrf

                        <div class="form-group row mb-3">
                            <label for="service_id" class="col-md-4 col-form-label text-md-right">Service</label>
                            <div class="col-md-6">
                                <select class="form-control @error('service_id') is-invalid @enderror" id="service_id" name="service_id" required>
                                    <option value="">Select Service</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}" 
                                            data-type="{{ $service->service_type }}"
                                            data-price="{{ $service->price }}"
                                            data-travel-fee="{{ $service->travel_fee }}">
                                            {{ $service->name }} - ${{ number_format($service->price, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('service_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="employee_id" class="col-md-4 col-form-label text-md-right">Professional</label>
                            <div class="col-md-6">
                                <select class="form-control @error('employee_id') is-invalid @enderror" id="employee_id" name="employee_id" required>
                                    <option value="">Select Professional</option>
                                </select>
                                @error('employee_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="date" class="col-md-4 col-form-label text-md-right">Date</label>
                            <div class="col-md-6">
                                <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" required min="{{ date('Y-m-d') }}">
                                @error('date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="time_slot" class="col-md-4 col-form-label text-md-right">Time Slot</label>
                            <div class="col-md-6">
                                <select class="form-control @error('time_slot') is-invalid @enderror" id="time_slot" name="time_slot" required>
                                    <option value="">Select Time Slot</option>
                                </select>
                                @error('time_slot')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="service_location" class="col-md-4 col-form-label text-md-right">Service Location</label>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_at_home" id="in_shop" value="0" checked>
                                    <label class="form-check-label" for="in_shop">
                                        In-Shop
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_at_home" id="at_home" value="1">
                                    <label class="form-check-label" for="at_home">
                                        At-Home
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div id="address_fields" style="display: none;">
                            <div class="form-group row mb-3">
                                <label for="address" class="col-md-4 col-form-label text-md-right">Address</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address">
                                    @error('address')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="city" class="col-md-4 col-form-label text-md-right">City</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city">
                                    @error('city')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="postal_code" class="col-md-4 col-form-label text-md-right">Postal Code</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control @error('postal_code') is-invalid @enderror" id="postal_code" name="postal_code">
                                    @error('postal_code')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="notes" class="col-md-4 col-form-label text-md-right">Notes</label>
                            <div class="col-md-6">
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3"></textarea>
                                @error('notes')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <div class="alert alert-info">
                                    <strong>Total Price:</strong> $<span id="total_price">0.00</span>
                                    <span id="travel_fee_note" style="display: none;"> (includes travel fee)</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Book Appointment
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Handle service type selection
    $('#service_id').change(function() {
        var selectedOption = $(this).find('option:selected');
        var serviceType = selectedOption.data('type');
        var price = parseFloat(selectedOption.data('price'));
        var travelFee = parseFloat(selectedOption.data('travel-fee')) || 0;

        // Update total price
        updateTotalPrice(price, travelFee);

        // Show/hide at-home option based on service type
        if (serviceType === 'at_home' || serviceType === 'both') {
            $('#at_home').prop('disabled', false);
        } else {
            $('#at_home').prop('disabled', true);
            $('#in_shop').prop('checked', true);
            $('#address_fields').hide();
        }

        // Load available employees for this service
        loadEmployees($(this).val());
    });

    // Handle service location selection
    $('input[name="is_at_home"]').change(function() {
        if ($(this).val() === '1') {
            $('#address_fields').show();
            $('#address, #city, #postal_code').prop('required', true);
        } else {
            $('#address_fields').hide();
            $('#address, #city, #postal_code').prop('required', false);
        }
    });

    // Handle date selection
    $('#date').change(function() {
        loadTimeSlots($('#service_id').val(), $(this).val(), $('#employee_id').val());
    });

    // Handle employee selection
    $('#employee_id').change(function() {
        loadTimeSlots($('#service_id').val(), $('#date').val(), $(this).val());
    });

    function updateTotalPrice(price, travelFee) {
        var total = price;
        if ($('#at_home').is(':checked')) {
            total += travelFee;
            $('#travel_fee_note').show();
        } else {
            $('#travel_fee_note').hide();
        }
        $('#total_price').text(total.toFixed(2));
    }

    function loadEmployees(serviceId) {
        if (!serviceId) return;

        $.get(`/api/services/${serviceId}/employees`, function(data) {
            var select = $('#employee_id');
            select.empty().append('<option value="">Select Professional</option>');
            
            data.forEach(function(employee) {
                select.append(`<option value="${employee.id}">${employee.name}</option>`);
            });
        });
    }

    function loadTimeSlots(serviceId, date, employeeId) {
        if (!serviceId || !date || !employeeId) return;

        $.get(`/api/employees/${employeeId}/available-slots`, {
            service_id: serviceId,
            date: date
        }, function(data) {
            var select = $('#time_slot');
            select.empty().append('<option value="">Select Time Slot</option>');
            
            data.forEach(function(slot) {
                select.append(`<option value="${slot.value}">${slot.label}</option>`);
            });
        });
    }

    // Form submission
    $('#appointmentForm').submit(function(e) {
        e.preventDefault();
        
        // Validate address fields if at-home service
        if ($('#at_home').is(':checked')) {
            if (!$('#address').val() || !$('#city').val() || !$('#postal_code').val()) {
                alert('Please fill in all address fields for at-home service.');
                return;
            }
        }

        // Submit form
        this.submit();
    });
});
</script>
@endpush
@endsection 