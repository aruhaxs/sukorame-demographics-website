@extends('layouts.admin')

@section('title', 'Edit Data Penduduk')

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const rwSelect = document.getElementById('rw_id');
        const rtSelect = document.getElementById('rt_id');

        const loadRtOptions = (rwId) => {
            rtSelect.innerHTML = '<option value="">-- Memuat RT... --</option>';
            rtSelect.disabled = true;

            if (rwId) {
                fetch(`/api/get-rt-by-rw/${rwId}`)
                    .then(response => response.json())
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

                        const rtToSelect = "{{ old('rt_id', $penduduk->rt_id) }}";
                        if (rtToSelect) {
                            rtSelect.value = rtToSelect;
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
        };

        // Event listener untuk perubahan pada dropdown RW
        rwSelect.addEventListener('change', function () {
            loadRtOptions(this.value);
        });

        const initialRwValue = rwSelect.value;
        if (initialRwValue) {
            loadRtOptions(initialRwValue);
        }
    });
</script>
@endpush

@section('content')

<style>
    .form-card { background-color: var(--color-bg-card); padding: 2.5rem; border-radius: 12px; max-width: 800px; margin: 2rem auto; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4); }
    .form-card h2 { color: var(--color-primary-light); text-align: center; margin-top: 0; margin-bottom: 2rem; font-size: 2rem; }
    .form-group { margin-bottom: 1.5rem; }
    .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; }
    .form-control, .form-select { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #334e6f; background-color: #0b1a2e; color: #f0f4f8; box-sizing: border-box; font-size: 1rem; }
    .form-control:focus, .form-select:focus { border-color: var(--color-primary-light); outline: none; box-shadow: 0 0 0 3px rgba(122, 186, 120, 0.3); }
    .form-select:disabled { background-color: #1c2b3a; cursor: not-allowed; }
    .btn-submit { background-color: var(--color-primary-light); color: var(--color-primary-dark); padding: 12px 25px; border: none; border-radius: 8px; font-weight: 700; font-size: 1rem; cursor: pointer; transition: background-color 0.3s; width: 100%; }
    .btn-submit:hover { background-color: #96c997; }
    .alert-error { color: #ffdddd; background-color: #dc3545; padding: 8px 12px; border-radius: 4px; margin-top: 5px; font-size: 0.85rem; }
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
</style>

<div class="form-card">
    <h2>Edit Data Penduduk</h2>

    <form action="{{ route('admin.penduduk.update', $penduduk) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="nama_lengkap">Nama Lengkap</label>
            <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control"
                value="{{ old('nama_lengkap', $penduduk->nama_lengkap) }}" required>
            @error('nama_lengkap') <div class="alert-error">{{ $message }}</div> @enderror
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label for="nik">NIK (16 Digit)</label>
                <input type="text" name="nik" id="nik" class="form-control"
                    value="{{ old('nik', $penduduk->nik) }}" required maxlength="16" minlength="16" pattern="\d{16}"
                    title="NIK harus terdiri dari 16 digit angka.">
                @error('nik') <div class="alert-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label for="nomor_kk">Nomor KK (16 Digit)</label>
                <input type="text" name="nomor_kk" id="nomor_kk" class="form-control"
                    value="{{ old('nomor_kk', $penduduk->nomor_kk) }}" maxlength="16" minlength="16" pattern="\d{16}"
                    title="Nomor KK harus terdiri dari 16 digit angka.">
                @error('nomor_kk') <div class="alert-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label for="tanggal_lahir">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control"
                    value="{{ old('tanggal_lahir', $penduduk->tanggal_lahir?->format('Y-m-d')) }}" required>
                @error('tanggal_lahir') <div class="alert-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label for="jenis_kelamin">Jenis Kelamin</label>
                <select name="jenis_kelamin" id="jenis_kelamin" class="form-select" required>
                    @php $jk = old('jenis_kelamin', $penduduk->jenis_kelamin); @endphp
                    <option value="Laki-laki" {{ $jk == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="Perempuan" {{ $jk == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
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
                        <option value="{{ $rw->id }}" {{ old('rw_id', $penduduk->rw_id) == $rw->id ? 'selected' : '' }}>
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
            <input type="text" name="pekerjaan" id="pekerjaan" class="form-control"
                value="{{ old('pekerjaan', $penduduk->pekerjaan) }}">
            @error('pekerjaan') <div class="alert-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="pendidikan_terakhir">Pendidikan Terakhir</label>
            <input type="text" name="pendidikan_terakhir" id="pendidikan_terakhir" class="form-control"
                value="{{ old('pendidikan_terakhir', $penduduk->pendidikan_terakhir) }}">
            @error('pendidikan_terakhir') <div class="alert-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="agama">Agama</label>
            <select name="agama" id="agama" class="form-select">
                @php $agama = old('agama', $penduduk->agama); @endphp
                <option value="">-- Pilih Agama --</option>
                <option value="Islam" {{ $agama == 'Islam' ? 'selected' : '' }}>Islam</option>
                <option value="Kristen" {{ $agama == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                <option value="Katolik" {{ $agama == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                <option value="Hindu" {{ $agama == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                <option value="Buddha" {{ $agama == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                <option value="Konghucu" {{ $agama == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
            </select>
            @error('agama') <div class="alert-error">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn-submit">SIMPAN PERUBAHAN</button>
    </form>
</div>
@endsection