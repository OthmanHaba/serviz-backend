<x-filament-widgets::widget>
    <x-filament::section>
        <div
            x-data="{
                providers: @js($providers),
                center: @js($center),
                map: null,
                markers: [],
                initMap() {
                    this.map = new google.maps.Map(this.$refs.map, {
                        center: this.center,
                        zoom: 12,
                        styles: [
                            {
                                featureType: 'poi',
                                elementType: 'labels',
                                stylers: [{ visibility: 'off' }]
                            }
                        ]
                    });

                    this.providers.forEach(provider => {
                        const marker = new google.maps.Marker({
                            position: { lat: provider.lat, lng: provider.lng },
                            map: this.map,
                            title: provider.name,
                            icon: {
                                url: this.getMarkerIcon(provider.type),
                                scaledSize: new google.maps.Size(32, 32)
                            }
                        });

                        const infoWindow = new google.maps.InfoWindow({
                            content: `
                                <div class="p-2">
                                    <h3 class="font-bold">${provider.name}</h3>
                                    <p class="text-sm">${this.getProviderType(provider.type)}</p>
                                </div>
                            `
                        });

                        marker.addListener('click', () => {
                            infoWindow.open(this.map, marker);
                        });

                        this.markers.push(marker);
                    });
                },
                getMarkerIcon(type) {
                    const icons = {
                        tow_truck: '/images/tow-truck-marker.png',
                        mechanic: '/images/mechanic-marker.png',
                        gas_delivery: '/images/gas-marker.png'
                    };
                    return icons[type] || icons.tow_truck;
                },
                getProviderType(type) {
                    const types = {
                        tow_truck: 'Tow Truck',
                        mechanic: 'Mechanic',
                        gas_delivery: 'Gas Delivery'
                    };
                    return types[type] || type;
                }
            }"
            x-init="initMap"
            wire:poll.15s="$refresh"
        >
            <div
                x-ref="map"
                class="w-full h-[400px] rounded-lg"
            ></div>
        </div>
    </x-filament::section>

    @push('scripts')
        <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places"></script>
    @endpush
</x-filament-widgets::widget> 