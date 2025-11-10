@extends('layouts.admin')

@section('title', 'Tambah Data Penduduk')

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const rwSelect = document.getElementById('rw_id');
        const rtSelect = document.getElementById('rt_id');
        
        // Ambil default values dari variabel PHP
        const defaultKelurahan = @json($defaultValues['kelurahan'] ?? 'Sukorame');
        const defaultKecamatan = @json($defaultValues['kecamatan'] ?? 'Mojoroto');
        const defaultKabupaten = @json($defaultValues['kabupaten'] ?? 'Kota Kediri');
        const defaultProvinsi = @json($defaultValues['provinsi'] ?? 'Jawa Timur');
        const defaultKodepos = @json($defaultValues['kodepos'] ?? '64119');
        const defaultKewarganegaraan = @json($defaultValues['kewarganegaraan'] ?? 'Indonesia');

        // Set default values untuk field
        document.getElementById('kelurahan').value = "{{ old('kelurahan', $defaultValues['kelurahan'] ?? 'Sukorame') }}";
        document.getElementById('kecamatan').value = "{{ old('kecamatan', $defaultValues['kecamatan'] ?? 'Mojoroto') }}";
        document.getElementById('kabupaten').value = "{{ old('kabupaten', $defaultValues['kabupaten'] ?? 'Kota Kediri') }}";
        document.getElementById('provinsi').value = "{{ old('provinsi', $defaultValues['provinsi'] ?? 'Jawa Timur') }}";
        document.getElementById('kodepos').value = "{{ old('kodepos', $defaultValues['kodepos'] ?? '64119') }}";
        document.getElementById('kewarganegaraan').value = "{{ old('kewarganegaraan', $defaultValues['kewarganegaraan'] ?? 'Indonesia') }}";
        
        // Set default kewarganegaraan (fallback check)
        const kewarganegaraanInput = document.getElementById('kewarganegaraan');
        if (kewarganegaraanInput.value === '') {
            kewarganegaraanInput.value = defaultKewarganegaraan;
        }

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
                            const oldRtValue = "{{ old('rt_id') }}";
                            data.forEach(rt => {
                                const option = document.createElement('option');
                                option.value = rt.id;
                                option.setAttribute('data-nomor', rt.nomor_rt);
                                option.textContent = `RT ${rt.nomor_rt}`;
                                if (rt.id == oldRtValue) {
                                    option.selected = true;
                                }
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

        const oldRwValue = "{{ old('rw_id') }}";
        if (oldRwValue) {
            rwSelect.value = oldRwValue;
            // Panggil event change agar data RT ikut terload saat ada error validasi
            setTimeout(() => {
                 rwSelect.dispatchEvent(new Event('change'));
            }, 100);
            
        }

        const tglLahirInput = document.getElementById('tanggal_lahir');
        if (tglLahirInput) {
            tglLahirInput.max = new Date().toISOString().split("T")[0];
        }
    });
</script>
@endpush

@section('content')

<style>
    /* ... (CSS style yang sudah Anda berikan) ... */
    .form-card { background-color: var(--color-bg-card); padding: 2.5rem; border-radius: 12px; max-width: 800px; margin: 2rem auto; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4); }
    .form-card h2 { color: var(--color-primary-light); text-align: center; margin-top: 0; margin-bottom: 2rem; font-size: 2rem; }
    .form-group { margin-bottom: 1.5rem; }
    .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; }
    .form-control, .form-select { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #334e6f; background-color: #0b1a2e; color: #f0f4f8; box-sizing: border-box; font-size: 1rem; }
    .form-control:focus, .form-select:focus { border-color: var(--color-primary-light); outline: none; box-shadow: 0 0 0 3px rgba(122, 186, 120, 0.3); }
    .form-select:disabled { background-color: #1c2b3a; cursor: not-allowed; }
    .btn-submit { background-color: var(--color-primary-light); color: var(--color-primary-dark); padding: 12px 25px; border: none; border-radius: 8px; font-weight: 700; font-size: 1rem; cursor: pointer; transition: background-color 0.3s; width: 100%; margin-top: 1rem; }
    .btn-submit:hover { background-color: #96c997; }
    .alert { padding: 1rem; margin-bottom: 1.5rem; border-radius: 8px; color: white; }
    .alert-success { background-color: #28a745; }
    .alert-danger, .validation-summary { background-color: #dc3545; color: white; padding: 1rem; margin-bottom: 1.5rem; border-radius: 8px; }
    .validation-summary ul { margin: 0.5rem 0 0 1rem; padding: 0; }
    .alert-error { color: #ffdddd; background-color: #dc3545; padding: 8px 12px; border-radius: 4px; margin-top: 5px; font-size: 0.85rem; }
    .grid-2 { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; }
    .grid-3 { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.5rem; }
    .form-group label sup { color: #dc3545; font-weight: 600; }
    .form-section-title { margin-top: 2.5rem; margin-bottom: 1rem; color: var(--color-primary-light); border-bottom: 1px solid #334e6f; padding-bottom: 0.5rem; font-size: 1.25rem; }
</style>

<div class="form-card">
    <h2>Tambah Data Penduduk Baru</h2>

    @if ($errors->any())
        <div class="validation-summary">
            <strong>Data yang dimasukkan tidak valid:</strong>
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif
    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    {{-- PENTING: ensure enctype="multipart/form-data" is present for file uploads --}}
    <form action="{{ route('admin.penduduk.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <h3 class="form-section-title">Data Diri</h3>
        <div class="form-group">
            <label for="nik">NIK (16 Digit) <sup>*</sup></label>
            <input type="text" name="nik" id="nik" class="form-control" value="{{ old('nik') }}" required maxlength="16" minlength="16" pattern="\d{16}" title="NIK harus 16 digit angka.">
            @error('nik') <div class="alert-error">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label for="nomor_kk">Nomor KK (16 Digit)</label>
            <input type="text" name="nomor_kk" id="nomor_kk" class="form-control" value="{{ old('nomor_kk') }}" maxlength="16" minlength="16" pattern="\d{16}" title="Nomor KK harus 16 digit angka.">
            @error('nomor_kk') <div class="alert-error">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label for="nama_lengkap">Nama Lengkap <sup>*</sup></label>
            <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control" value="{{ old('nama_lengkap') }}" required>
            @error('nama_lengkap') <div class="alert-error">{{ $message }}</div> @enderror
        </div>
        <div class="grid-2">
             <div class="form-group">
                <label for="tempat_lahir">Tempat Lahir</label>
                <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control" value="{{ old('tempat_lahir') }}">
                @error('tempat_lahir') <div class="alert-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label for="tanggal_lahir">Tanggal Lahir <sup>*</sup></label>
                <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir') }}" required >
                @error('tanggal_lahir') <div class="alert-error">{{ $message }}</div> @enderror
            </div>
        </div>
        <div class="form-group">
             <label for="jenis_kelamin">Jenis Kelamin <sup>*</sup></label>
             <select name="jenis_kelamin" id="jenis_kelamin" class="form-select" required>
                 <option value="">-- Pilih --</option>
                 <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                 <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
             </select>
             @error('jenis_kelamin') <div class="alert-error">{{ $message }}</div> @enderror
        </div>
        <div class="grid-2">
            <div class="form-group">
                <label for="status_perkawinan">Status Perkawinan</label>
                <select name="status_perkawinan" id="status_perkawinan" class="form-select">
                    <option value="">-- Pilih --</option>
                    <option value="Belum Kawin" {{ old('status_perkawinan') == 'Belum Kawin' ? 'selected' : '' }}>Belum Kawin</option>
                    <option value="Kawin" {{ old('status_perkawinan') == 'Kawin' ? 'selected' : '' }}>Kawin</option>
                    <option value="Cerai Hidup" {{ old('status_perkawinan') == 'Cerai Hidup' ? 'selected' : '' }}>Cerai Hidup</option>
                    <option value="Cerai Mati" {{ old('status_perkawinan') == 'Cerai Mati' ? 'selected' : '' }}>Cerai Mati</option>
                </select>
                @error('status_perkawinan') <div class="alert-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label for="agama">Agama</label>
                <select name="agama" id="agama" class="form-select">
                    <option value="">-- Pilih --</option>
                    <option value="Islam" {{ old('agama') == 'Islam' ? 'selected' : '' }}>Islam</option>
                    <option value="Kristen" {{ old('agama') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                    <option value="Katolik" {{ old('agama') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                    <option value="Hindu" {{ old('agama') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                    <option value="Buddha" {{ old('agama') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                    <option value="Konghucu" {{ old('agama') == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                </select>
                @error('agama') <div class="alert-error">{{ $message }}</div> @enderror
            </div>
        </div>
        <div class="form-group">
             <label for="pendidikan_terakhir">Pendidikan Terakhir</label>
             <select name="pendidikan_terakhir" id="pendidikan_terakhir" class="form-select">
                <option value="">-- Pilih --</option>
                <option value="Tidak Sekolah" {{ old('pendidikan_terakhir') == 'Tidak Sekolah' ? 'selected' : '' }}>Tidak Sekolah</option>
                <option value="SD" {{ old('pendidikan_terakhir') == 'SD' ? 'selected' : '' }}>SD</option>
                <option value="SMP" {{ old('pendidikan_terakhir') == 'SMP' ? 'selected' : '' }}>SMP</option>
                <option value="SMA/SMK" {{ old('pendidikan_terakhir') == 'SMA/SMK' ? 'selected' : '' }}>SMA/SMK</option>
                <option value="Diploma" {{ old('pendidikan_terakhir') == 'Diploma' ? 'selected' : '' }}>Diploma</option>
                <option value="S1" {{ old('pendidikan_terakhir') == 'S1' ? 'selected' : '' }}>S1</option>
                <option value="S2" {{ old('pendidikan_terakhir') == 'S2' ? 'selected' : '' }}>S2</option>
                <option value="S3" {{ old('pendidikan_terakhir') == 'S3' ? 'selected' : '' }}>S3</option>
             </select>
             @error('pendidikan_terakhir') <div class="alert-error">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label for="pekerjaan">Pekerjaan</label>
            <input type="text" name="pekerjaan" id="pekerjaan" class="form-control" value="{{ old('pekerjaan') }}">
            @error('pekerjaan') <div class="alert-error">{{ $message }}</div> @enderror
        </div>
         <div class="form-group">
            <label for="kewarganegaraan">Kewarganegaraan</label>
            <input type="text" name="kewarganegaraan" id="kewarganegaraan" class="form-control" value="{{ old('kewarganegaraan') }}">
            @error('kewarganegaraan') <div class="alert-error">{{ $message }}</div> @enderror
        </div>

        <h3 class="form-section-title">Kontak</h3>
         <div class="grid-2">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}">
                @error('email') <div class="alert-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label for="no_hp">No. HP</label>
                <input type="tel" name="no_hp" id="no_hp" class="form-control" value="{{ old('no_hp') }}">
                @error('no_hp') <div class="alert-error">{{ $message }}</div> @enderror
            </div>
         </div>

        <h3 class="form-section-title">Alamat</h3>
        <div class="form-group">
            <label for="jalan">Jalan <sup>*</sup></label>
            <input type="text" name="jalan" id="jalan" class="form-control" value="{{ old('jalan') }}" required>
            @error('jalan') <div class="alert-error">{{ $message }}</div> @enderror
        </div>
        <div class="grid-2">
            <div class="form-group">
                <label for="rw_id">RW <sup>*</sup></label>
                <select name="rw_id" id="rw_id" class="form-select" required>
                    <option value="">-- Pilih RW --</option>
                    @foreach($rws as $rw)
                        <option value="{{ $rw->id }}" data-nomor="{{ $rw->nomor_rw }}" {{ old('rw_id') == $rw->id ? 'selected' : '' }}>
                            RW {{ $rw->nomor_rw }}
                        </option>
                    @endforeach
                </select>
                @error('rw_id') <div class="alert-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label for="rt_id">RT <sup>*</sup></label>
                <select name="rt_id" id="rt_id" class="form-select" required {{ old('rw_id') ? '' : 'disabled' }}>
                    <option value="">-- Pilih RW Dulu --</option>
                </select>
                @error('rt_id') <div class="alert-error">{{ $message }}</div> @enderror
            </div>
        </div>
         <div class="form-group">
            <label for="kelurahan">Kelurahan/Desa <sup>*</sup></label>
            <input type="text" name="kelurahan" id="kelurahan" class="form-control" value="{{ old('kelurahan') }}" required>
            @error('kelurahan') <div class="alert-error">{{ $message }}</div> @enderror
        </div>
        <div class="grid-3">
            <div class="form-group">
                <label for="kecamatan">Kecamatan <sup>*</sup></label>
                <input type="text" name="kecamatan" id="kecamatan" class="form-control" value="{{ old('kecamatan') }}" required>
                @error('kecamatan') <div class="alert-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label for="kabupaten">Kabupaten/Kota <sup>*</sup></label>
                <input type="text" name="kabupaten" id="kabupaten" class="form-control" value="{{ old('kabupaten') }}" required>
                @error('kabupaten') <div class="alert-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label for="provinsi">Provinsi <sup>*</sup></label>
                <input type="text" name="provinsi" id="provinsi" class="form-control" value="{{ old('provinsi') }}" required>
                @error('provinsi') <div class="alert-error">{{ $message }}</div> @enderror
            </div>
        </div>
         <div class="form-group">
            <label for="kodepos">Kode Pos <sup>*</sup></label>
            <input type="text" name="kodepos" id="kodepos" class="form-control" value="{{ old('kodepos') }}" pattern="\d*" title="Hanya angka">
            @error('kodepos') <div class="alert-error">{{ $message }}</div> @enderror
        </div>

        <h3 class="form-section-title">Dokumen Pendukung</h3>
        <div class="grid-2">
            {{-- INPUT UNTUK FOTO KTP --}}
            <div class="form-group">
                <label for="foto_ktp_url">Foto KTP</label>
                <input type="file" name="foto_ktp_url" id="foto_ktp_url" class="form-control" accept="image/*">
                @error('foto_ktp_url') <div class="alert-error">{{ $message }}</div> @enderror
            </div>
            {{-- INPUT UNTUK FOTO KK --}}
            <div class="form-group">
                <label for="foto_kk_url">Foto Kartu Keluarga (KK)</label>
                <input type="file" name="foto_kk_url" id="foto_kk_url" class="form-control" accept="image/*">
                @error('foto_kk_url') <div class="alert-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <button type="submit" class="btn-submit">SIMPAN DATA</button>
    </form>
</div>
@endsection
