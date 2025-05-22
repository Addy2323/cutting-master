@extends('adminlte::page')

@section('title', 'Notifications')

@section('content_header')
    <h1>Notifications</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Your Notifications</h3>
            @if($notifications->where('is_read', false)->count() > 0)
                <div class="card-tools">
                    <form action="{{ route('notifications.markAllAsRead') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-info">
                            Mark All as Read
                        </button>
                    </form>
                </div>
            @endif
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notifications as $notification)
                            <tr>
                                <td>{{ $notification->title }}</td>
                                <td>{{ $notification->message }}</td>
                                <td>{{ $notification->created_at->diffForHumans() }}</td>
                                <td>
                                    @if($notification->is_read)
                                        <span class="badge badge-success">Read</span>
                                    @else
                                        <span class="badge badge-warning">Unread</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!$notification->is_read)
                                        <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-info">
                                                Mark as Read
                                            </button>
                                        </form>
                                    @endif
                                    @if($notification->link)
                                        <a href="{{ $notification->link }}" class="btn btn-sm btn-primary">
                                            View
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No notifications found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($notifications->hasPages())
            <div class="card-footer">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
@stop 