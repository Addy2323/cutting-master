<div class="container">
    <h1 class="text-2xl font-bold mb-4">Services</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($services as $service)
            <div class="bg-white p-4 rounded shadow">
                <h2 class="text-xl font-semibold">{{ $service->title }}</h2>
                <p>{{ $service->body }}</p>
            </div>
        @endforeach
    </div>
</div> 