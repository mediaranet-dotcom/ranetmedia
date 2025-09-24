<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            🗺️ Peta Lokasi ODP
        </x-slot>

        <x-slot name="description">
            Peta interaktif menampilkan semua lokasi ODP dengan status dan utilisasi real-time
        </x-slot>

        <div x-data="odpMap()" x-init="initMap()" class="w-full">
            <div id="odp-map" class="w-full h-96 border border-gray-300 rounded-lg"></div>

            <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div class="flex items-center space-x-2">
                    <div class="w-4 h-4 bg-green-500 rounded-full"></div>
                    <span>ODP Aktif</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-4 h-4 bg-yellow-500 rounded-full"></div>
                    <span>Pemeliharaan</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-4 h-4 bg-red-500 rounded-full"></div>
                    <span>Rusak</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-4 h-4 bg-gray-500 rounded-full"></div>
                    <span>Tidak Aktif</span>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

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
        min-width: 250px;
    }

    .odp-popup {
        font-family: system-ui, -apple-system, sans-serif;
    }

    .odp-popup h3 {
        margin: 0 0 8px 0;
        font-size: 16px;
        font-weight: 600;
        color: #1f2937;
    }

    .odp-popup .status-badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 500;
        text-transform: uppercase;
    }

    .odp-popup .status-active {
        background: #dcfce7;
        color: #166534;
    }

    .odp-popup .status-maintenance {
        background: #fef3c7;
        color: #92400e;
    }

    .odp-popup .status-damaged {
        background: #fee2e2;
        color: #991b1b;
    }

    .odp-popup .status-inactive {
        background: #f3f4f6;
        color: #374151;
    }

    .odp-popup .info-row {
        display: flex;
        justify-content: space-between;
        margin: 4px 0;
        font-size: 13px;
    }

    .odp-popup .info-label {
        color: #6b7280;
        font-weight: 500;
    }

    .odp-popup .info-value {
        color: #1f2937;
        font-weight: 600;
    }

    .odp-popup .edit-link {
        display: inline-block;
        margin-top: 8px;
        padding: 4px 12px;
        background: #3b82f6;
        color: white;
        text-decoration: none;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
    }

    .odp-popup .edit-link:hover {
        background: #2563eb;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    function odpMap() {
        return {
            map: null,
            locations: @json($locations),

            initMap() {
                // Initialize map
                this.map = L.map('odp-map').setView([-6.200000, 106.816666], 12);

                // Add OpenStreetMap tiles
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                    maxZoom: 19
                }).addTo(this.map);

                // Add markers for each ODP
                this.locations.forEach(location => {
                    this.addOdpMarker(location);
                });

                // Fit map to show all markers if there are any
                if (this.locations.length > 0) {
                    const group = new L.featureGroup(this.map._layers);
                    if (Object.keys(group._layers).length > 0) {
                        this.map.fitBounds(group.getBounds().pad(0.1));
                    }
                }
            },

            addOdpMarker(location) {
                // Create custom marker icon
                const markerIcon = L.divIcon({
                    className: 'custom-div-icon',
                    html: `
                    <div style="
                        background-color: ${location.statusColor};
                        border: 2px solid white;
                        border-radius: 50%;
                        width: 30px;
                        height: 30px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: white;
                        font-weight: bold;
                        font-size: 12px;
                        box-shadow: 0 2px 4px rgba(0,0,0,0.3);
                    ">
                        ${location.used_ports}
                    </div>
                `,
                    iconSize: [30, 30],
                    iconAnchor: [15, 15]
                });

                // Create marker
                const marker = L.marker([location.lat, location.lng], {
                    icon: markerIcon
                }).addTo(this.map);

                // Create popup content
                const popupContent = this.createPopupContent(location);

                // Bind popup
                marker.bindPopup(popupContent, {
                    maxWidth: 300,
                    className: 'odp-popup'
                });
            },

            createPopupContent(location) {
                const utilizationColor = location.utilization > 80 ? '#ef4444' :
                    (location.utilization > 60 ? '#f59e0b' : '#10b981');

                // Create customers list HTML
                let customersHtml = '';
                if (location.customers && location.customers.length > 0) {
                    customersHtml = `
                        <div style="margin-top: 12px; padding-top: 8px; border-top: 1px solid #e5e7eb;">
                            <div style="font-weight: 600; margin-bottom: 8px; color: #1f2937;">
                                👥 Connected Customers (${location.customers.length})
                            </div>
                            <div style="max-height: 200px; overflow-y: auto;">
                                ${location.customers.map(customer => `
                                    <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 8px; margin-bottom: 6px; font-size: 12px;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                            <div>
                                                <div style="color: #3b82f6; font-weight: bold; font-size: 11px;">${customer.customer_number}</div>
                                                <strong style="color: #1f2937;">${customer.name}</strong>
                                            </div>
                                            <span style="background: ${customer.status === 'active' ? '#10b981' : '#ef4444'}; color: white; padding: 2px 6px; border-radius: 12px; font-size: 10px;">
                                                ${customer.status}
                                            </span>
                                        </div>
                                        <div style="color: #6b7280; margin-bottom: 2px;">📞 ${customer.phone}</div>
                                        <div style="color: #6b7280; margin-bottom: 2px;">📦 ${customer.package}</div>
                                        <div style="display: flex; justify-content: space-between; font-size: 11px;">
                                            <span>🔌 Port ${customer.port || 'N/A'}</span>
                                            ${customer.fiber_color ? `<span>🎨 ${customer.fiber_color}</span>` : ''}
                                            ${customer.signal_strength ? `<span>📶 ${customer.signal_strength} dBm</span>` : ''}
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    `;
                } else {
                    customersHtml = `
                        <div style="margin-top: 12px; padding-top: 8px; border-top: 1px solid #e5e7eb;">
                            <div style="color: #6b7280; font-style: italic; text-align: center;">
                                No customers connected
                            </div>
                        </div>
                    `;
                }

                return `
                <div class="odp-popup">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <h3 style="margin: 0; color: #1f2937;">${location.name}</h3>
                        <span class="status-badge status-${location.status}" style="background: ${location.statusColor}; color: white; padding: 4px 8px; border-radius: 12px; font-size: 11px; text-transform: uppercase;">
                            ${location.status}
                        </span>
                    </div>

                    <div class="info-row" style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span class="info-label" style="color: #6b7280;">Code:</span>
                        <span class="info-value" style="color: #1f2937; font-weight: 500;">${location.code}</span>
                    </div>

                    <div class="info-row" style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span class="info-label" style="color: #6b7280;">Type:</span>
                        <span class="info-value" style="color: #1f2937; font-weight: 500;">${location.odp_type}</span>
                    </div>

                    <div class="info-row" style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span class="info-label" style="color: #6b7280;">Port Usage:</span>
                        <span class="info-value" style="color: ${utilizationColor}; font-weight: 600;">
                            ${location.port_usage} (${location.utilization}%)
                        </span>
                    </div>

                    <div class="info-row" style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <span class="info-label" style="color: #6b7280;">Condition:</span>
                        <span class="info-value" style="color: #1f2937; font-weight: 500;">${location.condition}</span>
                    </div>

                    <div style="margin: 8px 0; padding-top: 8px; border-top: 1px solid #e5e7eb;">
                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">📍 Location:</div>
                        <div style="font-size: 13px; color: #1f2937;">${location.address}</div>
                        <div style="font-size: 11px; color: #9ca3af;">${location.area}, ${location.district}</div>
                    </div>

                    ${customersHtml}

                    <div style="margin-top: 12px; padding-top: 8px; border-top: 1px solid #e5e7eb; text-align: center;">
                        <a href="/admin/odps/${location.id}/edit" style="display: inline-block; background: #3b82f6; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px;">
                            ✏️ Edit ODP
                        </a>
                    </div>
                </div>
            `;
            }
        }
    }
</script>
@endpush