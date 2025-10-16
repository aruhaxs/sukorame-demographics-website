@extends('layouts.app')
@section('title', 'Peta Wilayah')

@section('content')
{{-- Link ke CSS & JS Leaflet (wajib) --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
    #map {
        height: 600px;
        width: 100%;
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
    }
</style>

<section class="demografi-section">
    <h2 class="section-title">PETA WILAYAH KELURAHAN</h2>

    {{-- Container peta --}}
    <div id="map"></div>
</section>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // [1] Inisialisasi Peta
        var map = L.map('map').setView([-7.82, 112.02], 15); // Koordinat tengah kelurahan Anda

        // [2] Tambahkan Layer Peta dari OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // =================================================================
        // [3] TAMBAHKAN BATAS WILAYAH (POLIGON)
        // =================================================================
        const batasSukorameCoords = [
            [-7.815, 112.018],
            [-7.815, 112.025],
            [-7.822, 112.025],
            [-7.822, 112.018]
        ];

        const batasWilayah = L.polygon(batasSukorameCoords, {
            color: '#FF0000',      // Warna garis
            weight: 2,             // Ketebalan garis
            opacity: 0.8,          // Transparansi garis
            fillColor: '#FF0000',  // Warna isian
            fillOpacity: 0.15      // Transparansi isian
        }).addTo(map);


        // =================================================================
        // [4] AMBIL DAN TAMPILKAN TITIK LOKASI (MARKER) DARI DATABASE
        // =================================================================
        fetch("{{ route('api.locations') }}")
            .then(response => response.json())
            .then(locations => {
                locations.forEach(loc => {
                    var marker = L.marker([loc.latitude, loc.longitude]).addTo(map);

                    marker.bindPopup(
                        `<b>${loc.nama_bangunan}</b><br>` +
                        `Kategori: ${loc.kategori}<br>` +
                        `${loc.deskripsi}`
                    );
                });
            })
            .catch(error => console.error('Error fetching locations:', error));
    });
</script>
@endpush
