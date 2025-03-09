<x-filament::widget>
    <x-filament::section>
        <x-slot name="heading">Active Providers Map</x-slot>

        <div>
            <div id="mapLoading" class="p-4 text-center">Loading map...</div>
            <div id="mapError" class="p-4 text-center text-red-500 hidden"></div>

            <div wire:ignore x-data="mapComponent()" x-init="$nextTick(() => initMap())">
                <div x-ref="mapContainer" style="height: 500px; width: 100%;"></div>
            </div>
        </div>

        <x-slot name="footer">
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-500">
                    Showing {{ count($providers) }} active providers
                </span>
                <x-filament::button wire:click="refreshProviders" icon="heroicon-o-arrow-path">
                    Refresh Map
                </x-filament::button>
            </div>
        </x-slot>
    </x-filament::section>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

    <script>
        window.mapComponent = function () {
            return {
                providers: @json($providers),
                center: @json($center),
                map: null,
                markers: [],

                initMap() {
                    try {
                        document.getElementById('mapLoading').style.display = 'none';

                        const mapCenter = this.center?.lat && this.center?.lng
                            ? [this.center.lat, this.center.lng]
                            : [0, 0];

                        this.map = L.map(this.$refs.mapContainer).setView(mapCenter, this.providers.length > 0 ? 10 : 2);

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                        }).addTo(this.map);

                        this.loadMarkers();
                    } catch (error) {
                        this.showError(error);
                    }
                },

                loadMarkers() {
                    this.clearMarkers();
                    this.providers.forEach(provider => {
                        if (provider.currentLocation) {
                            this.addMarker(
                                [parseFloat(provider.currentLocation.latitude), parseFloat(provider.currentLocation.longitude)],
                                provider.name || 'Provider',
                                provider.id
                            );
                        }
                    });
                },

                addMarker(position, title, providerId) {
                    const marker = L.marker(position).addTo(this.map);
                    marker.bindPopup(`
                        <div>
                            <h3 style="font-weight: bold;">${title}</h3>
                            <p>Provider ID: ${providerId}</p>
                            <p>Latitude: ${position[0]}</p>
                            <p>Longitude: ${position[1]}</p>
                        </div>
                    `);
                    this.markers.push(marker);
                },

                clearMarkers() {
                    this.markers.forEach(marker => this.map.removeLayer(marker));
                    this.markers = [];
                },

                showError(error) {
                    document.getElementById('mapError').textContent = `Error loading map: ${error.message}`;
                    document.getElementById('mapError').classList.remove('hidden');
                    document.getElementById('mapLoading').classList.add('hidden');
                }
            };
        };

        // Ensure initMap is accessible
        window.initMap = function () {
            document.querySelector('[x-data="mapComponent()"]').__x.$data.initMap();
        };
    </script>
</x-filament::widget>
