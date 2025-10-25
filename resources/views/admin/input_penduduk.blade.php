@extends('layouts.admin')

@section('title', 'Tambah Data Penduduk')

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const rwSelect = document.getElementById('rw_id');
        const rtSelect = document.getElementById('rt_id');

        rwSelect.addEventListener('change', function () {
            const rwId = this.value;
            rtSelect.innerHTML = '<option value="">-- Memuat RT... --</option>';
            rtSelect.disabled = true;

            if (rwId) {
                fetch(`/api/get-rt-by-rw/${rwId}`)
                    .then(response => {
                        if (!response.ok) throw new Error('Gagal mengambil data RT.');
                        return response.json();
                    })
                    .then(data => {
                        rtSelect.disabled = false;
                        rtSelect.innerHTML = '<option value="">-- Pilih RT --</option>';
                        if (data.length > 0) {
                            data.forEach(rt => {
                                const option = document.createElement('option');
                                option.value = rt.id;
                                option.textContent = `RT ${rt.nomor_rt}`;
                                rtSelect.appendChild(option);
                            });
                        } else {
                            rtSelect.innerHTML = '<option value="">-- Tidak ada data RT --</option>';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching RT data:', error);
                        rtSelect.innerHTML = '<option value="">-- Gagal memuat data RT --</option>';
                    });
            } else {
                rtSelect.innerHTML = '<option value="">-- Pilih RW Terlebih Dahulu --</option>';
                rtSelect.disabled = true;
            }
        });

        // ✅ PERBAIKAN: Gunakan old('rw_id') dan old('rt_id')
        const oldRwValue = "{{ old('rw_id') }}";
        const oldRtValue = "{{ old('rt_id') }}";
        if (oldRwValue) {
            rwSelect.value = oldRwValue;
            // Memicu event 'change' untuk memuat data RT yang sesuai
            rwSelect.dispatchEvent(new Event('change'));

            // Beri sedikit waktu agar data RT selesai dimuat sebelum memilih nilai lama
            setTimeout(() => {
                // Tunggu fetch selesai, baru set nilai RT
                if (oldRtValue) rtSelect.value = oldRtValue;
            }, 500); // 500ms delay
        }
    });
</script>
@endpush

@section('content')

<style>
    /* Style Anda sudah bagus, tidak perlu diubah */
    .form-card { background-color: var(--color-bg-card); padding: 2.5rem; border-radius: 12px; max-width: 800px; margin: 2rem auto; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4); }
    .form-card h2 { color: var(--color-primary-light); text-align: center; margin-top: 0; margin-bottom: 2rem; font-size: 2rem; }
    .form-group { margin-bottom: 1.5rem; }
    .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; }
    .form-control, .form-select { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #334e6f; background-color: #0b1a2e; color: #f0f4f8; box-sizing: border-box; font-size: 1rem; }
    .form-control:focus, .form-select:focus { border-color: var(--color-primary-light); outline: none; box-shadow: 0 0 0 3px rgba(122, 186, 120, 0.3); }
    .form-select:disabled { background-color: #1c2b3a; cursor: not-allowed; }
    .btn-submit { background-color: var(--color-primary-light); color: var(--color-primary-dark); padding: 12px 25px; border: none; border-radius: 8px; font-weight: 700; font-size: 1rem; cursor: pointer; transition: background-color 0.3s; width: 100%; }
    .btn-submit:hover { background-color: #96c997; }
    .alert { padding: 1rem; margin-bottom: 1.5rem; border-radius: 8px; color: white; }
    .alert-success { background-color: #28a745; }
    .alert-danger { background-color: #dc3545; }
    .alert-error { color: #ffdddd; background-color: #dc3545; padding: 8px 12px; border-radius: 4px; margin-top: 5px; font-size: 0.85rem; }
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
</style>

<div class="form-card">
    <h2>Tambah Data Penduduk Baru</h2>

    @if ($errors->any())
        <div class="validation-summary">
            <strong>Data yang dimasukkan tidak valid:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <form action="{{ route('admin.penduduk.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="nama_lengkap">Nama Lengkap</label>
            <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control"
                value="{{ old('nama_lengkap') }}" required>
            @error('nama_lengkap') <div class="alert-error">{{ $message }}</div> @enderror
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label for="nik">NIK (16 Digit)</label>
                <input type="text" name="nik" id="nik" class="form-control"
                    value="{{ old('nik') }}" required maxlength="16" minlength="16" pattern="\d{16}"
                    title="NIK harus terdiri dari 16 digit angka.">
                @error('nik') <div class="alert-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label for="nomor_kk">Nomor KK (16 Digit)</label>
                <input type="text" name="nomor_kk" id="nomor_kk" class="form-control"
                    value="{{ old('nomor_kk') }}" maxlength="16" minlength="16" pattern="\d{16}"
                    title="Nomor KK harus terdiri dari 16 digit angka.">
                @error('nomor_kk') <div class="alert-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label for="tanggal_lahir">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control"
                    value="{{ old('tanggal_lahir') }}" required>
                @error('tanggal_lahir') <div class="alert-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label for="jenis_kelamin">Jenis Kelamin</label>
                <select name="jenis_kelamin" id="jenis_kelamin" class="form-select" required>
                    <option value="">-- Pilih Jenis Kelamin --</option>
                    <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                </select>
                @error('jenis_kelamin') <div class="alert-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label for="rw_id">RW</label>
                <select name="rw_id" id="rw_id" class="form-select" required>
                    <option value="">-- Pilih RW --</option>
                    @foreach($rws as $rw)
                        <option value="{{ $rw->id }}" {{ old('rw_id') == $rw->id ? 'selected' : '' }}>
                            RW {{ $rw->nomor_rw }}
                        </option>
                    @endforeach
                </select>
                @error('rw_id') <div class="alert-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label for="rt_id">RT</label>
                <select name="rt_id" id="rt_id" class="form-select" required disabled>
                    <option value="">-- Pilih RW Terlebih Dahulu --</option>
                </select>
                @error('rt_id') <div class="alert-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="pekerjaan">Pekerjaan</label>
            <input type="text" name="pekerjaan" id="pekerjaan" class="form-control" value="{{ old('pekerjaan') }}">
            @error('pekerjaan') <div class="alert-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="pendidikan_terakhir">Pendidikan Terakhir</label>
            <input type="text" name="pendidikan_terakhir" id="pendidikan_terakhir" class="form-control" value="{{ old('pendidikan_terakhir') }}">
            @error('pendidikan_terakhir') <div class="alert-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="agama">Agama</label>
            <select name="agama" id="agama" class="form-select">
                <option value="">-- Pilih Agama --</option>
                <option value="Islam" {{ old('agama') == 'Islam' ? 'selected' : '' }}>Islam</option>
                <option value="Kristen" {{ old('agama') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                <option value="Katolik" {{ old('agama') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                <option value="Hindu" {{ old('agama') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                <option value="Buddha" {{ old('agama') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                <option value="Konghucu" {{ old('agama') == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
            </select>
            @error('agama') <div class="alert-error">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn-submit">SIMPAN DATA</button>
    </form>
</div>
@endsection