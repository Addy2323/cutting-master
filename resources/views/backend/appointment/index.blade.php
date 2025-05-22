@extends('adminlte::page')

@section('title', 'All Appointments')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>All Apointments</h1>
        </div>

    </div>
@stop

@section('content')
    <!-- Modal -->
    <form id="appointmentStatusForm" method="POST" action="{{ route('appointments.update.status') }}">
        @csrf
        <input type="hidden" name="appointment_id" id="modalAppointmentId">

        <div class="modal fade" id="appointmentModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Appointment Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <p><strong>Client:</strong> <span id="modalAppointmentName">N/A</span></p>
                        <p><strong>Service:</strong> <span id="modalService">N/A</span></p>
                        <p><strong>Email:</strong> <span id="modalEmail">N/A</span></p>
                        <p><strong>Phone:</strong> <span id="modalPhone">N/A</span></p>
                        <p><strong>Staff:</strong> <span id="modalStaff">N/A</span></p>
                        <p><strong>Start:</strong> <span id="modalStartTime">N/A</span></p>
                        <p><strong>Amount:</strong> <span id="modalAmount">N/A</span></p>
                        <p><strong>Notes:</strong> <span id="modalNotes">N/A</span></p>
                        <p><strong>Current Status:</strong> <span id="modalStatusBadge">N/A</span></p>


                        <div class="form-group ">
                            <label><strong>Status:</strong></label>
                            <select name="status" class="form-control" id="modalStatusSelect">
                                <option value="Pending payment">Pending payment</option>
                                <option value="Processing">Processing</option>
                                <option value="Confirmed">Confirmed</option>
                                <option value="Cancelled">Cancelled</option>
                                <option value="Completed">Completed</option>
                                <option value="On Hold">On Hold</option>
                                {{-- <option value="Rescheduled">Rescheduled</option> --}}
                                <option value="No Show">No Show</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" onclick="return confirm('Are you sure you want to update booking status?')"
                            class="btn btn-danger">Update Status</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
        </div>
    </form>
    <div class="">
        @if (session('success'))
            <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>{{ session('success') }}</strong>
            </div>
        @endif
        <!-- Content Header (Page header) -->
        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card py-2 px-2">

                            <div class="card-body p-0">
                                <table id="myTable" class="table table-striped projects ">
                                    <thead>
                                        <tr>
                                            <th style="width: 1%">
                                                #
                                            </th>
                                            <th style="width: 15%">
                                                User
                                            </th>
                                            <th style="width: 15%">
                                                Email
                                            </th>
                                            <th style="width: 10%">
                                                Phone
                                            </th>
                                            <th style="width: 10%">
                                                Staff
                                            </th>


                                            <th style="width: 10%">
                                                Service
                                            </th>
                                            <th style="width: 10%">
                                                Date
                                            </th>
                                            <th style="width: 10%">
                                                Time
                                            </th>


                                            <th style="width: 15%" class="text-center">
                                                Status
                                            </th>
                                            <th style="width: 18%">
                                                Action
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $statusColors = [
                                                'Pending payment' => '#f39c12',
                                                'Processing' => '#3498db',
                                                'Confirmed' => '#2ecc71',
                                                'Cancelled' => '#ff0000',
                                                'Completed' => '#008000',
                                                'On Hold' => '#95a5a6',
                                                'Rescheduled' => '#f1c40f',
                                                'No Show' => '#e67e22',
                                            ];
                                        @endphp
                                        @foreach ($appointments as $appointment)
                                            <tr>
                                                <td>
                                                    {{ $loop->iteration }}
                                                </td>
                                                <td>
                                                    <a>
                                                        {{ $appointment->name }}
                                                    </a>
                                                    <br>
                                                    <small>
                                                        {{ $appointment->created_at->format('d M Y') }}
                                                    </small>
                                                </td>
                                                <td>
                                                    {{ $appointment->email }}
                                                </td>
                                                <td>
                                                    {{ $appointment->phone }}
                                                </td>
                                                <td>
                                                    {{ $appointment->employee->user->name }}
                                                </td>

                                                <td>
                                                    {{ $appointment->service->title ?? 'NA' }}
                                                </td>
                                                <td>
                                                    {{ $appointment->booking_date }}
                                                </td>
                                                <td>
                                                    {{ $appointment->booking_time }}
                                                </td>
                                                <td>
                                                    @php
                                                        $status = $appointment->status;
                                                        $color = $statusColors[$status] ?? '#7f8c8d';
                                                    @endphp
                                                    <span class="badge px-2 py-1"
                                                        style="background-color: {{ $color }}; color: white;">
                                                        {{ $status }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-primary btn-sm py-0 px-1 view-appointment-btn"
                                                        data-toggle="modal" data-target="#appointmentModal"
                                                        data-id="{{ $appointment->id }}"
                                                        data-name="{{ $appointment->name }}"
                                                        data-service="{{ $appointment->service->title ?? 'MA' }}"
                                                        data-email="{{ $appointment->email }}"
                                                        data-phone="{{ $appointment->phone }}"
                                                        data-employee="{{ $appointment->employee->user->name }}"
                                                        data-start="{{ $appointment->booking_date . ' ' . $appointment->booking_time }}"
                                                        data-amount="{{ $appointment->amount }}"
                                                        data-notes="{{ $appointment->notes }}"
                                                        data-status="{{ $appointment->status }}">View</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                            </div>
                            <!-- /.card-body -->
                        </div>
                    </div>
                    <!-- /.col -->

                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
    </div>
@stop

@section('css')

@stop

@section('js')

    {{-- hide notifcation --}}
    <script>
        $(document).ready(function() {
            $(".alert").delay(6000).slideUp(300);
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#myTable').DataTable({
                responsive: true
            });

        });
    </script>



    <script>
        $(document).on('click', '.view-appointment-btn', function() {
            // Set modal fields
            $('#modalAppointmentId').val($(this).data('id'));
            $('#modalAppointmentName').text($(this).data('name'));
            $('#modalService').text($(this).data('service'));
            $('#modalEmail').text($(this).data('email'));
            $('#modalPhone').text($(this).data('phone'));
            $('#modalStaff').text($(this).data('employee'));
            $('#modalStartTime').text($(this).data('start'));
            const amount = $(this).data('amount');
            $('#modalAmount').text(amount ? 'TZS ' + new Intl.NumberFormat().format(amount) : 'N/A');
            $('#modalNotes').text($(this).data('notes'));

            // Set status select dropdown
            var status = $(this).data('status');
            $('#modalStatusSelect').val(status);

            // Set status badge
            var statusColors = {
                'Pending payment': '#f39c12',
                'Processing': '#3498db',
                'Confirmed': '#2ecc71',
                'Cancelled': '#ff0000',
                'Completed': '#008000',
                'On Hold': '#95a5a6',
                'Rescheduled': '#f1c40f',
                'No Show': '#e67e22',
            };

            var badgeColor = statusColors[status] || '#7f8c8d';
            $('#modalStatusBadge').html(
                `<span class="badge px-2 py-1" style="background-color: ${badgeColor}; color: white;">${status}</span>`
            );
        });
    </script>
@endsection
