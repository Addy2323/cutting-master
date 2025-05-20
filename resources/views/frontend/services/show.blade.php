@extends('frontend.layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Service Details -->
        <div class="col-md-8">
            <div class="card">
                @if($service->image)
                    <img src="{{ asset('storage/' . $service->image) }}" 
                         class="card-img-top" 
                         alt="{{ $service->name }}">
                @endif
                <div class="card-body">
                    <h1 class="card-title">{{ $service->name }}</h1>
                    <p class="text-muted">
                        Category: {{ $service->category->name }}
                    </p>
                    <div class="mb-4">
                        <h4 class="text-primary">{{ number_format($service->price) }} TZS</h4>
                    </div>
                    <div class="mb-4">
                        <h5>Description</h5>
                        <p>{{ $service->description }}</p>
                    </div>
                    <div class="mb-4">
                        <h5>Duration</h5>
                        <p>{{ $service->duration }} minutes</p>
                    </div>
                    <a href="{{ route('booking.create', $service) }}" 
                       class="btn btn-primary btn-lg">Book Now</a>
                </div>
            </div>
        </div>

        <!-- Related Services -->
        <div class="col-md-4">
            <h3 class="mb-4">Related Services</h3>
            @forelse($relatedServices as $relatedService)
                <div class="card mb-3">
                    @if($relatedService->image)
                        <img src="{{ asset('storage/' . $relatedService->image) }}" 
                             class="card-img-top" 
                             alt="{{ $relatedService->name }}">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $relatedService->name }}</h5>
                        <p class="card-text">{{ Str::limit($relatedService->description, 50) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h6 mb-0">{{ number_format($relatedService->price) }} TZS</span>
                            <a href="{{ route('services.show', $relatedService) }}" 
                               class="btn btn-outline-primary btn-sm">View</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-info">
                    No related services found.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection 