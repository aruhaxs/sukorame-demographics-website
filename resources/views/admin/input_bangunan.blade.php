@extends('layouts.admin')

@section('title', 'Tambah Data Bangunan')

@push('styles')
{{-- Leaflet CSS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    :root {
        --color-bg-card: #0b1a2e;
        --color-input-bg: #162a43;
        --color-border: #334e6f;
        --color-text-main: #f0f4f8;
        --color-primary: #7aba78;
        --color-primary-hover: #96c997;
        --color-danger: #dc3545;
    }

    /* --- PERBAIKAN Z-INDEX (Agar Peta tidak menutupi Navbar) --- */
    .navbar, header, nav, .main-header, .topbar {
        position: relative;
        z-index: 9999 !important; /* Memastikan navbar selalu paling atas */
    }

    /* Layout Utama: Grid 2 Kolom */
    .form-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        max-width: 1400px;
        margin: 2rem auto;
        position: relative;
        z-index: 1; /* Konten di bawah navbar */
    }

    /* Responsif untuk Mobile */
    @media (max-width: 992px) {
        .form-container { grid-template-columns: 1fr; }
    }

    .form-card { 
        background-color: var(--color-bg-card); 
        padding: 2rem; 
        border-radius: 12px; 
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4); 
        height: fit-content;
    }
    
    .form-card h2 { 
        color: var(--color-primary); 
        margin-top: 0; 
        margin-bottom: 1.5rem; 
        font-size: 1.5rem; 
        border-bottom: 1px solid var(--color-border);
        padding-bottom: 10px;
    }

    .form-group { margin-bottom: 1.2rem; }
    .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: #b0c4de; }
    
    .form-control, .form-select, textarea.form-control { 
        width: 100%; padding: 10px 12px; border-radius: 8px; 
        border: 1px solid var(--color-border); background-color: var(--color-input-bg); 
        color: var(--color-text-main); box-sizing: border-box; 
    }
    .form-select:disabled { background-color: #2d3748; cursor: not-allowed; }

    .btn-submit { 
        background-color: var(--color-primary); color: #051b11; padding: 14px; 
        border: none; border-radius: 8px; font-weight: 700; cursor: pointer; 
        width: 100%; margin-top: 1rem; transition: background 0.3s;
    }
    .btn-submit:hover { background-color: var(--color-primary-hover); }

    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }

    /* --- STYLE PETA --- */
    #map {
        height: 600px;
        width: 100%;
        border-radius: 8px;
        border: 2px solid var(--color-border);
        z-index: 0; /* Peta di lapisan paling bawah */
    }
    
    /* --- STATUS PENCARIAN --- */
    .loading-status { font-size: 0.85rem; margin-top: 8px; font-weight: 600; }
    .st-searching { color: #f1c40f; display: none; } /* Kuning */
    .st-found { color: #2ecc71; display: none; } /* Hijau */
    .st-error { color: #e74c3c; display: none; } /* Merah */
</style>
@endpush

@section('content')

<div class="form-container">
    
    {{-- KOLOM KIRI: FORM INPUT DATA --}}
    <div class="form-card">
        <h2>Data Bangunan</h2>

        <form action="{{ route('admin.bangunan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="nama_bangunan">Nama Bangunan *</label>
                <input type="text" name="nama_bangunan" id="nama_bangunan" class="form-control"
                    value="{{ old('nama_bangunan') }}" required placeholder="Contoh: Toko Barokah">
            </div>

            <div class="form-group">
                <label for="kategori">Kategori *</label>
                <select name="kategori" id="kategori" class="form-select" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach(['Makanan & Minuman', 'Perbelanjaan', 'Akomodasi', 'Perumahan', 'Layanan Publik & Administrasi', 'Pemerintahan & Sipil', 'Kesehatan', 'Pendidikan', 'Rekreasi, Seni & Budaya', 'Alam & Lingkungan', 'Otomotif & Transportasi', 'Agama & Spiritual'] as $kat)
                        <option value="{{ $kat }}" {{ old('kategori') == $kat ? 'selected' : '' }}>{{ $kat }}</option>
                    @endforeach
                </select>
            </div>

            {{-- FORM ALAMAT DENGAN INDIKATOR STATUS --}}
            <div class="form-group">
                <label for="alamat">Alamat Lengkap *</label>
                <textarea name="alamat" id="alamat" class="form-control" rows="3" required placeholder="Contoh: Jl. Veteran No. 10, Mojoroto, Kediri">{{ old('alamat') }}</textarea>
                
                {{-- Indikator Status Pencarian --}}
                <div class="loading-status">
                    <span id="msg-search" class="st-searching">🔄 Sedang mencari lokasi di peta...</span>
                    <span id="msg-found" class="st-found">✅ Lokasi ditemukan! Marker telah dipindah.</span>
                    <span id="msg-error" class="st-error">❌ Lokasi tidak ditemukan. Coba kurangi detail alamat (misal: hanya nama jalan & kota).</span>
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label for="rw_id">Nomor RW *</label>
                    <select name="rw_id" id="rw_id" class="form-select" required>
                        <option value="">-- Pilih --</option>
                        @foreach($rws as $rw)
                            <option value="{{ $rw->id }}" {{ old('rw_id') == $rw->id ? 'selected' : '' }}>RW {{ $rw->nomor_rw }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="rt_id">Nomor RT *</label>
                    <select name="rt_id" id="rt_id" class="form-select" required disabled>
                        <option value="">-- Pilih RW Dulu --</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="deskripsi">Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi" class="form-control" rows="2">{{ old('deskripsi') }}</textarea>
            </div>

            <div class="form-group">
                <label for="foto">Foto Lokasi</label>
                <input type="file" name="foto" id="foto" class="form-control">
            </div>

            {{-- Hidden Inputs (Readonly) --}}
            <div class="grid-2" style="background: rgba(0,0,0,0.2); padding: 10px; border-radius: 8px;">
                <div class="form-group" style="margin-bottom:0;">
                    <label style="font-size:0.85rem;">Latitude (Otomatis)</label>
                    <input type="text" name="latitude" id="latitude" class="form-control"
                        value="{{ old('latitude') }}" readonly style="background:#0f2238; color:#aaa;" required>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label style="font-size:0.85rem;">Longitude (Otomatis)</label>
                    <input type="text" name="longitude" id="longitude" class="form-control"
                        value="{{ old('longitude') }}" readonly style="background:#0f2238; color:#aaa;" required>
                </div>
            </div>

            <button type="submit" class="btn-submit">SIMPAN DATA</button>
        </form>
    </div>

    {{-- KOLOM KANAN: PETA --}}
    <div class="form-card">
        <h2>Peta Lokasi</h2>
        <div id="map"></div>
        <div style="text-align: center; margin-top: 10px; color: #8fa1b8; font-size: 0.9rem;">
            

[Image of Map marker icon]
 Pin akan berpindah otomatis saat alamat ditemukan.<br>
            Anda bisa menggeser pin secara manual untuk akurasi lebih baik.
        </div>
    </div>

</div>

@endsection

@push('scripts')
{{-- Load Leaflet JS --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        
        // --- 1. SETUP MAP ---
        const defaultLat = -7.812345; 
        const defaultLng = 112.012345;

        // Ambil nilai lama jika ada (saat validasi error)
        let curLat = document.getElementById('latitude').value || defaultLat;
        let curLng = document.getElementById('longitude').value || defaultLng;

        const map = L.map('map').setView([curLat, curLng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        let marker = L.marker([curLat, curLng], {draggable: true}).addTo(map);

        function updateCoords(lat, lng) {
            document.getElementById('latitude').value = lat.toFixed(7);
            document.getElementById('longitude').value = lng.toFixed(7);
        }

        // Event: Marker digeser manual
        marker.on('dragend', function(e) {
            const pos = marker.getLatLng();
            updateCoords(pos.lat, pos.lng);
            map.panTo(pos);
        });

        // Event: Peta diklik
        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            updateCoords(e.latlng.lat, e.latlng.lng);
        });
        
        // Render ulang peta agar ukurannya pas
        setTimeout(() => { map.invalidateSize(); }, 500);

        // --- 2. LOGIKA OTOMATISASI ALAMAT (DEBOUNCE) ---
        const alamatInput = document.getElementById('alamat');
        const msgSearch = document.getElementById('msg-search');
        const msgFound = document.getElementById('msg-found');
        const msgError = document.getElementById('msg-error');
        let timeout = null;

        alamatInput.addEventListener('input', function() {
            const query = this.value;
            
            // Reset Status
            clearTimeout(timeout);
            msgSearch.style.display = 'none';
            msgFound.style.display = 'none';
            msgError.style.display = 'none';

            if(query.length < 5) return; // Jangan cari jika terlalu pendek

            // Tunggu 1.5 detik setelah user selesai mengetik
            timeout = setTimeout(() => {
                msgSearch.style.display = 'block';

                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`)
                    .then(res => res.json())
                    .then(data => {
                        msgSearch.style.display = 'none';
                        
                        if(data && data.length > 0) {
                            const lat = parseFloat(data[0].lat);
                            const lon = parseFloat(data[0].lon);
                            
                            // Update Map & Marker
                            map.setView([lat, lon], 17);
                            marker.setLatLng([lat, lon]);
                            updateCoords(lat, lon);
                            
                            // Tampilkan pesan sukses
                            msgFound.style.display = 'block';
                            setTimeout(() => { msgFound.style.display = 'none'; }, 3000);
                        } else {
                            // Tampilkan pesan gagal
                            msgError.style.display = 'block';
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        msgSearch.style.display = 'none';
                    });
            }, 1500); 
        });

        // --- 3. LOGIKA RW/RT ---
        const rwSelect = document.getElementById('rw_id');
        const rtSelect = document.getElementById('rt_id');

        if (rwSelect.value) fetchRT(rwSelect.value);

        rwSelect.addEventListener('change', function() { fetchRT(this.value); });

        function fetchRT(rwId) {
            rtSelect.innerHTML = '<option value="">Memuat...</option>';
            rtSelect.disabled = true;
            if (rwId) {
                fetch(`/api/get-rt-by-rw/${rwId}`)
                    .then(r => r.json())
                    .then(data => {
                        rtSelect.innerHTML = '<option value="">-- Pilih RT --</option>';
                        const oldRt = "{{ old('rt_id') }}";
                        data.forEach(rt => {
                            let sel = (rt.id == oldRt) ? 'selected' : '';
                            rtSelect.innerHTML += `<option value="${rt.id}" ${sel}>RT ${rt.nomor_rt}</option>`;
                        });
                        rtSelect.disabled = false;
                    })
                    .catch(() => rtSelect.innerHTML = '<option value="">Gagal</option>');
            } else {
                rtSelect.innerHTML = '<option value="">-- Pilih RW Dulu --</option>';
            }
        }
    });
</script>
@endpush