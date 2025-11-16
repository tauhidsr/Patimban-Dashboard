{{-- resources/views/map/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Peta Sebaran Penduduk Desa Patimban') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="p-0 overflow-hidden bg-white shadow-sm sm:rounded-lg">

                {{-- Info jumlah wilayah --}}
                <div class="p-4 bg-white border-b">
                    <p class="text-sm text-gray-700">
                        Menampilkan <strong>{{ count($areas) }}</strong> wilayah dengan koordinat.
                    </p>
                </div>

                <div id="map" style="height: 520px;"></div>
            </div>
        </div>
    </div>

    {{-- Leaflet CSS & JS (CDN) --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        const areas = @json($areas);

        // auto-fit marker
        const map = L.map('map');

        // fallback center kalau belum ada data
        if (areas.length === 0) {
            const fallbackCenter = [-6.5, 108.3];
            map.setView(fallbackCenter, 12);
        }

        // bounds untuk auto-fit
        const bounds = [];

        // Base layers (pilihan tampilan peta)
        const osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        });

        const light = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap, © Carto'
        });

        const satellite = L.tileLayer(
            'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
            {
                maxZoom: 19,
                attribution: 'Tiles © Esri'
            }
        );

        // default pakai OSM
        osm.addTo(map);

        // custom icon hijau
        const greenIcon = new L.Icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.3.4/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            shadowSize: [41, 41]
        });

        areas.forEach(a => {
            const marker = L.marker([a.latitude, a.longitude], { icon: greenIcon }).addTo(map);

            const info = `
                <div style="min-width:200px">
                    <strong>${a.nama_wilayah}</strong><br/>
                    KK: ${a.kk ?? '-'}<br/>
                    L: ${a.laki_laki ?? '-'} | P: ${a.perempuan ?? '-'}<br/>
                    Total: <b>${a.jumlah_penduduk ?? '-'}</b><br/>
                    Tahun: ${a.tahun ?? '-'}
                </div>
            `;
            marker.bindPopup(info);

            bounds.push([a.latitude, a.longitude]);
        });

        // auto-fit ke semua marker
        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [40, 40] });
        }

        // Legend (Keterangan)
        const legend = L.control({ position: "bottomright" });

        legend.onAdd = function () {
            const div = L.DomUtil.create("div", "legend");
            div.innerHTML = `
                <div style="
                    background: white;
                    padding: 10px 14px;
                    border-radius: 8px;
                    box-shadow: 0 1px 4px rgba(0,0,0,0.2);
                    font-size: 12px;
                    line-height: 1.4;
                ">
                    <strong>Keterangan:</strong><br>
                    <img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png"
                        style="width: 14px; vertical-align: middle; margin-right: 4px;">
                    Wilayah Penduduk
                </div>
            `;
            return div;
        };

        legend.addTo(map);

        // Tombol Reset Zoom
        const resetControl = L.Control.extend({
            options: { position: 'topleft' },

            onAdd: function () {
                const btn = L.DomUtil.create('button', 'reset-zoom-btn');

                btn.innerHTML = 'Reset Zoom';
                btn.style.background = '#fff';
                btn.style.padding = '6px 10px';
                btn.style.border = '1px solid #ccc';
                btn.style.borderRadius = '6px';
                btn.style.cursor = 'pointer';
                btn.style.fontSize = '12px';
                btn.style.boxShadow = '0 1px 4px rgba(0,0,0,0.3)';

                btn.onclick = () => {
                    if (bounds.length > 0) {
                        map.fitBounds(bounds, { padding: [40, 40] });
                    }
                };

                return btn;
            }
        });

        map.addControl(new resetControl());

        // Layer control (pilihan tampilan peta)
        const baseMaps = {
            'OpenStreetMap': osm,
            'Peta Light': light,
            'Peta Satelit': satellite,
        };

        L.control.layers(baseMaps, null, { position: 'topleft' }).addTo(map);
    </script>
</x-app-layout>