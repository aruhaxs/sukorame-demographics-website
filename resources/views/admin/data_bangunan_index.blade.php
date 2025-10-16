@extends('layouts.admin')

@section('title', 'Data Bangunan & Peta Wilayah')

{{-- Menambahkan library Leaflet.js di head --}}
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""/>
<style>
    /* == PINE GREEN THEME (Konsisten) == */
    :root {
        --color-primary: #0a6847;
        --color-primary-light: #7aba78;
        --color-bg-dark: #0d1b2a;
        --color-bg-card: #1b263b;
        --color-text-light: #f0f8ff;
        --color-text-subtle: #a0aec0;
        --color-border: #4a5568;
        --color-danger: #e53e3e;
    }

    /* == Bagian Header Halaman == */
    .header-bar { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 2rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;}
    .admin-title { font-size: 1.8rem; font-weight: 600; color: var(--color-text-light); }
    .btn-tambah-data { background-color: var(--color-primary); color: var(--color-text-light); padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: background-color 0.2s; }
    .btn-tambah-data:hover { background-color: #0d8259; }

    /* == Peta & Card Ringkasan == */
    #map { height: 500px; width: 100%; border-radius: 12px; margin-bottom: 2.5rem; box-shadow: 0 4px 10px rgba(0,0,0,0.3); }
    .summary-cards-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2.5rem; }
    .summary-card { background-color: var(--color-bg-card); padding: 1.5rem; border-radius: 12px; text-align: center; border-left: 5px solid var(--color-primary); }
    .summary-card h4 { margin: 0 0 0.5rem 0; color: var(--color-text-subtle); text-transform: uppercase; font-size: 0.9rem; letter-spacing: 0.5px; }
    .summary-card .value { font-size: 2.5rem; font-weight: 700; color: var(--color-primary-light); }
    .leaflet-popup-content-wrapper { background: var(--color-bg-card); color: var(--color-text-light); border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.4); }
    .leaflet-popup-content-wrapper .leaflet-popup-content { line-height: 1.6; }
    .leaflet-popup-content-wrapper .popup-title { font-size: 1.2rem; font-weight: bold; color: var(--color-primary-light); margin-bottom: 8px; border-bottom: 1px solid var(--color-border); padding-bottom: 5px; }
    .leaflet-popup-content-wrapper .popup-category { font-size: 0.8rem; font-weight: bold; background-color: var(--color-primary); color: var(--color-text-light); padding: 3px 8px; border-radius: 12px; display: inline-block; margin-bottom: 8px; }
    .leaflet-popup-content-wrapper img { max-width: 100%; height: auto; border-radius: 6px; margin-top: 10px; }
    .leaflet-popup-tip { background: var(--color-bg-card); }

    /* == STYLING TABEL PROFESIONAL == */
    .data-table-section {
        background-color: var(--color-bg-card);
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
    }
    .data-table-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
    .data-table-header h2 { color: var(--color-primary-light); font-size: 1.5rem; margin: 0; }
    .data-table-wrapper { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    thead tr { border-bottom: 2px solid var(--color-primary); }
    th { padding: 12px 15px; text-align: left; font-weight: 600; color: var(--color-primary-light); text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; }
    tbody tr { border-bottom: 1px solid var(--color-border); transition: background-color 0.2s ease; }
    tbody tr:last-child { border-bottom: none; }
    tbody tr:hover { background-color: rgba(255, 255, 255, 0.05); }
    td { padding: 15px; vertical-align: middle; color: var(--color-text-light); }
    .tag-kategori { background-color: var(--color-primary); color: #fff; padding: 4px 10px; border-radius: 15px; font-size: 0.75rem; font-weight: 600; }
    .btn-aksi { padding: 6px 14px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.85rem; border: none; cursor: pointer; transition: all 0.2s ease; display: inline-block; margin-right: 8px; }
    .btn-aksi.edit { background-color: #2d3748; color: var(--color-text-subtle); }
    .btn-aksi.edit:hover { background-color: var(--color-primary); color: #fff; }
    .btn-aksi.hapus { background-color: #2d3748; color: var(--color-text-subtle); }
    .btn-aksi.hapus:hover { background-color: var(--color-danger); color: #fff; }
    td[colspan="4"] { text-align: center; padding: 3rem; color: var(--color-text-subtle); }

    /* == STYLING PAGINATION == */
    .pagination-wrapper { margin-top: 2rem; }
    .pagination { display: flex; justify-content: center; list-style: none; padding: 0; }
    .page-item .page-link { color: var(--color-text-subtle); background-color: #2d3748; border: 1px solid var(--color-border); padding: 8px 14px; margin: 0 3px; border-radius: 6px; text-decoration: none; transition: all 0.2s ease; }
    .page-item.active .page-link { background-color: var(--color-primary); color: #fff; border-color: var(--color-primary); }
    .page-item:not(.disabled) .page-link:hover { background-color: var(--color-primary-light); color: var(--color-bg-dark); border-color: var(--color-primary-light); }
    .page-item.disabled .page-link { color: #6b7280; background-color: var(--color-bg-card); border-color: var(--color-border); cursor: not-allowed; }
</style>
@endpush

@section('content')

<div class="header-bar">
    <h1 class="admin-title">PETA WILAYAH & DATA BANGUNAN</h1>
    <a href="{{ route('admin.bangunan.create') }}" class="btn-tambah-data">Tambah Data Bangunan</a>
</div>

{{-- Card Ringkasan Kategori --}}
<div class="summary-cards-grid">
    <div class="summary-card">
        <h4>Total Bangunan</h4>
        <span class="value">{{ $totalBangunan }}</span>
    </div>
    @foreach($categoryCounts as $kategori => $total)
    <div class="summary-card">
        <h4>{{ $kategori }}</h4>
        <span class="value">{{ $total }}</span>
    </div>
    @endforeach
</div>

{{-- Container untuk Peta Interaktif --}}
<div id="map"></div>

{{-- Tabel Data Bangunan --}}
<section class="data-table-section">
    <div class="data-table-header">
        <h2>Daftar Bangunan</h2>
    </div>
    <div class="data-table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Nama Bangunan</th>
                    <th>Kategori</th>
                    <th>Lokasi (RW/RT)</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bangunans as $bangunan)
                <tr>
                    <td>{{ $bangunan->nama_bangunan }}</td>
                    <td><span class="tag-kategori">{{ $bangunan->kategori }}</span></td>
                    <td>
                        RW {{ optional($bangunan->rw)->nomor_rw ?? 'N/A' }} /
                        RT {{ optional($bangunan->rt)->nomor_rt ?? 'N/A' }}
                    </td>
                    <td>
                        <a href="{{ route('admin.bangunan.edit', $bangunan) }}" class="btn-aksi edit">Edit</a>
                        <form action="{{ route('admin.bangunan.destroy', $bangunan) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-aksi hapus">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4">Belum ada data bangunan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{-- ✅ PERBAIKAN: Menggunakan variabel $bangunans (plural) --}}
    <div class="pagination-wrapper">
        {{ $bangunans->links() }}
    </div>
</section>
@endsection

{{-- Menambahkan script Leaflet.js di akhir body --}}
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin=""></script>

<script>
    const mapCenter = [-7.8180, 112.0185];
    const map = L.map('map').setView(mapCenter, 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    const bangunanData = @json($bangunansForMap);

    bangunanData.forEach(function(bangunan) {
        if (bangunan.latitude && bangunan.longitude) {
            const marker = L.marker([bangunan.latitude, bangunan.longitude]).addTo(map);
            let popupContent = `
                <div class="popup-title">${bangunan.nama_bangunan}</div>
                <div class="popup-category">${bangunan.kategori}</div>
                <p>${bangunan.deskripsi || 'Tidak ada deskripsi.'}</p>
            `;
            if (bangunan.foto) {
                const imageUrl = `{{ asset('storage') }}/${bangunan.foto}`;
                popupContent += `<img src="${imageUrl}" alt="Foto ${bangunan.nama_bangunan}">`;
            }
            marker.bindPopup(popupContent);
        }
    });
</script>
@endpush