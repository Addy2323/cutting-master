<div class="container">
    <h1 class="text-2xl font-bold mb-4">Categories</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($categories as $category)
            <div class="bg-white p-4 rounded shadow">
                <h2 class="text-xl font-semibold">{{ $category->title }}</h2>
                <p>{{ $category->body }}</p>
            </div>
        @endforeach
    </div>
</div> 