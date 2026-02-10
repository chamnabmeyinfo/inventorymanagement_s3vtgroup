<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in - S3VT Inventory</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --win-accent: #0078D4;
            --win-accent-hover: #106EBE;
            --win-accent-subtle: rgba(0, 120, 212, 0.12);
            --win-text: #1F1F1F;
            --win-text-secondary: #605E5C;
            --win-danger: #D13438;
            --win-radius: 14px;
            --win-blur: 24px;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0; font-family: 'Segoe UI', system-ui, sans-serif;
            background: linear-gradient(160deg, #E8ECF1 0%, #DDE4EB 50%, #D4DCE4 100%);
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .login-wrap { width: 100%; max-width: 420px; }
        .login-logo {
            font-size: 1.5rem; font-weight: 600; color: var(--win-accent);
            margin-bottom: 32px; text-align: center;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.4);
            -webkit-backdrop-filter: blur(var(--win-blur)) saturate(180%);
            backdrop-filter: blur(var(--win-blur)) saturate(180%);
            border-radius: var(--win-radius);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.5);
            padding: 32px;
        }
        .login-card h1 { margin: 0 0 8px 0; font-size: 1.25rem; font-weight: 600; color: var(--win-text); }
        .login-card .subtitle { color: var(--win-text-secondary); font-size: 0.875rem; margin-bottom: 24px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 6px; font-weight: 500; font-size: 0.875rem; }
        .form-group input {
            width: 100%; padding: 10px 14px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 10px;
            font-size: 0.9375rem;
            font-family: inherit;
            background: rgba(255, 255, 255, 0.5);
            -webkit-backdrop-filter: blur(8px);
            backdrop-filter: blur(8px);
            transition: border-color 0.2s ease;
        }
        .form-group input:focus {
            outline: none;
            border-color: var(--win-accent);
            box-shadow: 0 0 0 2px var(--win-accent-subtle);
        }
        .btn {
            width: 100%; padding: 10px 16px;
            border-radius: 10px;
            border: none;
            font-weight: 600;
            font-size: 0.9375rem;
            cursor: pointer;
            background: var(--win-accent);
            color: #fff;
            font-family: inherit;
            transition: background 0.2s ease;
        }
        .btn:hover { background: var(--win-accent-hover); }
        .alert-error {
            background: rgba(209, 52, 56, 0.12);
            -webkit-backdrop-filter: blur(8px);
            backdrop-filter: blur(8px);
            color: var(--win-danger);
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.875rem;
            border: 1px solid rgba(209, 52, 56, 0.25);
        }
    </style>
</head>
<body>
    <div class="login-wrap">
        <div class="login-logo">S3VT Inventory</div>
        <div class="login-card">
            <h1>Sign in</h1>
            <p class="subtitle">Admin dashboard</p>
            @if($errors->any())
                <div class="alert-error">{{ $errors->first() }}</div>
            @endif
            <form method="POST" action="{{ route('admin.login') }}">
                @csrf
                <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" required>
                </div>
                <button type="submit" class="btn">Sign in</button>
            </form>
        </div>
    </div>
</body>
</html>
