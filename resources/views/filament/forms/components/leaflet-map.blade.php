<div x-data="leafletMap()" x-init="initMap()" class="w-full">
    <div id="map-{{ $getId() }}" class="w-full h-96 border border-gray-300 rounded-lg"></div>

    <div class="mt-2 text-sm text-gray-600">
        <p>📍 Klik pada peta untuk mengatur lokasi ODP</p>
        <p>🗺️ Seret marker untuk menyesuaikan posisi</p>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .leaflet-container {
        height: 400px;
        width: 100%;
        border-radius: 0.5rem;
    }

    .leaflet-popup-content {
        margin: 8px 12px;
        line-height: 1.4;
    }

    .custom-marker {
        background-color: #3B82F6;
        border: 2px solid white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    function leafletMap() {
        return {
            map: null,
            marker: null,

            initMap() {
                const mapId = 'map-{{ $getId() }}';
                const lat = -6.200000;
                const lng = 106.816666;

                // Initialize map
                this.map = L.map(mapId).setView([lat, lng], 15);

                // Add OpenStreetMap tiles
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                    maxZoom: 19
                }).addTo(this.map);

                // Add initial marker
                this.marker = L.marker([lat, lng], {
                    draggable: true
                }).addTo(this.map);

                this.marker.bindPopup(`
                    <div class="text-center">
                        <strong>Lokasi ODP</strong><br>
                        <small>Lat: ${lat.toFixed(6)}<br>Lng: ${lng.toFixed(6)}</small>
                    </div>
                `);

                // Handle map click
                this.map.on('click', (e) => {
                    this.updateMarker(e.latlng.lat, e.latlng.lng);
                });

                // Handle marker drag
                this.marker.on('dragend', (e) => {
                    const position = e.target.getLatLng();
                    this.updateMarker(position.lat, position.lng);
                });
            },

            updateMarker(lat, lng) {
                // Update marker position
                this.marker.setLatLng([lat, lng]);

                // Update popup content
                this.marker.setPopupContent(`
                    <div class="text-center">
                        <strong>Lokasi ODP</strong><br>
                        <small>Lat: ${lat.toFixed(6)}<br>Lng: ${lng.toFixed(6)}</small>
                    </div>
                `);

                // Update form fields
                this.updateFormFields(lat, lng);
            },

            updateFormFields(lat, lng) {
                // Find and update latitude field
                const latField = document.querySelector('input[name="latitude"]');
                if (latField) {
                    latField.value = lat.toFixed(8);
                    latField.dispatchEvent(new Event('input', {
                        bubbles: true
                    }));
                }

                // Find and update longitude field
                const lngField = document.querySelector('input[name="longitude"]');
                if (lngField) {
                    lngField.value = lng.toFixed(8);
                    lngField.dispatchEvent(new Event('input', {
                        bubbles: true
                    }));
                }
            }
        }
    }
</script>
@endpush