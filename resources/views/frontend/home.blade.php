@extends('frontend.layouts.app')

@section('content')
<!-- Hero Section -->
<section class="hero bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-4 fw-bold">Book Your Appointment Online</h1>
                <p class="lead">Schedule your services with ease. Choose from our wide range of professional services.</p>
                <a href="{{ route('services.index') }}" class="btn btn-light btn-lg">Book Now</a>
            </div>
            <div class="col-md-6">
                <img src="{{ asset('images/hero-image.jpg') }}" alt="Appointment Booking" class="img-fluid rounded">
            </div>
        </div>
    </div>
</section>

<!-- Featured Services Section -->
<section class="featured-services py-5">
    <div class="container">
        <h2 class="text-center mb-4">Featured Services</h2>
        <div class="row">
            @foreach($featuredServices as $service)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    @if($service->image)
                    <img src="{{ asset('storage/' . $service->image) }}" class="card-img-top" alt="{{ $service->title }}">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $service->title }}</h5>
                        <p class="card-text">{{ Str::limit($service->description, 100) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 mb-0">${{ number_format($service->price, 2) }}</span>
                            <a href="{{ route('services.show', $service->slug) }}" class="btn btn-primary">Book Now</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="categories py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-4">Our Categories</h2>
        <div class="row">
            @foreach($categories as $category)
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    @if($category->image)
                    <img src="{{ asset('storage/' . $category->image) }}" class="card-img-top" alt="{{ $category->name }}">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $category->name }}</h5>
                        <p class="card-text">{{ Str::limit($category->description, 50) }}</p>
                        <a href="{{ route('categories.show', $category->slug) }}" class="btn btn-outline-primary">View Services</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="why-choose-us py-5">
    <div class="container">
        <h2 class="text-center mb-4">Why Choose Us</h2>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="text-center">
                    <i class="fas fa-clock fa-3x mb-3 text-primary"></i>
                    <h4>Easy Booking</h4>
                    <p>Book your appointment online in just a few clicks</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="text-center">
                    <i class="fas fa-calendar-check fa-3x mb-3 text-primary"></i>
                    <h4>Flexible Schedule</h4>
                    <p>Choose from multiple time slots that suit you</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="text-center">
                    <i class="fas fa-star fa-3x mb-3 text-primary"></i>
                    <h4>Expert Staff</h4>
                    <p>Our professionals are highly trained and experienced</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection 