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
                <div id="map" style="height: 520px;"></div>
            </div>
        </div>
    </div>

    {{-- Leaflet CSS & JS (CDN) --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        const areas = @json($areas);

        // fallback center sementara (akan kita ganti ke koordinat Desa Patimban kalau sudah ada)
        const center = [-6.5, 108.3]; // approx Indramayu area; nanti kita update

        const map = L.map('map').setView(center, 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        areas.forEach(a => {
            const marker = L.marker([a.latitude, a.longitude]).addTo(map);
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
        });
    </script>
</x-app-layout>
