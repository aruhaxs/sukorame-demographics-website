@extends('layouts.admin')

@section('title', 'Data Bangunan & Peta Wilayah')

@push('styles')
{{-- Library Leaflet & Fonts --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"/>

<style>
    /* == TEMA DARK NAVY (Sesuai Screenshot) == */
    :root {
        --color-primary: #10b981; /* Hijau */
        --color-bg-dark: #0f172a; /* Background Gelap */
        --color-bg-card: #1e293b; /* Card Navy */
        --color-text-light: #f1f5f9;
        --color-text-muted: #94a3b8;
        --color-border: #334155;
    }

    body { font-family: 'Poppins', sans-serif; background-color: var(--color-bg-dark); color: var(--color-text-light); }

    /* Header & Tombol */
    .header-bar { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 1px solid var(--color-border); }
    .admin-title { font-size: 1.8rem; font-weight: 600; color: var(--color-text-light); margin: 0; }
    .btn-tambah-data { background-color: var(--color-primary); color: #000; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px; }
    .btn-tambah-data:hover { background-color: #059669; color: #fff; }

    /* Summary Cards */
    .summary-cards-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
    .summary-card { background-color: var(--color-bg-card); padding: 1.5rem; border-radius: 12px; text-align: center; border: 1px solid var(--color-border); position: relative; overflow: hidden; }
    .summary-card::before { content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 100%; background-color: var(--color-primary); }
    .summary-card h4 { margin: 0 0 0.5rem 0; color: var(--color-text-muted); text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; }
    .summary-card .value { font-size: 2rem; font-weight: 700; color: var(--color-text-light); }

    /* == MASTER CONTROLS (Filter Gabungan) == */
    .master-controls-container {
        background-color: var(--color-bg-card);
        padding: 1.5rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        border: 1px solid var(--color-border);
        display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
    }
    .control-group { flex: 1; min-width: 250px; position: relative; }
    .master-input { width: 100%; background-color: #0f172a; border: 1px solid var(--color-border); color: var(--color-text-light); padding: 12px 16px 12px 45px; border-radius: 8px; outline: none; font-size: 0.95rem; }
    .master-input:focus { border-color: var(--color-primary); }
    .master-icon { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--color-text-muted); }
    .master-select { background-color: #0f172a; border: 1px solid var(--color-border); color: var(--color-text-light); padding: 12px 16px; border-radius: 8px; outline: none; cursor: pointer; min-width: 200px; }

    /* == MAP WRAPPER == */
    .map-wrapper {
        position: relative; width: 100%; height: 500px;
        border-radius: 12px; overflow: hidden;
        border: 1px solid var(--color-border);
        margin-bottom: 2rem;
        z-index: 1;
        background-color: #aad3df; /* Placeholder warna laut */
    }
    #map { width: 100%; height: 100%; }

    /* Legenda & Drawer */
    .legend-container { position: absolute; bottom: 20px; left: 20px; z-index: 900; }
    .legend-btn { width: 45px; height: 45px; background: var(--color-bg-card); border: 1px solid var(--color-border); color: var(--color-text-light); border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 1.2rem; box-shadow: 0 4px 10px rgba(0,0,0,0.5); }
    .legend-content { background: var(--color-bg-card); padding: 15px; border-radius: 8px; border: 1px solid var(--color-border); margin-bottom: 10px; display: none; color: var(--color-text-light); width: 220px; box-shadow: 0 4px 15px rgba(0,0,0,0.5); }
    .legend-content.show { display: block; }
    .legend-item { display: flex; align-items: center; margin-bottom: 8px; font-size: 0.85rem; }
    .legend-color { width: 15px; height: 15px; border-radius: 4px; margin-right: 10px; }

    .info-drawer {
        position: absolute; top: 0; right: 0; width: 320px; height: 100%;
        background: var(--color-bg-card); z-index: 1000;
        transform: translateX(100%); transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-left: 1px solid var(--color-border); overflow-y: auto; color: var(--color-text-light);
        box-shadow: -5px 0 20px rgba(0,0,0,0.5);
    }
    .info-drawer.active { transform: translateX(0); }
    .drawer-header { position: relative; height: 180px; background: #334155; }
    .drawer-img { width: 100%; height: 100%; object-fit: cover; }
    .close-drawer { position: absolute; top: 10px; left: 10px; background: rgba(0,0,0,0.6); border: none; color: white; width: 32px; height: 32px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(4px); }
    .drawer-body { padding: 1.5rem; }
    .drawer-badge { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: bold; color: #fff; display: inline-block; margin-bottom: 10px; }

    /* Tabel */
    .data-table-section { background-color: var(--color-bg-card); border-radius: 12px; border: 1px solid var(--color-border); overflow: hidden; }
    .data-table-header { padding: 1.5rem; border-bottom: 2px solid var(--color-primary); }
    .data-table-header h2 { color: var(--color-primary); font-size: 1.2rem; margin: 0; font-weight: 600; }
    .data-table-wrapper { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    thead { background-color: #162032; }
    th { padding: 1rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
    td { padding: 1rem 1.5rem; border-bottom: 1px solid var(--color-border); color: var(--color-text-light); font-size: 0.9rem; }
    tbody tr:hover { background-color: rgba(255,255,255,0.03); }
    .tag-kategori { padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; color: #fff; background-color: var(--color-primary); }
    .btn-action { padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; border: 1px solid var(--color-border); background: #0f172a; color: var(--color-text-light); text-decoration: none; display: flex; align-items: center; justify-content: center; }
    .btn-action:hover { border-color: var(--color-primary); color: var(--color-primary); }
    .btn-action.delete:hover { border-color: #ef4444; color: #ef4444; }
    .pagination-wrapper { padding: 1.5rem; display: flex; justify-content: center; }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="header-bar">
    <h1 class="admin-title">Dashboard Bangunan</h1>
    <a href="{{ route('admin.bangunan.create') }}" class="btn-tambah-data">
        <i class="bi bi-plus-lg"></i> Tambah Data
    </a>
</div>

{{-- Summary Cards --}}
<div class="summary-cards-grid">
    <div class="summary-card">
        <h4>Total Bangunan</h4>
        <div class="value">{{ $totalBangunan }}</div>
    </div>
    @foreach($categoryCounts as $kategori => $total)
    <div class="summary-card">
        <h4>{{ $kategori }}</h4>
        <div class="value">{{ $total }}</div>
    </div>
    @endforeach
</div>

{{-- MASTER CONTROL (Filter Gabungan) --}}
<div class="master-controls-container">
    <div class="control-group">
        <i class="bi bi-search master-icon"></i>
        <input type="text" id="masterSearch" class="master-input" placeholder="Cari data bangunan...">
    </div>
    <div class="control-group" style="flex: 0 0 auto;">
        <select id="masterCategory" class="master-select">
            <option value="">Semua Kategori</option>
        </select>
    </div>
</div>

{{-- Peta --}}
<div class="map-wrapper">
    {{-- Legenda --}}
    <div class="legend-container">
        <div id="legend-content" class="legend-content">
            <div style="font-weight:bold; margin-bottom:12px; border-bottom:1px solid #334155; padding-bottom:8px;">Legenda Wilayah</div>
            {{-- Manual Legend Polygons --}}
            <div class="legend-item"><span class="legend-color" style="background:#22c55e; opacity:0.6;"></span>Sawah/Kebun</div>
            <div class="legend-item"><span class="legend-color" style="background:#3b82f6; opacity:0.6;"></span>Pemukiman</div>
            <hr style="border-color:#334155; margin:10px 0;">
            {{-- Auto Legend Markers --}}
            <div id="legend-items"></div>
        </div>
        <button class="legend-btn" onclick="toggleLegend()"><i class="bi bi-map"></i></button>
    </div>

    {{-- Drawer Detail --}}
    <div id="info-drawer" class="info-drawer">
        <div class="drawer-header">
            <button class="close-drawer" onclick="closeDrawer()"><i class="bi bi-x-lg"></i></button>
            <img id="drawer-img" src="" class="drawer-img" onerror="this.src='https://via.placeholder.com/400x200?text=No+Image'">
        </div>
        <div class="drawer-body">
            <span id="drawer-category" class="drawer-badge">Kategori</span>
            <h3 id="drawer-title" style="margin-top:0; margin-bottom:0.5rem;">Judul</h3>
            <div style="font-size:0.85rem; color:#94a3b8; margin-bottom:1rem;">
                <i class="bi bi-geo-alt-fill" style="color:var(--color-primary)"></i> <span id="drawer-coords">-</span>
            </div>
            <p id="drawer-desc" style="color:#cbd5e1; font-size:0.9rem; line-height:1.6; border-top:1px solid #334155; padding-top:1rem;">Deskripsi</p>
            <a href="#" id="drawer-edit-btn" class="btn-action" style="margin-top:1.5rem; padding:10px;">Edit Data Ini</a>
        </div>
    </div>

    <div id="map"></div>
</div>

{{-- Tabel Data --}}
<section class="data-table-section">
    <div class="data-table-header">
        <h2>Daftar Bangunan</h2>
    </div>
    <div class="data-table-wrapper">
        <table id="dataTable">
            <thead>
                <tr>
                    <th>Nama Bangunan</th>
                    <th>Kategori</th>
                    <th>Lokasi (RW/RT)</th>
                    <th style="text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bangunans as $bangunan)
                <tr class="data-row" data-category="{{ $bangunan->kategori }}">
                    <td class="name-cell">{{ $bangunan->nama_bangunan }}</td>
                    <td><span class="tag-kategori">{{ $bangunan->kategori }}</span></td>
                    <td>RW {{ optional($bangunan->rw)->nomor_rw }} / RT {{ optional($bangunan->rt)->nomor_rt }}</td>
                    <td>
                        <div style="display:flex; gap:8px; justify-content:flex-end;">
                            <a href="{{ route('admin.bangunan.edit', $bangunan) }}" class="btn-action">Edit</a>
                            <form action="{{ route('admin.bangunan.destroy', $bangunan) }}" method="POST" onsubmit="return confirm('Hapus?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-action delete"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center; padding:2rem;">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrapper">{{ $bangunans->links() }}</div>
</section>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    const categoryConfig = {
        "Makanan & Minuman": { color: "#f43f5e", icon: "restaurant.svg" },
        "Perbelanjaan": { color: "#f59e0b", icon: "shop.svg" },
        "Akomodasi": { color: "#3b82f6", icon: "lodging.svg" },
        "Perumahan": { color: "#64748b", icon: "home.svg" },
        "Layanan Publik": { color: "#14b8a6", icon: "town-hall.svg" },
        "Kesehatan": { color: "#ef4444", icon: "hospital-JP.svg" },
        "Pendidikan": { color: "#8b5cf6", icon: "college.svg" },
        "Wisata": { color: "#10b981", icon: "art-gallery.svg" },
        "Tempat Ibadah": { color: "#eab308", icon: "religious-muslim.svg" },
        "Default": { color: "#10b981", icon: "lainnya.svg" }
    };

    let map, buildingsLayer, allMapData;

    document.addEventListener('DOMContentLoaded', function () {
        // 1. Inisialisasi Peta
        map = L.map('map', { zoomControl: false }).setView([-7.8180, 112.0185], 14);
        L.control.zoom({ position: 'topright' }).addTo(map);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

        // FIX: Paksa refresh ukuran peta agar tidak error saat load
        setTimeout(() => { map.invalidateSize(); }, 300);

        // 2. LOAD POLYGON (Warna Warni)
        // A. Batas Wilayah & Zoom Otomatis (Center Fix)
        fetch("{{ asset('geojson/sukorame_boundary.geojson') }}")
            .then(r => r.json())
            .then(data => {
                const boundary = L.geoJSON(data, { 
                    style: { color: "#ef4444", weight: 3, opacity: 0.8, fill: false, dashArray: '5, 8' } 
                }).addTo(map);
                
                // INI KUNCINYA: Zoom ke boundary setelah data selesai dimuat
                map.fitBounds(boundary.getBounds(), { padding: [20, 20] });
            })
            .catch(e => console.error("Gagal load boundary"));

        // B. Area Sawah (Hijau)
        fetch("{{ asset('geojson/sawah.geojson') }}")
            .then(r => r.json())
            .then(d => {
                L.geoJSON(d, { style: { color: "#22c55e", weight: 1, fillColor: "#22c55e", fillOpacity: 0.2 } }).addTo(map);
            }).catch(e => {});

        // C. Area Pemukiman (Biru)
        fetch("{{ asset('geojson/pemukiman.geojson') }}")
            .then(r => r.json())
            .then(d => {
                L.geoJSON(d, { style: { color: "#3b82f6", weight: 1, fillColor: "#3b82f6", fillOpacity: 0.15 } }).addTo(map);
            }).catch(e => {});


        // 3. LOAD DATA BANGUNAN (Marker)
        fetch('{{ route('api.bangunan.map') }}')
            .then(res => res.json())
            .then(data => {
                allMapData = data;
                initMapMarkers(data);
                populateFilter(data);
            });

        generateLegend();

        // 4. LOGIC FILTER GABUNGAN
        const mSearch = document.getElementById('masterSearch');
        const mCat = document.getElementById('masterCategory');

        function runFilter() {
            const key = mSearch.value.toLowerCase();
            const cat = mCat.value;

            // Filter Peta
            if(allMapData) {
                const filtered = allMapData.features.filter(f => {
                    return (f.properties.nama.toLowerCase().includes(key) || 
                           (f.properties.deskripsi && f.properties.deskripsi.toLowerCase().includes(key))) &&
                           (cat === "" || f.properties.kategori === cat);
                });
                initMapMarkers({ type: "FeatureCollection", features: filtered });
            }

            // Filter Tabel
            document.querySelectorAll('.data-row').forEach(row => {
                const name = row.querySelector('.name-cell').innerText.toLowerCase();
                const rowCat = row.dataset.category;
                const show = name.includes(key) && (cat === "" || rowCat === cat);
                row.style.display = show ? '' : 'none';
            });
            
            closeDrawer();
        }

        mSearch.addEventListener('input', runFilter);
        mCat.addEventListener('change', runFilter);
    });

    // --- Helper Functions ---
    function initMapMarkers(data) {
        if(buildingsLayer) map.removeLayer(buildingsLayer);
        buildingsLayer = L.geoJSON(data, {
            pointToLayer: (feature, latlng) => {
                const c = categoryConfig[feature.properties.kategori] || categoryConfig["Default"];
                const html = `<div style="width:36px;height:36px;background:${c.color};border-radius:50%;display:flex;align-items:center;justify-content:center;border:2px solid white;box-shadow:0 3px 5px rgba(0,0,0,0.3);">
                                <img src="/icons/${c.icon}" style="width:18px;filter:brightness(0) invert(1);" onerror="this.style.display='none'">
                              </div>`;
                return L.marker(latlng, { icon: L.divIcon({ className: 'custom-icon', html: html, iconSize:[36,36], iconAnchor:[18,18]}) });
            },
            onEachFeature: (f, l) => { l.on('click', (e) => { openDrawer(f.properties, e.latlng); map.flyTo(e.latlng, 17); }); }
        }).addTo(map);
    }

    function populateFilter(data) {
        const cats = new Set(data.features.map(f => f.properties.kategori));
        const sel = document.getElementById('masterCategory');
        [...cats].sort().forEach(c => {
            let opt = document.createElement('option');
            opt.value = c; opt.innerText = c;
            sel.appendChild(opt);
        });
    }

    function openDrawer(p, ll) {
        document.getElementById('drawer-title').innerText = p.nama;
        document.getElementById('drawer-desc').innerText = p.deskripsi || "-";
        document.getElementById('drawer-category').innerText = p.kategori;
        const conf = categoryConfig[p.kategori] || categoryConfig["Default"];
        document.getElementById('drawer-category').style.background = conf.color;
        document.getElementById('drawer-coords').innerText = ll.lat.toFixed(5) + ", " + ll.lng.toFixed(5);
        document.getElementById('drawer-img').src = p.foto_url || "";
        
        if(p.id) {
            document.getElementById('drawer-edit-btn').href = "{{ route('admin.bangunan.edit', '999') }}".replace('999', p.id);
            document.getElementById('drawer-edit-btn').style.display = 'flex';
        }
        document.getElementById('info-drawer').classList.add('active');
    }
    
    function closeDrawer() { document.getElementById('info-drawer').classList.remove('active'); }
    function toggleLegend() { document.getElementById('legend-content').classList.toggle('show'); }
    function generateLegend() {
        const div = document.getElementById('legend-items');
        let h = '';
        for(let [k,v] of Object.entries(categoryConfig)){
            if(k!=="Default") h += `<div class="legend-item"><span class="legend-color" style="background:${v.color}"></span>${k}</div>`;
        }
        div.innerHTML = h;
    }
</script>
@endpush