@extends('frontend.layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Categories Sidebar -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Categories</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach($categories as $category)
                            <li class="list-group-item">
                                <a href="{{ route('services.index', ['category' => $category->id]) }}" 
                                   class="text-decoration-none {{ request('category') == $category->id ? 'fw-bold' : '' }}">
                                    {{ $category->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Services List -->
        <div class="col-md-9">
            <div class="row">
                @forelse($services as $service)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            @if($service->image)
                                <img src="{{ asset('storage/' . $service->image) }}" 
                                     class="card-img-top" 
                                     alt="{{ $service->name }}">
                            @endif
                            <div class="card-body">
                                <h5 class="card-title">{{ $service->name }}</h5>
                                <p class="card-text">{{ Str::limit($service->description, 100) }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="h5 mb-0">{{ number_format($service->price) }} TZS</span>
                                    <a href="{{ route('services.show', $service) }}" 
                                       class="btn btn-primary">View Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info">
                            No services found.
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $services->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 