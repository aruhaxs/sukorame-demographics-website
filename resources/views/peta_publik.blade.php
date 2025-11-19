{{-- Pastikan 'layouts.app' adalah nama layout publik Anda --}}
@extends('layouts.app')

@section('title', 'Peta Sebaran Wilayah')

{{-- Menambahkan library Leaflet.js di head --}}
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""/>

<link
  rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
/>

<style>
    /*
     * PENTING: Anda mungkin perlu menyesuaikan warna dan gaya di bawah ini
     * agar cocok dengan tema layout 'layouts.app' Anda.
     * Atau, pindahkan gaya ini ke file CSS utama Anda (misal: public/css/app.css)
     */

    /* Variabel warna contoh (sesuaikan!) */
    :root {
        --map-bg-card: #ffffff; /* Latar kartu/popup (jika tema terang) */
        --map-text-primary: #333333; /* Warna teks utama */
        --map-text-subtle: #666666; /* Warna teks sekunder */
        --map-border-color: #dddddd; /* Warna border */
        --map-primary-color: #0a6847; /* Warna utama (hijau) */
        --map-highlight-color: #e53e3e; /* Warna highlight (merah) */
    }

    #map {
        height: 70vh;
        width: 100%;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        z-index: 1;
        background-color: #f8f9fa;
        margin-bottom: 1.5rem;
    }

    /* Legenda warna di kanan bawah */
    #legend {
        position: absolute;
        bottom: 20px;
        right: 25px;
        background: white;
        border: 1px solid #ccc;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 0.9rem;
        line-height: 1.4;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        z-index: 1000;
        color: #000 !important;   /* Pastikan teks hitam */
    }
    #legend strong {
        display: block;
        margin-bottom: 8px;
        color: var(--map-text-primary, #333);
    }
    #legend span {
        display: inline-block;
        width: 14px;
        height: 14px;
        border-radius: 3px;
        margin-right: 6px;
    }

    /* Supaya container-nya tidak mepet */
    .container.py-4 {
        padding-left: 2rem !important;
        padding-right: 2rem !important;
    }


    /* Kustomisasi popup (sesuaikan warna!) */
    .leaflet-popup-content-wrapper { background: var(--map-bg-card); color: var(--map-text-primary); border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
    .leaflet-popup-content-wrapper .popup-title { font-size: 1.2rem; font-weight: bold; color: var(--map-primary-color); margin-bottom: 8px; border-bottom: 1px solid var(--map-border-color); padding-bottom: 5px; }
    .leaflet-popup-content-wrapper .popup-category { font-size: 0.8rem; font-weight: bold; background-color: var(--map-primary-color); color: white; padding: 3px 8px; border-radius: 12px; display: inline-block; margin-bottom: 8px; }
    .leaflet-popup-content-wrapper img { width: 100%; height: auto; border-radius: 6px; margin-top: 10px; }
    .leaflet-popup-tip { background: var(--map-bg-card); }

    /* Style untuk Filter (sesuaikan warna!) */
    .map-filters {
        display: flex;
        flex-wrap: wrap; /* Agar responsif di layar kecil */
        gap: 1rem;
        margin-bottom: 1.5rem;
        background-color: var(--map-bg-card, #f8f9fa); /* Default jika variabel tidak ada */
        padding: 1rem;
        border-radius: 12px;
        border: 1px solid var(--map-border-color, #dee2e6);
    }
    .map-filters .form-group {
        flex: 1 1 250px; /* Lebar minimum 250px, bisa membesar */
    }
    .map-filters label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: var(--map-text-subtle, #6c757d);
        font-size: 0.9rem;
    }
    .map-filters .form-control,
    .map-filters .form-select {
        width: 100%;
        padding: 10px; /* Sedikit lebih kecil */
        border-radius: 8px;
        border: 1px solid var(--map-border-color, #ced4da);
        background-color: #ffffff;
        color: var(--map-text-primary, #495057);
        box-sizing: border-box;
        font-size: 1rem;
    }
    .map-filters .form-control:focus,
    .map-filters .form-select:focus {
         border-color: var(--map-primary-color);
         outline: none;
         box-shadow: 0 0 0 2px rgba(10, 104, 71, 0.2); /* Shadow sesuai warna primer */
    }

    /* Style untuk Ringkasan Kategori (sesuaikan warna!) */
    .summary-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 1.5rem;
        margin-top: 2.5rem;
    }
    .summary-stat-card {
        background-color: var(--map-bg-card, #ffffff);
        padding: 1.5rem;
        border-radius: 12px;
        text-align: center;
        border: 1px solid var(--map-border-color, #dee2e6);
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .summary-stat-card h4 {
        margin: 0 0 0.5rem 0;
        color: var(--map-text-subtle, #6c757d);
        text-transform: uppercase;
        font-size: 0.9rem;
    }
    .summary-stat-card .value {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--map-primary-color, #0a6847);
    }
</style>
@endpush

@section('content')

    {{-- Gunakan container jika layout Anda membutuhkannya --}}
    <div class="container py-4">

        {{-- Judul Halaman Publik (Sesuaikan style jika perlu) --}}
        <h1 style="font-size: 1.8rem; font-weight: 600; color: var(#ffffffff); margin-bottom: 1rem;">
            Peta Sebaran Wilayah
        </h1>
        <p style="color: var(#ffffffff); margin-bottom: 2rem;">
            Lihat lokasi fasilitas umum, UMKM, dan bangunan lainnya di wilayah kami.
        </p>

        <div class="map-filters">
            <div class="form-group">
                <label for="search-input">Cari Nama Bangunan</label>
                <input type="text" id="search-input" class="form-control" placeholder="Cth: POLINEMA PSDKU...">
            </div>
            <div class="form-group">
                <label for="category-filter">Filter Kategori</label>
                <select id="category-filter" class="form-select">
                    <option value="">Semua Kategori</option>
                    {{-- Opsi akan ditambahkan oleh JavaScript --}}
                </select>
            </div>
        </div>

        {{-- Container untuk Peta Interaktif --}}
        <div id="map"></div>

        <div id="map-summary-container" style="margin-top: 2.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 600; color: var( #ffffffff); margin-bottom: 1.5rem; border-bottom: 1px solid var(--map-border-color, #dee2e6); padding-bottom: 1rem;">
                Ringkasan Kategori
            </h2>
            <div class="summary-stats-grid" id="map-summary" style="margin-bottom: 2.5rem;">
                {{-- Konten akan diisi oleh JavaScript --}}
                <p style="color: var(--map-text-subtle);">Memuat data ringkasan...</p>
            </div>
        </div>
        <div id="legend" style="margin-top: 15px;"></div>

    </div>

@endsection

{{--
======================================================================
SCRIPT PETA (Tidak ada perubahan fungsional, hanya memastikan @push ada)
======================================================================
--}}
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin=""></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        let allFeaturesData = null; // Menyimpan data asli dari API
        let buildingsLayer = null;  // Menyimpan layer Leaflet

        // --- 1. Inisialisasi Peta ---
        const mapCenter = [-7.8180, 112.0185];
        const map = L.map('map').setView(mapCenter, 16);

        // --- 2. Tambahkan Tile Layer (Peta Dasar) ---
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        // --- 3. Memuat Batas Wilayah (GeoJSON Statis) ---
        const styleBatas = {
            "color": "#e53e3e",     // WARNA MERAH
            "weight": 3,
            "opacity": 0.8,
            "fillColor": "#e53e3e",
            "fillOpacity": 0.2
        };

        fetch("{{ asset('geojson/sukorame_boundary.geojson') }}")
            .then(response => {
                if (!response.ok) throw new Error('File batas wilayah tidak ditemukan.');
                return response.json();
            })
            .then(data => {
                L.geoJSON(data, { style: styleBatas }).addTo(map);
                map.fitBounds(L.geoJSON(data).getBounds().pad(0.1));
            })
            .catch(error => console.error('Error loading boundary GeoJSON:', error));

            // Warna khusus berdasarkan kategori
        const categoryColors = {
            "Pendidikan": "blue",   // Biru
            "Kesehatan": "",    // Merah
            "Tempat Ibadah": "yellow",// Kuning
            "UMKM": "green",         // Hijau
            "Lainnya": "violet"       // Ungu
        };

            function getMarkerColor(kategori) {
        switch (kategori) {
            case "Pendidikan": return "blue";
            case "Kesehatan": return "red";
            case "Tempat Ibadah": return "yellow";
            case "UMKM": return "green";
            case "Lainnya": return "violet";
            default: return "green";
        }
    }




        // --- 4. Memuat Titik Bangunan (GeoJSON Dinamis dari API) ---
        fetch('{{ route('api.bangunan.map') }}')
            .then(response => {
                if (!response.ok) throw new Error('Gagal mengambil data bangunan.');
                return response.json();
            })
            .then(geoJsonData => {
                allFeaturesData = geoJsonData; // Simpan data asli

                const categoryCounts = {};
                const uniqueCategories = new Set();

                // Loop untuk menghitung kategori & mengisi filter
                allFeaturesData.features.forEach(feature => {
                    const category = feature.properties.kategori;
                    if (category) {
                        categoryCounts[category] = (categoryCounts[category] || 0) + 1;
                        uniqueCategories.add(category);
                    }
                });

                populateSummaryStats(categoryCounts);
                populateCategoryFilter(uniqueCategories);

                function createLegend() {
                const legend = document.getElementById('legend');
                legend.innerHTML = '<strong>Keterangan Warna:</strong><br>';
                for (const [key, color] of Object.entries(categoryColors)) {
                    legend.innerHTML += `
                        <span style="display:inline-block;width:14px;height:14px;background:${color};border-radius:3px;margin-right:6px;"></span>${key}<br>
                    `;
                }
}
createLegend();


buildingsLayer = L.geoJSON(geoJsonData, {
    pointToLayer: function (feature, latlng) {
    const kategori = feature.properties.kategori;

    const categoryIcons = {
        "Pendidikan": "college.svg",
        "Kesehatan": "hospital-JP.svg",
        "Tempat Ibadah": "religious-muslim.svg",
        "UMKM": "fast-food.svg",
        "Lainnya": "lainnya.svg"
    };

    const categoryColors = {
        "Pendidikan": "#1d5e9eff",
        "Kesehatan": "#069764ff",
        "Tempat Ibadah": "#ffcb52ff",
        "UMKM": "#901212ff",
        "Lainnya": "#8b5cf6"
    };

    const iconFile = categoryIcons[kategori] || "lainnya.svg";
    const bgColor = categoryColors[kategori] || "#0a6847";

    const customIcon = L.divIcon({
        className: "custom-marker",
        html: `
    <div style="
        position:relative;
        width:40px;
        height:40px;
        background:${bgColor};
        border-radius:50% 50% 50% 0;
        transform:rotate(-45deg);
        border:2px solid white;
        display:flex;
        justify-content:center;
        align-items:center;
        box-shadow:0 0 5px rgba(0,0,0,0.3);
    ">
        <img src="/icons/${iconFile}" 
             style="width:20px;height:20px;filter:invert(1); transform:rotate(45deg);">
    </div>
`,

        iconSize: [40, 40],
        iconAnchor: [20, 40],
        popupAnchor: [0, -40]
    });

    return L.marker(latlng, { icon: customIcon });
},


    onEachFeature: function (feature, layer) {
        const props = feature.properties;
        const popupContent = `
            <div class="popup-title">${props.nama}</div>
            <div class="popup-category" style="background-color:${categoryColors[props.kategori] || '#0a6847'}">
                ${props.kategori}
            </div>
            <p>${props.deskripsi || 'Tidak ada deskripsi.'}</p>
            <img src="${props.foto_url}" alt="Foto ${props.nama}">
        `;
        layer.bindPopup(popupContent);
    }
}).addTo(map);


                // Aktifkan filter
                document.getElementById('search-input').addEventListener('input', filterMap);
                document.getElementById('category-filter').addEventListener('change', filterMap);
            })
            .catch(error => {
                console.error('Error fetching map data:', error);
                document.getElementById('map').innerHTML = `<p style="text-align:center; padding: 20px; color: var(--color-danger, #dc3545);">Gagal memuat data titik bangunan.</p>`;
                document.getElementById('map-summary').innerHTML = `<p style="color: var(--color-danger, #dc3545);">Gagal memuat ringkasan.</p>`;
            });


        // Fungsi untuk Filter Peta
        function filterMap() {
            if (!allFeaturesData || !buildingsLayer) return;

            const searchTerm = document.getElementById('search-input').value.toLowerCase();
            const categoryFilter = document.getElementById('category-filter').value;

            buildingsLayer.clearLayers();

            const filteredFeatures = allFeaturesData.features.filter(feature => {
                const props = feature.properties;
                const nameMatch = props.nama.toLowerCase().includes(searchTerm);
                const categoryMatch = (categoryFilter === "" || props.kategori === categoryFilter);
                return nameMatch && categoryMatch;
            });

            buildingsLayer.addData({
                type: 'FeatureCollection',
                features: filteredFeatures
            }).eachLayer(function (layer) {
                const kategori = layer.feature.properties.kategori;
                const color = categoryColors[kategori] || "#0a6847";
                if (layer.setStyle) {
                    layer.setStyle({ fillColor: color, color: "#fff" });
                }
            });

            const categoryStyles = {
                "Pendidikan": {
                    color: "#2861a7ff",
                    icon: "bi bi-book"
                },
                "Kesehatan": {
                    color: "#2bac40ff",
                    icon: "bi bi-hospital"
                },
                "Tempat Ibadah": {
                    color: "#b8b8b8ff",
                    icon: "bi bi-moon-stars"
                },
                "UMKM": {
                    color: "#17a2b8",
                    icon: "bi bi-shop"
                },
                "Lainnya": {
                    color: "#6c757d",
                    icon: "bi bi-geo-alt"
                }
            };


        }

        // Fungsi untuk Ringkasan Kategori
        function populateSummaryStats(counts) {
            const summaryContainer = document.getElementById('map-summary');
            summaryContainer.innerHTML = '';

            if (Object.keys(counts).length === 0) {
                 summaryContainer.innerHTML = `<p style="color: var(--map-text-subtle);">Tidak ada data kategori.</p>`;
                 return;
            }

            const sortedCategories = Object.keys(counts).sort();

            for (const category of sortedCategories) {
                const count = counts[category];
                const cardHtml = `
                    <div class="summary-stat-card">
                        <h4>${category}</h4>
                        <span class="value">${count}</span>
                    </div>
                `;
                summaryContainer.innerHTML += cardHtml;
            }
        }

        // Fungsi untuk mengisi dropdown filter
        function populateCategoryFilter(categories) {
            const filterSelect = document.getElementById('category-filter');
            const sortedCategories = [...categories].sort();

            sortedCategories.forEach(category => {
                const option = document.createElement('option');
                option.value = category;
                option.textContent = category;
                filterSelect.appendChild(option);
            });
        }

        // --- 5. Memuat Kawasan Perumahan (Polygon Hijau) ---
        fetch("{{ asset('geojson/brigif.geojson') }}")
        .then(res => {
            if (!res.ok) throw new Error("GeoJSON Brigif tidak ditemukan.");
            return res.json();
        })
        .then(brigifData => {

            const brigifStyle = {
                color: "#723a3aff",      // outline
                weight: 2,
                fillColor: "#6a2525ff", // warna isi
                fillOpacity: 0.35
            };

            const brigifLayer = L.geoJSON(brigifData, {
                style: brigifStyle,
                onEachFeature: (feature, layer) => {
                    const name = feature?.properties?.name || "Kawasan Brigif";
                    layer.bindPopup(`<b>Kawasan Brigif</b><br>${name}`);
                }
            }).addTo(map);

            // map.fitBounds(brigifLayer.getBounds());
        })
        .catch(err => console.error("Error loading Brigif area:", err));


        // --- 6. Memuat Kawasan Sawah (Polygon Hijau Muda) ---
        fetch("{{ asset('geojson/sawah.geojson') }}")
        .then(res => {
            if (!res.ok) throw new Error("GeoJSON Sawah tidak ditemukan.");
            return res.json();
        })
        .then(sawahData => {

            const sawahStyle = {
                color: "#0f7d2c",       // outline hijau tua
                weight: 2,
                fillColor: "#4caf50",   // isi hijau muda
                fillOpacity: 0.4
            };

            const sawahLayer = L.geoJSON(sawahData, {
                style: sawahStyle,
                onEachFeature: (feature, layer) => {
                    const name = feature?.properties?.name || "Kawasan Sawah";
                    layer.bindPopup(`<b>Area Sawah</b>`);
                }
            }).addTo(map);

            // map.fitBounds(sawahLayer.getBounds());
        })
        .catch(err => console.error("Error loading Sawah area:", err));



        fetch("{{ asset('geojson/pemukiman.geojson') }}")
            .then(res => {
                if (!res.ok) throw new Error("GeoJSON pemukiman tidak ditemukan.");
                return res.json();
            })
            .then(pemukimanData => {

                const pemukimanStyle = {
                    color: "#3e5eaeff",       // outline
                    weight: 2,
                    fillColor: "#144b8aff",   // isi
                    fillOpacity: 0.4
                };

                const pemukimanLayer = L.geoJSON(pemukimanData, {
                    style: pemukimanStyle,
                    onEachFeature: (feature, layer) => {
                        if (feature.properties && feature.properties.name) {
                            layer.bindPopup(`<b>Kawasan Pemukiman:</b><br>${feature.properties.name}`);
                        } else {
                            layer.bindPopup(`<b>Kawasan Pemukiman</b>`);
                        }
                    }
                }).addTo(map);

                // map.fitBounds(pemukimanLayer.getBounds());
            })
            .catch(err => console.error("Error loading pemukiman area:", err));

    });

    

</script>
@endpush
