@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
@php
     $appointments = Appointment::whereMonth('booking_date', Carbon::now()->month)
            ->whereYear('booking_date', Carbon::now()->year)
            ->where('status', 'Confirmed') // Only show confirmed appointments
            ->get();
@endphp
    <p>Welcome to this beautiful admin panel.</p>
    <div id="calendar"></div>

<script>
    $(document).ready(function() {
        $('#calendar').fullCalendar({
            events: [
                @foreach($appointments as $appointment)
                    {
                        title: '{{ $appointment->name }} - {{ $appointment->service->name }}',  // Appointment Name and Service
                        start: '{{ $appointment->booking_date }}T{{ \Carbon\Carbon::createFromFormat('g:i A', trim(explode(' - ', $appointment->booking_time)[0]))->format('H:i') }}',
                        end: '{{ $appointment->booking_date }}T{{ \Carbon\Carbon::createFromFormat('g:i A', trim(explode(' - ', $appointment->booking_time)[1]))->format('H:i') }}',
                        description: '{{ $appointment->notes }}', // Description (Optional)
                        color: 'green', // Color for confirmed appointments
                    },
                @endforeach
            ],
            // Optional: Customization for the calendar
            eventClick: function(event) {
                // If you want to show more info when clicking on an event, you can do so here.
                alert(event.title);  // Display event title (name + service)
            },
        });
    });
</script>

@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    <!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@3.2.0/dist/fullcalendar.css" rel="stylesheet">

@stop

@section('js')
    <!-- jQuery and FullCalendar JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@3.2.0/dist/fullcalendar.min.js"></script>
@stop
