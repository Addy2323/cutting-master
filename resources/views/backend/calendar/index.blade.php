@extends('backend.layouts.app')
@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Calendar</h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Appointments Calendar</h3>
                        </div>
                        <div class="card-body">
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<style>
    .fc-event {
        cursor: pointer;
    }
    .fc-event.at-home {
        border-left: 4px solid #28a745;
    }
    .fc-event.travel-buffer {
        background-color: #f8f9fa;
        border-left: 4px solid #6c757d;
    }
    .fc-event.travel-buffer .fc-event-title {
        font-style: italic;
    }
</style>
@endpush

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: {
            url: '{{ route("api.appointments.calendar") }}',
            failure: function() {
                alert('There was an error while fetching events!');
            }
        },
        eventDidMount: function(info) {
            // Add tooltip with appointment details
            $(info.el).tooltip({
                title: function() {
                    var event = info.event;
                    var details = [
                        'Client: ' + event.extendedProps.client_name,
                        'Service: ' + event.extendedProps.service_name,
                        'Location: ' + (event.extendedProps.is_at_home ? 'At Home' : 'In Shop')
                    ];
                    
                    if (event.extendedProps.is_at_home) {
                        details.push('Address: ' + event.extendedProps.address);
                    }
                    
                    return details.join('<br>');
                },
                html: true,
                placement: 'top',
                trigger: 'hover',
                container: 'body'
            });
        },
        eventContent: function(arg) {
            var event = arg.event;
            var content = document.createElement('div');
            
            // Add travel buffer time for at-home appointments
            if (event.extendedProps.is_at_home && event.extendedProps.travel_buffer_minutes) {
                var bufferStart = new Date(event.start);
                bufferStart.setMinutes(bufferStart.getMinutes() - event.extendedProps.travel_buffer_minutes);
                
                var bufferEnd = new Date(event.end);
                bufferEnd.setMinutes(bufferEnd.getMinutes() + event.extendedProps.travel_buffer_minutes);
                
                // Add buffer events
                calendar.addEvent({
                    title: 'Travel Time',
                    start: bufferStart,
                    end: event.start,
                    classNames: ['travel-buffer'],
                    display: 'background'
                });
                
                calendar.addEvent({
                    title: 'Travel Time',
                    start: event.end,
                    end: bufferEnd,
                    classNames: ['travel-buffer'],
                    display: 'background'
                });
            }
            
            // Style the main event
            if (event.extendedProps.is_at_home) {
                content.classList.add('at-home');
            }
            
            return {
                domNodes: [content]
            };
        }
    });
    
    calendar.render();
});
</script>
@endpush
@endsection 