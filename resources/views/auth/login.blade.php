<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login | AbsensiApp</title>
    <meta name="description" content="Masuk ke panel AbsensiApp Sekolah" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .wrapper {
            display: flex;
            width: 100%;
            max-width: 900px;
            min-height: 540px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.12);
        }

        /* ===== LEFT SIDE ===== */
        .left {
            flex: 1;
            background: linear-gradient(145deg, #2563eb, #1d4ed8);
            padding: 48px 44px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        /* decorative circles */
        .left::before {
            content: '';
            position: absolute;
            width: 300px; height: 300px;
            border-radius: 50%;
            background: rgba(255,255,255,0.07);
            top: -80px; right: -80px;
        }
        .left::after {
            content: '';
            position: absolute;
            width: 200px; height: 200px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
            bottom: -60px; left: -40px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative; z-index: 1;
        }
        .brand-icon {
            width: 44px; height: 44px;
            background: rgba(255,255,255,0.2);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
        }
        .brand-name {
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .left-content { position: relative; z-index: 1; }

        .left-title {
            font-size: 1.9rem;
            font-weight: 700;
            line-height: 1.25;
            margin-bottom: 14px;
            letter-spacing: -0.03em;
        }
        .left-sub {
            font-size: 0.9rem;
            opacity: 0.75;
            line-height: 1.6;
            margin-bottom: 32px;
        }

        .feat-list { list-style: none; display: flex; flex-direction: column; gap: 12px; }
        .feat-list li {
            display: flex; align-items: center; gap: 10px;
            font-size: 0.85rem; opacity: 0.85;
        }
        .feat-list li i {
            font-size: 1rem;
            width: 28px; height: 28px;
            background: rgba(255,255,255,0.15);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .left-footer {
            font-size: 0.75rem;
            opacity: 0.5;
            position: relative; z-index: 1;
        }

        @media (max-width: 640px) { .left { display: none; } }

        /* ===== RIGHT SIDE (form) ===== */
        .right {
            width: 380px;
            flex-shrink: 0;
            background: #fff;
            padding: 48px 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        @media (max-width: 640px) {
            .right { width: 100%; max-width: 420px; border-radius: 20px; }
        }

        .form-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.03em;
            margin-bottom: 4px;
        }
        .form-sub {
            font-size: 0.85rem;
            color: #64748b;
            margin-bottom: 32px;
        }

        /* Alert */
        .alert {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 10px;
            padding: 10px 14px;
            color: #dc2626;
            font-size: 0.82rem;
            display: flex; align-items: center; gap: 8px;
            margin-bottom: 20px;
        }

        /* Form */
        .form-group { margin-bottom: 18px; }
        .form-label {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }
        .input-wrap { position: relative; }
        .input-icon {
            position: absolute; left: 13px; top: 50%;
            transform: translateY(-50%);
            color: #94a3b8; font-size: 1rem;
            pointer-events: none;
        }
        .form-input {
            width: 100%;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 11px 13px 11px 38px;
            font-size: 0.9rem;
            font-family: 'Inter', sans-serif;
            color: #0f172a;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
            background: #fafafa;
        }
        .form-input::placeholder { color: #cbd5e1; }
        .form-input:focus {
            border-color: #2563eb;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }
        .toggle-pass {
            position: absolute; right: 12px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none;
            color: #94a3b8; cursor: pointer; font-size: 1rem;
            transition: color 0.15s;
        }
        .toggle-pass:hover { color: #2563eb; }

        .btn-login {
            width: 100%;
            background: #2563eb;
            color: #fff;
            border: none; border-radius: 10px;
            padding: 12px;
            font-size: 0.9rem; font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 7px;
            transition: background 0.15s, transform 0.1s, box-shadow 0.15s;
            box-shadow: 0 4px 14px rgba(37,99,235,0.3);
            margin-top: 8px;
        }
        .btn-login:hover {
            background: #1d4ed8;
            box-shadow: 0 6px 20px rgba(37,99,235,0.4);
            transform: translateY(-1px);
        }
        .btn-login:active { transform: translateY(0); }
        .btn-login.loading .btn-text { display: none; }
        .btn-login.loading .spinner { display: flex !important; }

        .spinner { display: none; align-items: center; gap: 6px; }

        .form-footer {
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid #f1f5f9;
            text-align: center;
            font-size: 0.78rem;
            color: #94a3b8;
        }
    </style>
</head>
<body>
<div class="wrapper">

    <!-- Left -->
    <div class="left">
        <div class="brand">
            <div class="brand-icon"><i class='bx bx-check-double'></i></div>
            <span class="brand-name">AbsensiApp</span>
        </div>

        <div class="left-content">
            <div class="left-title">Sistem Absensi<br>Digital Sekolah</div>
            <div class="left-sub">
                Kelola kehadiran siswa dengan cepat, akurat, dan modern berbasis QR Code.
            </div>
            <ul class="feat-list">
                <li><i class='bx bx-qr'></i> Absensi QR Code real-time</li>
                <li><i class='bx bx-bar-chart-alt-2'></i> Rekap & laporan otomatis</li>
                <li><i class='bx bx-medal'></i> Sistem poin & reward siswa</li>
                <li><i class='bx bx-shield-quarter'></i> Multi-role: Admin, Guru, Siswa</li>
            </ul>
        </div>

        <div class="left-footer">&copy; {{ date('Y') }} AbsensiApp. All rights reserved.</div>
    </div>

    <!-- Right -->
    <div class="right">
        <div class="form-title">Selamat datang!</div>
        <div class="form-sub">Masuk untuk melanjutkan ke dashboard</div>

        @if($errors->any())
        <div class="alert">
            <i class='bx bx-error-circle'></i>
            {{ $errors->first() }}
        </div>
        @endif

        @if(session('error'))
        <div class="alert">
            <i class='bx bx-error-circle'></i>
            {{ session('error') }}
        </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" id="loginForm">
            @csrf

            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <div class="input-wrap">
                    <i class='bx bx-envelope input-icon'></i>
                    <input type="email" name="email" id="email" class="form-input"
                           placeholder="nama@sekolah.sch.id"
                           value="{{ old('email') }}" required autocomplete="email" />
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div class="input-wrap">
                    <i class='bx bx-lock-alt input-icon'></i>
                    <input type="password" name="password" id="password" class="form-input"
                           placeholder="Masukkan password" required />
                    <button type="button" class="toggle-pass" onclick="togglePass()">
                        <i class='bx bx-hide' id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-login" id="submitBtn">
                <span class="btn-text">
                    <i class='bx bx-log-in'></i> Masuk
                </span>
                <span class="spinner">
                    <i class='bx bx-loader-alt bx-spin'></i> Memproses...
                </span>
            </button>
        </form>

        <div class="form-footer">
            &copy; {{ date('Y') }} AbsensiApp &mdash; Sistem Manajemen Sekolah
        </div>
    </div>
</div>

<script>
    function togglePass() {
        const input = document.getElementById('password');
        const icon  = document.getElementById('eyeIcon');
        input.type  = input.type === 'password' ? 'text' : 'password';
        icon.className = input.type === 'text' ? 'bx bx-show' : 'bx bx-hide';
    }

    document.getElementById('loginForm').addEventListener('submit', function () {
        document.getElementById('submitBtn').classList.add('loading');
    });
</script>
</body>
</html>
