<div class="form-group">
    <label for="address">Address</label>
    <div class="input-group">
        <input type="text" class="form-control" id="address" name="address" required>
        <div class="input-group-append">
            <button type="button" class="btn btn-primary" onclick="getCurrentLocation()">
                <i class="fas fa-location-arrow"></i> Get Current Location
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                // Get coordinates
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                // Use OpenStreetMap Nominatim API to get address
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.display_name) {
                            document.getElementById('address').value = data.display_name;
                        }
                    })
                    .catch(error => {
                        console.error('Error getting address:', error);
                        alert('Could not get address from coordinates');
                    });
            },
            function(error) {
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        alert('Please allow location access to get your current address');
                        break;
                    case error.POSITION_UNAVAILABLE:
                        alert('Location information is unavailable');
                        break;
                    case error.TIMEOUT:
                        alert('Request to get location timed out');
                        break;
                    default:
                        alert('An unknown error occurred');
                        break;
                }
            }
        );
    } else {
        alert('Geolocation is not supported by your browser');
    }
}
</script>
@endpush 