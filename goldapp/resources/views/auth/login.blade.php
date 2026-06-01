<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Production ERP - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --gold: #d4af37;
            --gold-light: #f0d060;
            --gold-dark: #a0821c;
        }
        body {
            background: radial-gradient(ellipse at center, #1a1535 0%, #0d0d18 60%, #000 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
            overflow: hidden;
        }
        /* Decorative background diamonds */
        body::before {
            content: '◇ ✦ ◆ ✦ ◇ ✦ ◆ ✦ ◇ ✦ ◆ ✦ ◇';
            position: fixed;
            top: 8%;
            left: 0; right: 0;
            text-align: center;
            color: rgba(212,175,55,0.08);
            font-size: 1.5rem;
            letter-spacing: 12px;
            pointer-events: none;
        }
        body::after {
            content: '◇ ✦ ◆ ✦ ◇ ✦ ◆ ✦ ◇ ✦ ◆ ✦ ◇';
            position: fixed;
            bottom: 8%;
            left: 0; right: 0;
            text-align: center;
            color: rgba(212,175,55,0.08);
            font-size: 1.5rem;
            letter-spacing: 12px;
            pointer-events: none;
        }
        .login-wrap {
            width: 100%;
            max-width: 420px;
            padding: 16px;
        }
        .login-card {
            background: linear-gradient(145deg, #13132a 0%, #0f0f22 100%);
            border: 1px solid rgba(212,175,55,0.4);
            border-radius: 12px;
            padding: 40px 36px;
            box-shadow: 0 0 60px rgba(212,175,55,0.08), inset 0 0 60px rgba(0,0,0,0.3);
            position: relative;
        }
        .login-card::before {
            content: '';
            position: absolute;
            top: 0; left: 20%; right: 20%;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
        }
        .login-card::after {
            content: '';
            position: absolute;
            bottom: 0; left: 20%; right: 20%;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
        }
        .brand-row {
            text-align: center;
            margin-bottom: 28px;
        }
        .brand-diamonds {
            color: var(--gold);
            font-size: 1rem;
            letter-spacing: 8px;
            margin-bottom: 8px;
        }
        .brand-title {
            color: var(--gold);
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: 6px;
            text-shadow: 0 0 20px rgba(212,175,55,0.5);
            margin: 0;
        }
        .brand-sub {
            color: rgba(212,175,55,0.5);
            font-size: 0.65rem;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-top: 4px;
        }
        .divider-gold {
            border: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(212,175,55,0.3), transparent);
            margin: 20px 0;
        }
        .form-label {
            color: rgba(212,175,55,0.7);
            font-size: 0.72rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .form-control {
            background: rgba(255,255,255,0.04) !important;
            border: 1px solid rgba(212,175,55,0.3) !important;
            color: #e0d8b8 !important;
            border-radius: 6px;
            padding: 10px 14px;
        }
        .form-control:focus {
            border-color: var(--gold) !important;
            box-shadow: 0 0 0 3px rgba(212,175,55,0.12) !important;
            background: rgba(255,255,255,0.06) !important;
        }
        .form-control::placeholder { color: rgba(200,190,150,0.3) !important; }
        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, var(--gold-dark), var(--gold));
            color: #000;
            font-weight: 700;
            font-size: 0.88rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            border: none;
            padding: 12px;
            border-radius: 6px;
            transition: all 0.3s;
            margin-top: 8px;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, var(--gold), var(--gold-light));
            color: #000;
            box-shadow: 0 6px 20px rgba(212,175,55,0.4);
            transform: translateY(-1px);
        }
        .error-box {
            background: rgba(220,50,50,0.1);
            border: 1px solid rgba(220,50,50,0.35);
            color: #f87171;
            border-radius: 6px;
            padding: 10px 14px;
            font-size: 0.82rem;
            margin-bottom: 16px;
        }
        .footer-text {
            text-align: center;
            color: rgba(212,175,55,0.25);
            font-size: 0.65rem;
            letter-spacing: 2px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="login-wrap">
    <div class="login-card">
        <div class="brand-row">
            <div class="brand-diamonds">◇ ✦ ◆ ✦ ◇</div>
            <h1 class="brand-title">GOLD PLUS</h1>
            <div class="brand-sub">Food Production ERP System</div>
        </div>
        <hr class="divider-gold">

        @if($errors->any())
            <div class="error-box">
                @foreach($errors->all() as $e) {{ $e }} @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control"
                    placeholder="admin@goldapp.com"
                    value="{{ old('email') }}" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control"
                    placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-login">◆ Sign In ◆</button>
        </form>

        <div class="footer-text">◇ Food Production ERP v1.0 ◇</div>
    </div>
</div>
</body>
</html>
