@extends('layouts.admin')

@section('title', 'Data Komoditas')

@section('content')
<style>
:root {
    --primary-color: #0a6847;
    --primary-light: #7aba78;
    --card-bg: #1b263b;
    --text-light: #f0f8ff;
    --text-subtle: #a0aec0;
    --border-color: #4a5568;
    --danger-color: #e53e3e;
}

/* HEADER */
.header-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; }
.header-actions { display: flex; gap: 1rem; align-items: center; }
.search-input { background-color: var(--card-bg); border: 1px solid var(--border-color); color: var(--text-light); padding: 10px 15px; border-radius: 8px; width: 250px; font-size: 0.9rem; transition: all 0.2s ease; }
.search-input:focus { border-color: var(--primary-light); box-shadow: 0 0 0 3px rgba(122,186,120,0.3); outline: none; }
.btn-tambah-data { background-color: var(--primary-color); color: var(--text-light); padding: 10px 20px; border-radius: 8px; font-weight: 600; text-decoration: none; transition: background-color 0.2s; }
.btn-tambah-data:hover { background-color: #0d8259; }

/* STAT CARD */
.stat-cards-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem; }
.stat-card-small { background-color: var(--card-bg); padding: 1.5rem; border-radius: 12px; text-align: center; border-left: 5px solid var(--primary-color); }
.stat-card-small p { margin:0 0 0.5rem 0; color: var(--text-subtle); text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; }
.stat-card-small .value { font-size: 2.25rem; font-weight: 700; color: var(--primary-light); }

/* CARD LIST */
.card-list-wrapper { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px,1fr)); gap: 1.5rem; }
.data-card-item {
    background-color: var(--card-bg);
    border-radius: 12px;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.data-card-item:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.4); }
.card-header { margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center; }
.card-header h3 { margin:0; font-size: 1.3rem; color: var(--text-light); }
.category-tag { background-color: var(--primary-color); color: var(--text-light); padding: 6px 12px; border-radius: 8px; font-weight:700; font-size:0.85rem; }

/* CARD BODY */
.card-body { flex-grow: 1; margin-top: 0.5rem; }
.card-body p { margin:0.4rem 0; color: var(--text-light); font-size: 0.95rem; display:flex; }
.card-body p strong { width: 90px; display:inline-block; color: var(--text-subtle); }

/* CARD ACTIONS */
.card-footer { margin-top:1rem; display:flex; gap:0.5rem; justify-content:flex-end; }
.btn-icon { background:#2d3748; border:none; cursor:pointer; padding:6px 10px; font-size:0.9rem; color:var(--text-subtle); border-radius:6px; display:inline-flex; align-items:center; justify-content:center; transition: all 0.2s ease; text-decoration:none; }
.btn-icon:hover { color: var(--text-light); background-color: var(--primary-color); }
.btn-icon-delete:hover { background-color: var(--danger-color); color:#fff; }
</style>

<div class="header-bar">
    <h1 class="admin-title">Data Komoditas</h1>
    <div class="header-actions">
        <input type="text" class="search-input" placeholder="Cari komoditas..." id="search-komoditas">
        <a href="{{ route('admin.komoditas.create') }}" class="btn-tambah-data">Tambah Komoditas</a>
    </div>
</div>

<div class="stat-cards-grid">
    <div class="stat-card-small">
        <p>Total Komoditas</p>
        <span class="value">{{ $totalKomoditas ?? 0}}</span>
    </div>
    <div class="stat-card-small">
        <p>Total Kategori</p>
        <span class="value">{{ $totalKategori }}</span>
    </div>
</div>

<div class="card-list-wrapper" id="komoditas-wrapper">
    @forelse($komoditas as $k)
    <div class="data-card-item" data-nama="{{ strtolower($k->nama_komoditas) }}" data-id="{{ $k->id }}">
        <div class="card-header">
            <h3>{{ $k->nama_komoditas }}</h3>
            <span class="category-tag">{{ $k->kategori }}</span>
        </div>
        <div class="card-body">
            <p><strong>Produksi:</strong> {{ $k->produksi ?? '-' }}</p>
            <p><strong>Periode:</strong> {{ $k->periode ?? '-' }}</p>
            <p><strong>Produsen:</strong> {{ $k->produsen ?? '-' }}</p>
            <p><strong>Lokasi:</strong> {{ $k->lokasi ?? '-' }}</p>
            <p><strong>Harga:</strong> 
                @if(is_numeric($k->harga)) Rp {{ number_format($k->harga,0,',','.') }} @else - @endif
            </p>
        </div>
        <div class="card-footer">
            <a href="{{ route('admin.komoditas.edit', $k) }}" class="btn-icon" title="Edit">✏️</a>
            <button class="btn-icon btn-icon-delete" title="Hapus" onclick="deleteKomoditas({{ $k->id }})">🗑️</button>
        </div>
    </div>
    @empty
    <div style="grid-column:1/-1; text-align:center; color: var(--text-subtle);">Belum ada data komoditas.</div>
    @endforelse
</div>

@endsection

@push('scripts')
<script>
function deleteKomoditas(id){
    if(!confirm('Yakin ingin menghapus data ini?')) return;
    fetch("{{ url('admin/komoditas') }}/"+id, {
        method:'DELETE',
        headers:{
            'X-CSRF-TOKEN':'{{ csrf_token() }}',
            'Accept':'application/json'
        }
    })
    .then(res => {
        if(res.ok){
            // hapus card dari DOM
            const card = document.querySelector('.data-card-item[data-id="'+id+'"]');
            if(card) card.remove();
        } else {
            alert('Gagal menghapus data.');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Terjadi kesalahan.');
    });
}

// Optional: search filter
document.getElementById('search-komoditas').addEventListener('input', function(){
    const term = this.value.toLowerCase();
    document.querySelectorAll('.data-card-item').forEach(card=>{
        const name = card.getAttribute('data-nama');
        card.style.display = name.includes(term) ? 'flex' : 'none';
    });
});
</script>
@endpush
