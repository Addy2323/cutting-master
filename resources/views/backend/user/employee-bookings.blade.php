@extends('adminlte::page')

@section('title', 'All Appointments')

@section('content_header')
<div class="row ">
    <div class="col-sm-6">
        <h1>My Bookings</h1>
    </div>

</div>
@stop

@section('content')
    <!-- Main content -->

    <!-- Main content -->
    <div class="content pl-md-2 ">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-lg-12 margin-tb">
                    {{-- <div class="pull-left">
                            <h2>Users Management</h2>
                        </div> --}}
                    @can('user-create')
                        <div class="pull-right">
                            <a class="btn btn-success" href="{{ route('employee.create') }}"> Create New Employee</a>
                        </div>
                    @endcan
                </div>
            </div>


            @if (session()->has('success'))
                <div class="alert alert-dismissable alert-success">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <strong>
                        {!! session()->get('success') !!}
                    </strong>
                </div>
            @endif


            <div class="row">
                <div class="col-md-12">
                    <div class="card p-3 shadow">
                        <div id="example_wrapper" class="dataTables_wrapper table-responsive">
                            <table id="example" class="display dataTable table table-striped" style="width: 100%;"
                                aria-describedby="example_info">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th >Phone</th>
                                        <th width="20%">Time</th>
                                        <th width="10%">Date</th>
                                        <th>Employee</th>
                                        <th>Status</th>
                                        <th width="10%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bookings as $booking)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $booking->first_name }} {{ $booking->last_name }}</td>
                                            <td>{{ $booking->email }}</td>
                                            <td>{{ $booking->phone }}</td>
                                            <td>@if($booking->booking_time) {{ $booking->booking_time }} @else NA @endif</td>
                                            <td>{{ $booking->booking_date }}</td>
                                            <td>{{ $booking->employee_name }}</td>


                                            <td>
                                                @if ($booking->payment_status == true)
                                                    <div class="badge badge-success">Paid</div>
                                                @else
                                                    <div class="badge badge-danger">Pending</div>
                                                @endif
                                            </td>

                                            <td class="d-flex">


                                                    <a class="btn btn-primary btn-sm mr-2"
                                                        href="{{ Route('employee.booking.detail',$booking->id) }}">View</a>

                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Status</th>
                                        <th width="280px">Action</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                    </div>
                </div>

            </div>



        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        function DeleteAlert() {
            var confirmed = confirm("Are you sure you want to delete this data?");
            if (confirmed) {
                // code to delete data goes here
                console.log("Data has been deleted.");

            } else {
                addEventListener("click", function(event) {
                    event.preventDefault()
                })
            }
        }
    </script>


    {{-- hide notifcation --}}
    <script>
        $(document).ready(function() {
            $(".alert").delay(6000).slideUp(300);
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#myTable').DataTable();

        });
    </script>

    <script type="text/javascript">
        $("#example").DataTable();
    </script>

@stop
