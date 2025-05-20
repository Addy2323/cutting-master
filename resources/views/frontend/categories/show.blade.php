@extends('frontend.layouts.app')

@section('content')
<div class="container py-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $category->title }}</li>
        </ol>
    </nav>

    <div class="row mb-4">
        <div class="col-md-8">
            <h1>{{ $category->title }}</h1>
            <p class="lead">{{ $category->body }}</p>
        </div>
    </div>

    <h2 class="mb-4">Services in this Category</h2>
    <div class="row">
        @forelse($services as $service)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    @if($service->image)
                        <img src="{{ asset('uploads/images/service/' . $service->image) }}" 
                             class="card-img-top" 
                             alt="{{ $service->title }}">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $service->title }}</h5>
                        <p class="card-text">{{ Str::limit($service->excerpt, 100) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 mb-0">{{ number_format($service->price) }} TZS</span>
                            <a href="{{ route('booking.create', $service) }}" 
                               class="btn btn-primary">Book Now</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    No services found in this category.
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $services->links() }}
    </div>
</div>
@endsection 