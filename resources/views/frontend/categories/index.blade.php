@extends('frontend.layouts.app')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">Service Categories</h1>
    
    <div class="row">
        @forelse($categories as $category)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    @if($category->image)
                        <img src="{{ asset('uploads/images/category/' . $category->image) }}" 
                             class="card-img-top" 
                             alt="{{ $category->title }}">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $category->title }}</h5>
                        <p class="card-text">{{ Str::limit($category->body, 100) }}</p>
                        <a href="{{ route('get.services', $category) }}" 
                           class="btn btn-primary">View Services</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    No categories found.
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $categories->links() }}
    </div>
</div>
@endsection 