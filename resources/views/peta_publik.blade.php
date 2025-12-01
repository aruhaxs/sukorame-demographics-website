@extends('layouts.app')

@section('title', 'Peta Sebaran Wilayah')

@push('styles')
{{-- Library Leaflet, Google Fonts & Icons --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"/>

<style>
    :root {
        --primary-color: #0a6847; /* Hijau Utama */
        --secondary-color: #f7a731; /* Kuning/Emas */
        --text-dark: #1a202c;
        --text-gray: #718096;
        --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    body { font-family: 'Poppins', sans-serif; background-color: #f3f4f6; }

    /* --- CONTAINER PENYAMA LEBAR (SAMA DENGAN NAVBAR & FOOTER) --- */
    .content-container {
        width: 100%;
        max-width: 1200px; /* Lebar maksimum sama dengan Navbar */
        margin: 0 auto;    /* Posisi Tengah */
        padding: 2rem 1.5rem; /* Padding: Atas-Bawah 2rem, Kiri-Kanan 1.5rem (Sama Navbar) */
        position: relative; 
    }

    /* --- 1. HEADER & FILTER SECTION (Modern Look) --- */
    .dashboard-header {
        background: #ffffff;
        border-radius: 16px;
        padding: 1.5rem 2rem;
        box-shadow: var(--card-shadow);
        margin-bottom: 20px;
        border: 1px solid rgba(0,0,0,0.02);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1.5rem;
    }

    .header-content h1 {
        font-size: 1.6rem; font-weight: 700; color: var(--text-dark); margin: 0; letter-spacing: -0.5px;
    }
    .header-content p {
        color: var(--text-gray); font-size: 0.95rem; margin: 5px 0 0 0;
    }

    .filter-wrapper {
        display: flex; gap: 10px; flex: 1; max-width: 600px; justify-content: flex-end;
    }

    /* Input Style */
    .search-container { position: relative; flex: 2; }
    .search-container i {
        position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #a0aec0; transition: color 0.3s;
    }
    .custom-input {
        width: 100%; padding: 12px 16px 12px 48px; border: 2px solid #edf2f7; border-radius: 10px;
        background: #f8fafc; font-size: 0.95rem; outline: none; transition: all 0.3s ease;
    }
    .custom-input:focus {
        border-color: var(--primary-color); background: #ffffff; box-shadow: 0 0 0 3px rgba(10, 104, 71, 0.1);
    }
    .search-container:focus-within i { color: var(--primary-color); }

    .custom-select {
        flex: 1; min-width: 180px; padding: 12px 16px; border: 2px solid #edf2f7; border-radius: 10px;
        background: #f8fafc; cursor: pointer; outline: none; transition: all 0.3s ease;
    }
    .custom-select:focus { border-color: var(--primary-color); background: #ffffff; }

    /* --- 2. MAP WRAPPER --- */
    .map-wrapper {
        position: relative; width: 100%; height: 75vh; border-radius: 16px;
        overflow: hidden; box-shadow: var(--card-shadow); background: white; border: 1px solid #e2e8f0;
    }
    #map { width: 100%; height: 100%; z-index: 1; }

    /* --- 3. LEGENDA (Floating Toggle Button) --- */
    .legend-container {
        position: absolute; bottom: 30px; left: 20px; z-index: 999;
        display: flex; flex-direction: column-reverse; align-items: flex-start; gap: 10px;
    }

    .legend-btn {
        width: 50px; height: 50px; background: white; border-radius: 50%;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15); display: flex; justify-content: center; align-items: center;
        cursor: pointer; border: none; color: var(--text-dark); font-size: 1.4rem; transition: all 0.3s ease;
    }
    .legend-btn:hover { background: var(--primary-color); color: white; transform: scale(1.05); }
    .legend-btn.active { background: var(--primary-color); color: white; transform: rotate(180deg); }

    .legend-content {
        background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(5px);
        padding: 0; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        width: 220px; max-height: 0; overflow: hidden; opacity: 0;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .legend-content.show { max-height: 400px; opacity: 1; padding: 15px; overflow-y: auto; }

    .legend-title {
        font-size: 0.85rem; font-weight: 700; margin-bottom: 10px; color: var(--text-dark);
        border-bottom: 2px solid #edf2f7; padding-bottom: 5px;
    }
    .legend-item { display: flex; align-items: center; margin-bottom: 8px; font-size: 0.8rem; color: #4a5568; font-weight: 500; }
    .legend-color { width: 12px; height: 12px; border-radius: 3px; margin-right: 10px; flex-shrink: 0; }

    /* --- 4. DRAWER DETAIL (Sidebar) --- */
    .info-drawer {
        position: absolute; top: 0; right: 0; width: 350px; height: 100%;
        background: white; z-index: 1000; box-shadow: -5px 0 25px rgba(0,0,0,0.1);
        transform: translateX(100%); transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow-y: auto; display: flex; flex-direction: column;
    }
    .info-drawer.active { transform: translateX(0); }

    .drawer-header { position: relative; height: 200px; background-color: #f7fafc; }
    .drawer-img { width: 100%; height: 100%; object-fit: cover; }
    .close-drawer {
        position: absolute; top: 15px; left: 15px; background: rgba(255,255,255,0.9); border: none;
        width: 36px; height: 36px; border-radius: 50%; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1); z-index: 2; transition: all 0.2s;
    }
    .close-drawer:hover { background: white; transform: scale(1.1); }

    .drawer-body { padding: 1.5rem; }
    
    .drawer-badge {
        background: var(--primary-color); color: white; padding: 4px 12px; border-radius: 20px;
        font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;
        display: inline-block; margin-bottom: 0.5rem;
    }

    #info-drawer {
        color: #041c36 !important;
    }

    /* 2. Paksa Judul menjadi Navy Gelap */
    .drawer-title {
        font-size: 1.4rem;
        font-weight: 700;
        color: #041c36 !important;
        margin-bottom: 0.5rem;
        line-height: 1.3;
        display: block;
    }

    /* 3. Paksa Deskripsi menjadi Navy Gelap */
    .drawer-desc {
        font-size: 0.95rem;
        color: #041c36 !important; /* PENTING: !important */
        line-height: 1.6;
        margin-bottom: 1.5rem;
        display: block;
    }
    
    .meta-box { background: #f8fafc; padding: 1rem; border-radius: 8px; border: 1px solid #edf2f7; }
    .meta-item { display: flex; align-items: center; gap: 10px; font-size: 0.85rem; color: #718096; }

    /* Responsive */
    @media (max-width: 768px) {
        .dashboard-header { flex-direction: column; align-items: flex-start; padding: 1.5rem; }
        .filter-wrapper { width: 100%; flex-direction: column; }
        .info-drawer { width: 100%; } /* Drawer full screen di HP */
        .map-wrapper { height: 80vh; }
        .legend-container { left: 15px; bottom: 25px; }
    }
</style>
@endpush

@section('content')
{{-- WRAPPER UTAMA DENGAN CLASS content-container --}}
<div class="content-container">
    
    {{-- HEADER & FILTER SECTION --}}
    <div class="dashboard-header">
        <div class="header-content">
            <h1>Peta Digital Wilayah</h1>
            <p>Eksplorasi fasilitas umum, UMKM, dan tata ruang Sukorame.</p>
        </div>

        <div class="filter-wrapper">
            <div class="search-container">
                <i class="bi bi-search"></i>
                <input type="text" id="search-input" class="custom-input" placeholder="Cari lokasi, gedung, atau jalan...">
            </div>
            <select id="category-filter" class="custom-select">
                <option value="">Semua Kategori</option>
                {{-- Option diisi via JS --}}
            </select>
        </div>
    </div>

    {{-- MAP CONTAINER --}}
    <div class="map-wrapper">
        
        {{-- 1. LEGENDA TOMBOL (Floating Left) --}}
        <div class="legend-container">
            <button class="legend-btn" onclick="toggleLegend()" title="Buka Legenda">
                <i id="legend-icon" class="bi bi-layers-fill"></i>
            </button>
            <div id="legend-content" class="legend-content">
                <div class="legend-title">Kategori Wilayah</div>
                <div id="legend-items">
                    {{-- Item Legenda diisi JS --}}
                </div>
            </div>
        </div>

        {{-- 2. DRAWER DETAIL (Sliding Right) --}}
        <div id="info-drawer" class="info-drawer">
            <div class="drawer-header">
                <button class="close-drawer" onclick="closeDrawer()"><i class="bi bi-arrow-left"></i></button>
                <img id="drawer-img" src="" class="drawer-img" onerror="this.src='https://via.placeholder.com/400x200?text=No+Image'">
            </div>
            <div class="drawer-body">
                <span id="drawer-category" class="drawer-badge">Kategori</span>
                <h3 id="drawer-title">Nama Lokasi</h3>
                <p id="drawer-desc">Deskripsi lokasi akan muncul di sini.</p>
                
                <div class="meta-box">
                    <div class="meta-item">
                        <i class="bi bi-geo-alt" style="color: var(--primary-color)"></i>
                        <span id="drawer-coords">-</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. AREA PETA --}}
        <div id="map"></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    // --- KONFIGURASI KATEGORI ---
    const categoryConfig = {
        "Makanan & Minuman": { color: "#e57373", icon: "restaurant.svg" },
        "Perbelanjaan": { color: "#ffb74d", icon: "shop.svg" },
        "Akomodasi": { color: "#64b5f6", icon: "lodging.svg" },
        "Perumahan": { color: "#9e9e9e", icon: "home.svg" },
        "Layanan Publik & Administrasi": { color: "#4db6ac", icon: "place-of-worship.svg" },
        "Pemerintahan & Sipil": { color: "#673ab7", icon: "town-hall.svg" },
        "Kesehatan": { color: "#ff3d00", icon: "hospital-JP.svg" },
        "Pendidikan": { color: "#1565c0", icon: "college.svg" },
        "Rekreasi, Seni & Budaya": { color: "#81c784", icon: "art-gallery.svg" },
        "Alam & Lingkungan": { color: "#388e3c", icon: "zoo.svg" },
        "Otomotif & Transportasi": { color: "#ff9800", icon: "bus.svg" },
        "Agama & Spiritual": { color: "#fdd835", icon: "religious-muslim.svg" },
        "Default": { color: "#0a6847", icon: "lainnya.svg" }
    };

    let map, buildingsLayer, allFeaturesData;

    document.addEventListener('DOMContentLoaded', function () {
        
        // 1. Inisialisasi Peta
        map = L.map('map', { zoomControl: false }).setView([-7.8180, 112.0185], 15);
        L.control.zoom({ position: 'topright' }).addTo(map);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19, attribution: '© OpenStreetMap'
        }).addTo(map);

        // 2. Load Boundary (Batas Wilayah) & FIT BOUNDS (Otomatis Zoom)
        fetch("{{ asset('geojson/sukorame_boundary.geojson') }}")
            .then(res => res.json())
            .then(data => {
                const boundary = L.geoJSON(data, {
                    style: { color: "#333", weight: 3, opacity: 0.8, fillOpacity: 0.05, dashArray: '5, 5' }
                }).addTo(map);

                // --- INI KUNCINYA: Otomatis zoom ke wilayah ---
                map.fitBounds(boundary.getBounds(), { padding: [50, 50], animate: true });
            })
            .catch(e => console.log("Gagal memuat batas wilayah"));

        // 3. Generate Legenda (Hidden Awal)
        generateLegend();

        // 4. Load Data Titik (API)
        fetch('{{ route('api.bangunan.map') }}')
            .then(res => res.json())
            .then(data => {
                allFeaturesData = data;
                initMapData(data);
                populateFilter(data);
            })
            .catch(err => console.error("Gagal memuat data bangunan:", err));
            
        // 5. Load Polygon Tambahan (Sawah dll)
        loadAdditionalPolygons();

        // Event Listeners Filter
        document.getElementById('search-input').addEventListener('input', applyFilters);
        document.getElementById('category-filter').addEventListener('change', applyFilters);
    });

    // --- LOGIC LEGENDA ---
    function toggleLegend() {
        const content = document.getElementById('legend-content');
        const btn = document.querySelector('.legend-btn');
        const icon = document.getElementById('legend-icon');

        content.classList.toggle('show');
        btn.classList.toggle('active');

        if (content.classList.contains('show')) {
            icon.classList.remove('bi-layers-fill');
            icon.classList.add('bi-x-lg');
        } else {
            icon.classList.remove('bi-x-lg');
            icon.classList.add('bi-layers-fill');
        }
    }

    function generateLegend() {
        const container = document.getElementById('legend-items');
        let html = '';
        for (const [key, val] of Object.entries(categoryConfig)) {
            if(key !== "Default") {
                html += `
                    <div class="legend-item">
                        <span class="legend-color" style="background:${val.color}"></span>
                        ${key}
                    </div>`;
            }
        }
        container.innerHTML = html;
    }

    // --- RENDER MAP & CUSTOM MARKER ---
    function initMapData(geoJsonData) {
        if (buildingsLayer) map.removeLayer(buildingsLayer);

        buildingsLayer = L.geoJSON(geoJsonData, {
            pointToLayer: function (feature, latlng) {
                const cat = feature.properties.kategori;
                const config = categoryConfig[cat] || categoryConfig["Default"];
                
                // CSS Marker Bulat
                const iconHtml = `
                    <div style="
                        width: 40px; height: 40px;
                        background: ${config.color};
                        border: 2px solid white;
                        border-radius: 50%;
                        box-shadow: 0 4px 8px rgba(0,0,0,0.3);
                        display: flex; justify-content: center; align-items: center;
                        transition: transform 0.2s;
                    ">
                        <img src="/icons/${config.icon}" 
                             style="width: 20px; height: 20px; filter: brightness(0) invert(1);"
                             onerror="this.style.display='none'">
                    </div>
                `;

                return L.marker(latlng, {
                    icon: L.divIcon({
                        className: 'custom-leaflet-icon',
                        html: iconHtml,
                        iconSize: [40, 40],
                        iconAnchor: [20, 20]
                    })
                });
            },
            onEachFeature: function (feature, layer) {
                // Event Klik Marker
                layer.on('click', function(e) {
                    map.flyTo(e.latlng, 18, { duration: 1.2 }); // Animasi Zoom
                    openDrawer(feature.properties, e.latlng);
                });
            }
        }).addTo(map);
    }

    // --- DRAWER LOGIC ---
    function openDrawer(props, latlng) {
        const config = categoryConfig[props.kategori] || categoryConfig["Default"];
        
        document.getElementById('drawer-title').innerText = props.nama;
        document.getElementById('drawer-desc').innerText = props.deskripsi || "Tidak ada deskripsi tersedia.";
        
        const badge = document.getElementById('drawer-category');
        badge.innerText = props.kategori;
        badge.style.backgroundColor = config.color;

        const coords = document.getElementById('drawer-coords');
        coords.innerText = `${latlng.lat.toFixed(6)}, ${latlng.lng.toFixed(6)}`;
        
        const imgEl = document.getElementById('drawer-img');
        imgEl.src = props.foto_url ? props.foto_url : 'https://via.placeholder.com/400x200?text=No+Image';

        document.getElementById('info-drawer').classList.add('active');
    }

    function closeDrawer() {
        document.getElementById('info-drawer').classList.remove('active');
    }

    // --- FILTER & UTILS ---
    function applyFilters() {
        if (!allFeaturesData) return;
        const search = document.getElementById('search-input').value.toLowerCase();
        const category = document.getElementById('category-filter').value;
        closeDrawer(); // Tutup detail jika sedang mencari

        const filtered = allFeaturesData.features.filter(f => {
            const p = f.properties;
            const matchName = p.nama.toLowerCase().includes(search);
            const matchCat = category === "" || p.kategori === category;
            return matchName && matchCat;
        });
        initMapData({ type: "FeatureCollection", features: filtered });
    }

    function populateFilter(data) {
        const categories = new Set();
        data.features.forEach(f => { if(f.properties.kategori) categories.add(f.properties.kategori); });
        const select = document.getElementById('category-filter');
        [...categories].sort().forEach(c => {
            const opt = document.createElement('option');
            opt.value = c; opt.innerText = c;
            select.appendChild(opt);
        });
    }

    function loadAdditionalPolygons() {
        const addPoly = (url, color, name) => {
            fetch(url).then(r=>r.json()).then(d=>{
                L.geoJSON(d, {
                    style: { color: color, weight: 1, fillColor: color, fillOpacity: 0.2 }
                }).bindPopup(`<b>${name}</b>`).addTo(map);
            }).catch(e=>{});
        }
        addPoly("{{ asset('geojson/sawah.geojson') }}", "#4caf50", "Area Persawahan");
        addPoly("{{ asset('geojson/pemukiman.geojson') }}", "#3f51b5", "Area Pemukiman");
    }
</script>
@endpush