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
    /* Variabel warna */
    :root {
        --map-bg-card: #ffffff;
        --map-text-primary: #333333;
        --map-text-subtle: #666666;
        --map-border-color: #dddddd;
        --map-primary-color: #0a6847;
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

    /* Legenda */
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
        color: #000 !important;
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

    /* Layout */
    .container.py-4 {
        padding-left: 2rem !important;
        padding-right: 2rem !important;
    }

    /* Popup Style */
    .leaflet-popup-content-wrapper { background: var(--map-bg-card); color: var(--map-text-primary); border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
    .leaflet-popup-content-wrapper .popup-title { font-size: 1.2rem; font-weight: bold; color: var(--map-primary-color); margin-bottom: 8px; border-bottom: 1px solid var(--map-border-color); padding-bottom: 5px; }
    .leaflet-popup-content-wrapper .popup-category { font-size: 0.8rem; font-weight: bold; background-color: var(--map-primary-color); color: white; padding: 3px 8px; border-radius: 12px; display: inline-block; margin-bottom: 8px; }
    .leaflet-popup-content-wrapper img { width: 100%; height: auto; border-radius: 6px; margin-top: 10px; }
    .leaflet-popup-tip { background: var(--map-bg-card); }

    /* Filters */
    .map-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.5rem;
        background-color: var(--map-bg-card, #f8f9fa);
        padding: 1rem;
        border-radius: 12px;
        border: 1px solid var(--map-border-color, #dee2e6);
    }
    .map-filters .form-group { flex: 1 1 250px; }
    .map-filters label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--map-text-subtle, #6c757d); font-size: 0.9rem; }
    .map-filters .form-control, .map-filters .form-select {
        width: 100%; padding: 10px; border-radius: 8px;
        border: 1px solid var(--map-border-color, #ced4da);
        background-color: #ffffff; color: var(--map-text-primary, #495057);
        box-sizing: border-box; font-size: 1rem;
    }

    /* Summary Stats */
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
    .summary-stat-card h4 { margin: 0 0 0.5rem 0; color: var(--map-text-subtle, #6c757d); text-transform: uppercase; font-size: 0.9rem; }
    .summary-stat-card .value { font-size: 2.5rem; font-weight: 700; color: var(--map-primary-color, #0a6847); }
</style>
@endpush

@section('content')
    <div class="container py-4">
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
                    {{-- Opsi akan diisi otomatis oleh JavaScript --}}
                </select>
            </div>
        </div>

        {{-- MAP CONTAINER --}}
        <div id="map"></div>

        <div id="map-summary-container" style="margin-top: 2.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 600; color: var(#ffffffff); margin-bottom: 1.5rem; border-bottom: 1px solid var(--map-border-color, #dee2e6); padding-bottom: 1rem;">
                Ringkasan Kategori
            </h2>
            <div class="summary-stats-grid" id="map-summary" style="margin-bottom: 2.5rem;">
                <p style="color: var(--map-text-subtle);">Memuat data ringkasan...</p>
            </div>
        </div>
        <div id="legend" style="margin-top: 15px;"></div>
    </div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin=""></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        let allFeaturesData = null;
        let buildingsLayer = null;

        // --- 1. Inisialisasi Peta ---
        const mapCenter = [-7.8180, 112.0185];
        const map = L.map('map').setView(mapCenter, 16);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        // --- 2. Definisi GLOBAL 12 Kategori ---
        const categoryColors = {
            "Makanan & Minuman": "#e57373",
            "Perbelanjaan": "#ffb74d",
            "Akomodasi": "#64b5f6",
            "Perumahan": "#9e9e9e",
            "Layanan Publik & Administrasi": "#4db6ac",
            "Pemerintahan & Sipil": "#673ab7",
            "Kesehatan": "#ff3d00",
            "Pendidikan": "#1565c0",
            "Rekreasi, Seni & Budaya": "#81c784",
            "Alam & Lingkungan": "#388e3c",
            "Otomotif & Transportasi": "#ff9800",
            "Agama & Spiritual": "#fdd835"
        };

        // Nama file ikon harus sesuai dengan yang ada di folder /public/icons/
        const categoryIcons = {
            "Makanan & Minuman": "restaurant.svg",
            "Perbelanjaan": "shop.svg",
            "Akomodasi": "lodging.svg",
            "Perumahan": "home.svg",
            "Layanan Publik & Administrasi": "place-of-worship.svg",
            "Pemerintahan & Sipil": "town-hall.svg",
            "Kesehatan": "hospital-JP.svg",
            "Pendidikan": "college.svg",
            "Rekreasi, Seni & Budaya": "art-gallery.svg",
            "Alam & Lingkungan": "zoo.svg",
            "Otomotif & Transportasi": "bus.svg",
            "Agama & Spiritual": "religious-muslim.svg"
        };

        // --- 3. Memuat Batas Wilayah ---
        const styleBatas = {
            "color": "#666666",
            "weight": 3,
            "opacity": 0.8,
            "fillColor": "#888888",
            "fillOpacity": 0.2
        };

        fetch("{{ asset('geojson/sukorame_boundary.geojson') }}")
            .then(res => { if (!res.ok) throw new Error('Gagal'); return res.json(); })
            .then(data => {
                L.geoJSON(data, { style: styleBatas }).addTo(map);
                map.fitBounds(L.geoJSON(data).getBounds().pad(0.1));
            })
            .catch(err => console.error('Error loading boundary:', err));

        // --- Helper Functions ---
        function createLegend() {
            const legend = document.getElementById('legend');
            legend.innerHTML = '<strong>Keterangan Warna:</strong><br>';
            for (const [key, color] of Object.entries(categoryColors)) {
                legend.innerHTML += `<span style="display:inline-block;width:14px;height:14px;background:${color};border-radius:3px;margin-right:6px;"></span>${key}<br>`;
            }
        }
        createLegend();

        // --- 4. Memuat Titik Bangunan (CORE LOGIC) ---
        fetch('{{ route('api.bangunan.map') }}')
            .then(response => {
                if (!response.ok) throw new Error('Gagal mengambil data bangunan.');
                return response.json();
            })
            .then(geoJsonData => {
                allFeaturesData = geoJsonData;

                // Hitung Statistik & Filter
                const categoryCounts = {};
                const uniqueCategories = new Set();
                allFeaturesData.features.forEach(feature => {
                    const category = feature.properties.kategori;
                    if (category) {
                        categoryCounts[category] = (categoryCounts[category] || 0) + 1;
                        uniqueCategories.add(category);
                    }
                });

                populateSummaryStats(categoryCounts);
                populateCategoryFilter(uniqueCategories);

                // --- RENDER LAYER BANGUNAN ---
                buildingsLayer = L.geoJSON(geoJsonData, {
                    pointToLayer: function (feature, latlng) {
                        const kategori = feature.properties.kategori;
                        
                        const iconFile = categoryIcons[kategori]; 
                        const bgColor = categoryColors[kategori];

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
                                     style="width:20px;height:20px;filter:invert(1); transform:rotate(45deg);"
                                     onerror="this.onerror=null; this.src='/icons/lainnya.svg';"> 
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
                        const catColor = categoryColors[props.kategori] || "#0a6847";

                        // Perhatikan penambahan class="popup-img-fixed" pada tag <img> di bawah
                        const popupContent = `
                            <div class="popup-title">${props.nama}</div>
                            <div class="popup-category" style="background-color:${catColor}">
                                ${props.kategori}
                            </div>
                            <p>${props.deskripsi || 'Tidak ada deskripsi.'}</p>
                            ${props.foto_url ? `<img src="${props.foto_url}" alt="Foto ${props.nama}" class="popup-img-fixed">` : ''}
                        `;

                        // Tambahkan opsi { maxWidth: 320 } agar popup tidak terlalu melebar
                        layer.bindPopup(popupContent, {
                            maxWidth: 320, // Lebar maksimal popup dalam pixel (standar yang bagus)
                            minWidth: 200  // Lebar minimal
                        });
                    }
                }).addTo(map);

                // Event Listener untuk Filter
                document.getElementById('search-input').addEventListener('input', filterMap);
                document.getElementById('category-filter').addEventListener('change', filterMap);
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('map').innerHTML = `<p style="text-align:center;padding:20px;color:red;">Gagal memuat data bangunan. Cek Console.</p>`;
            });

        // --- Fungsi Filter ---
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
            buildingsLayer.addData({ type: 'FeatureCollection', features: filteredFeatures });
        }

        // --- Fungsi Statistik ---
        function populateSummaryStats(counts) {
            const summaryContainer = document.getElementById('map-summary');
            summaryContainer.innerHTML = '';
            if (Object.keys(counts).length === 0) {
                summaryContainer.innerHTML = `<p>Tidak ada data kategori.</p>`;
                return;
            }
            Object.keys(counts).sort().forEach(category => {
                const count = counts[category];
                const primaryColor = categoryColors[category] || '#0a6847';
                summaryContainer.innerHTML += `
                    <div class="summary-stat-card">
                        <h4>${category}</h4>
                        <span class="value" style="color: ${primaryColor}">${count}</span>
                    </div>
                `;
            });
        }

        // --- Fungsi Dropdown ---
        function populateCategoryFilter(categories) {
            const filterSelect = document.getElementById('category-filter');
            [...categories].sort().forEach(category => {
                const option = document.createElement('option');
                option.value = category;
                option.textContent = category;
                filterSelect.appendChild(option);
            });
        }

        // --- 5. Memuat Polygon Tambahan ---
        const loadPolygon = (url, color, popupText) => {
            fetch(url).then(r=>r.json()).then(d=>{
                L.geoJSON(d, {style:{color:color,weight:2,fillColor:color,fillOpacity:0.35}})
                .bindPopup((l) => `<b>${popupText}</b><br>${l.feature.properties.name || ''}`)
                .addTo(map);
            }).catch(e=>console.log(`Error loading ${popupText}:`, e));
        };

        loadPolygon("{{ asset('geojson/brigif.geojson') }}", "#723a3a", "Kawasan Brigif");
        loadPolygon("{{ asset('geojson/sawah.geojson') }}", "#0f7d2c", "Area Sawah");
        loadPolygon("{{ asset('geojson/pemukiman.geojson') }}", "#3e5eae", "Kawasan Pemukiman");
    });
</script>
@endpush