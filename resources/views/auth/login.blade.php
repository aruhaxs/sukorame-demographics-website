<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa; /* abu-abu sangat terang */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
        }
        .login-card {
            background: #ffffff;
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            padding: 2.5rem;
            width: 100%;
            max-width: 420px;
            text-align: center;
        }
        .login-card h3 {
            font-weight: 600;
            color: #212529; /* teks gelap */
        }
        .input-group-text {
            background-color: #e9ecef;
            border-color: #ced4da;
            color: #495057;
        }
        .form-control {
            border-radius: 6px;
            border: 1px solid #ced4da;
            background-color: #ffffff;
            padding: 0.6rem 0.8rem;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #86b7fe;
            background-color: #ffffff;
        }
        .btn-primary {
            border-radius: 6px;
            background-color: #0d6efd; /* biru bootstrap */
            border: none;
            transition: all 0.3s;
            font-weight: 500;
        }
        .btn-primary:hover {
            background-color: #0b5ed7; /* biru lebih gelap saat hover */
        }
        .brand-logo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-bottom: 1rem;
            background-color: #f1f3f5; /* abu terang */
            padding: 10px;
        }
        label {
            font-weight: 500;
            color: #212529;
        }
        .fade-in {
            animation: fadeIn 0.8s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="login-card fade-in">
        <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Admin" class="brand-logo">
        <h3 class="mb-4">Login Administrator</h3>

        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            @if($errors->has('email') && !$errors->has('password') && count($errors->all()) == 1)
                <div class="alert alert-danger text-start" role="alert">
                    {{ $errors->first('email') }}
                </div>
            @endif

            <div class="mb-3 text-start">
                <label for="email" class="form-label">Alamat Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                           id="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                </div>
                @error('email')
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3 text-start">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                           id="password" name="password" required autocomplete="current-password">
                </div>
                @error('password')
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-check mb-3 text-start">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">Ingat Saya</label>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Masuk Sekarang</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
