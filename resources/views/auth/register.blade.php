<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Global Supply Chain</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-dark: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.7);
            --accent-cyan: #03fbff;
            --accent-indigo: #6366f1;
        }

        body {
            background-color: var(--bg-dark);
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 15px;
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(3, 251, 255, 0.1) 0px, transparent 50%);
        }

        .register-card {
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 480px;
            padding: 40px;
        }

        .brand-title {
            font-weight: 800;
            font-size: 24px;
            color: #ffffff;
        }

        .brand-title span {
            color: var(--accent-cyan);
        }

        .form-control {
            background-color: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #ffffff;
            border-radius: 12px;
            padding: 12px 16px;
        }

        .form-control:focus {
            background-color: rgba(15, 23, 42, 0.8);
            border-color: var(--accent-cyan);
            color: #ffffff;
            box-shadow: 0 0 0 4px rgba(3, 251, 255, 0.15);
        }

        .form-control::placeholder {
            color: #64748b;
        }

        .btn-glow {
            background: linear-gradient(135deg, #4f46e5 0%, #0284c7 100%);
            color: #ffffff;
            font-weight: 700;
            border: none;
            border-radius: 12px;
            padding: 14px;
            transition: all 0.3s ease;
        }

        .btn-glow:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px -5px rgba(79, 70, 229, 0.5);
            color: #ffffff;
        }
    </style>
</head>
<body>

    <div class="register-card">
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle mb-3" style="background: rgba(79, 70, 229, 0.2);">
                <i class="fas fa-user-plus fa-2x" style="color: var(--accent-cyan);"></i>
            </div>
            <h4 class="brand-title mb-1">Daftar Akun <span>Baru</span></h4>
            <p class="text-secondary small">Buat akun untuk mengakses platform Global Supply Chain</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger border-0 rounded-3 mb-4" style="background: rgba(239, 68, 68, 0.15); color: #f87171;">
                <ul class="mb-0 ps-3 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="name" class="form-label text-light small fw-semibold">Nama Lengkap</label>
                <div class="input-group">
                    <span class="input-group-text border-end-0" style="background: rgba(15, 23, 42, 0.6); border-color: rgba(255, 255, 255, 0.15); color: #64748b;">
                        <i class="fas fa-user"></i>
                    </span>
                    <input type="text" name="name" id="name" class="form-control border-start-0" placeholder="John Doe" value="{{ old('name') }}" required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label text-light small fw-semibold">Alamat Email</label>
                <div class="input-group">
                    <span class="input-group-text border-end-0" style="background: rgba(15, 23, 42, 0.6); border-color: rgba(255, 255, 255, 0.15); color: #64748b;">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input type="email" name="email" id="email" class="form-control border-start-0" placeholder="nama@email.com" value="{{ old('email') }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label text-light small fw-semibold">Kata Sandi</label>
                <div class="input-group">
                    <span class="input-group-text border-end-0" style="background: rgba(15, 23, 42, 0.6); border-color: rgba(255, 255, 255, 0.15); color: #64748b;">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" name="password" id="password" class="form-control border-start-0" placeholder="Minimal 8 karakter" required>
                </div>
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label text-light small fw-semibold">Konfirmasi Kata Sandi</label>
                <div class="input-group">
                    <span class="input-group-text border-end-0" style="background: rgba(15, 23, 42, 0.6); border-color: rgba(255, 255, 255, 0.15); color: #64748b;">
                        <i class="fas fa-shield-check"></i>
                    </span>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control border-start-0" placeholder="Ulangi kata sandi" required>
                </div>
            </div>

            <button type="submit" class="btn btn-glow w-100 mb-3">
                <i class="fas fa-user-plus me-2"></i> Daftar Sekarang
            </button>
        </form>

        <div class="text-center mt-3 pt-3 border-top border-secondary border-opacity-25">
            <span class="text-secondary small">Sudah memiliki akun?</span>
            <a href="{{ route('login') }}" class="small fw-bold ms-1 text-decoration-none" style="color: var(--accent-cyan);">
                Masuk di sini <i class="fas fa-arrow-right ms-1" style="font-size: 10px;"></i>
            </a>
        </div>
    </div>

</body>
</html>
