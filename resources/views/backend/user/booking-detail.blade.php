@extends('adminlte::page')

@section('title', 'Booking Detail')

@section('content_header')
    <div class="row ">
        <div class="col-sm-6">
            <h1><a href="{{ route('employee.bookings') }}" class="btn btn-sm btn-primary">Back</a></h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('bookings.index.status', ['status' => 0]) }}">Pending Payment
                        ()
                    </a> |</li>
                <li class=""> &nbsp; <a href="{{ route('bookings.index.status', ['status' => 1]) }}">Paid
                        ()</a></li>
            </ol>
        </div>
    </div>
@stop

@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">


                <div class="invoice p-3 mb-3">

                    <div class="row">
                        <div class="col-12">
                            <h4>
                                <i class="fas fa-globe"></i> {{ $setting->bname }}
                                <small class="float-right">Date: {{ $booking->created_at->format('d, M Y') }}</small>
                            </h4>
                        </div>

                    </div>

                    <div class="row invoice-info pb-3">
                        <div class="col-sm-4 invoice-col">
                            From
                            <address>
                                <strong>{{ $setting->bname }}</strong><br>
                                Email: {{ $setting->email }} <br>
                                Phone: {{ $setting->phone }} <br>
                                {{ $setting->address }}

                            </address>
                        </div>

                        <div class="col-sm-4 invoice-col">
                            To
                            <address>
                                <strong>{{ $booking->first_name }} {{ $booking->last_name }}</strong><br>
                                Email: {{ $booking->email }} <br>
                                Phone: {{ $booking->phone }} <br>
                                {{ $booking->city }},
                                {{ $booking->state }},
                                {{ $booking->country }}

                            </address>
                        </div>

                        <div class="col-sm-4 invoice-col">
                            <b>Order ID: </b> #{{ $booking->order_id }}<br>
                            <br>
                            <b>Transaction ID:</b> {{ $booking->transaction_id }}<br>
                            <b>Payment Method :</b> {{ $booking->payment_mode }}<br>
                            @if ($booking->payment_status == true)
                                <b>Status:</b>
                                <div class="badge badge-success">Paid</div>
                            @endif

                        </div>

                    </div>


                    <div class="row">
                        <div class="col-12 table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Gender</th>
                                        <th>Service</th>
                                        <th>Booking Time</th>
                                        <th>Booking Date</th>
                                        <th>Birth Place</th>
                                        <th>Birth Time</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $booking->first_name }} {{ $booking->last_name }}</td>
                                        <td>{{ $booking->phone }}</td>
                                        <td>{{ $booking->gender }}</td>
                                        <td>{{ $booking->service_name }}</td>
                                        <td>{{ $booking->booking_time }}</td>
                                        <td>{{ $booking->booking_date }}</td>
                                        <td>{{ $booking->birth_place }}</td>
                                        <td>{{ $booking->birth_time }}</td>

                                        @if ($booking->payment_mode == 'Razorpay')
                                            <td>{{ Number::currency($booking->amount, 'inr') }}</td>
                                        @endif
                                        @if ($booking->payment_mode == 'Paypal')
                                            <td>{{ Number::currency($booking->amount / 83, 'usd') }}</td>
                                        @endif
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-10">


                            <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                                <b>City: </b>{{ $booking->city }} | <b>State: </b>{{ $booking->state }} | <b>Country:
                                </b>{{ $booking->country }}
                            </p>
                            <p class="lead mb-0"><b>Service Provider</b>: {{ $booking->employee_name }}</p>
                            <p class="lead mb-0"><b>Booking Time</b>: {{ $booking->booking_time }}</p>
                            <p class="lead"><b>Booking Date</b>:
                                {{ $booking->booking_date ? date('d M Y', strtotime($booking->booking_date)) : 'N/A' }}</p>

                        </div>

                        <div class="col-2">

                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>

                                        <tr>
                                            <th>Total:</th>
                                            @if ($booking->payment_mode == 'Razorpay')
                                                <td>{{ Number::currency($booking->amount, 'inr') }}</td>
                                            @endif

                                            @if ($booking->payment_mode == 'Paypal')
                                                <td>{{ Number::currency($booking->amount / 83, 'usd') }}</td>
                                            @endif

                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>


                    <div class="row no-print">
                        <div class="col-12">
                            <a href="#" onclick="window.print()" class="btn btn-default"><i class="fas fa-print"></i> Print</a>




                            {{-- <a href="{{ route('bookings.generate-pdf', ['id' => $booking->id]) }}" class="btn btn-primary float-right" style="margin-right: 5px;"><i class="fas fa-download"></i> Generate PDF</a> --}}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

@stop

@section('js')



@stop

