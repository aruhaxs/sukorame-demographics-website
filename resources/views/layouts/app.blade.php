<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | Kelurahan Sukorame</title>

    {{-- CSS Utama Anda --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Tambahkan ini di <head> -->
    <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    />


    {{-- Chart.js (jika perlu) --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- ====================================================== --}}
    {{--    BARU: Tambahkan @stack('styles') DI SINI        --}}
    {{-- ====================================================== --}}
    @stack('styles')
    {{-- Ini akan memuat CSS dari halaman anak (seperti CSS Leaflet) --}}
    <style>
        nav, .navbar, header {
            position: sticky; /* Membuat navbar menempel saat scroll */
            top: 0;           /* Menempel di paling atas */
            z-index: 9999;    /* LEBIH TINGGI dari z-index peta (1000) */
            background-color: var(--color-primary-dark, #001f3f); /* Pastikan navbar punya warna background, tidak transparan */
        }

        .leaflet-popup-content img.popup-img-fixed {
        width: 100%;             /* Lebar mengikuti container popup */
        height: auto;            /* Tinggi menyesuaikan proporsi */
        max-height: 200px;       /* BATAS KETINGGIAN: Ubah angka ini jika masih terlalu besar/kecil */
        object-fit: cover;       /* Agar gambar tidak gepeng jika aspek rasionya beda (opsional) */
        display: block;          /* Menghapus spasi di bawah gambar inline */
        margin-top: 10px;        /* Memberi jarak sedikit dari teks di atasnya */
        border-radius: 4px;      /* Pemanis: sudut sedikit membulat */
        }

        /* Opsional: Membatasi tinggi deskripsi jika teksnya sangat panjang */
        .leaflet-popup-content p {
            max-height: 100px;
            overflow-y: auto;
        }
    </style>

</head>
<body>

    @include('partials.navbar')

    {{-- Konten halaman akan masuk di sini --}}
    @yield('content')

    @include('partials.footer')

    {{-- Skrip dari halaman anak akan dimuat di sini --}}
    @stack('scripts')

    {{-- Skrip utama Anda (jika ada) --}}
    <script src="{{ asset('js/carousel.js') }}"></script>
</body>
</html>
