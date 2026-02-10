<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - S3VT Inventory</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-primary: #0f766e;
            --color-primary-hover: #0d9488;
            --color-primary-light: #ccfbf1;
            --color-text: #1e293b;
            --color-text-muted: #64748b;
            --color-border: #e2e8f0;
            --color-danger: #dc2626;
        }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Inter', system-ui, sans-serif; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1rem; }
        .login-card {
            background: #fff; border-radius: 12px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
            padding: 2rem; width: 100%; max-width: 400px;
        }
        .login-card h1 { margin: 0 0 0.5rem 0; font-size: 1.5rem; font-weight: 700; color: #0f172a; }
        .login-card .subtitle { color: #64748b; font-size: 0.9375rem; margin-bottom: 1.5rem; }
        .form-group { margin-bottom: 1.25rem; }
        .form-group label { display: block; margin-bottom: 0.25rem; font-weight: 500; font-size: 0.9375rem; }
        .form-group input {
            width: 100%; padding: 0.625rem 0.875rem; border: 1px solid #e2e8f0;
            border-radius: 8px; font-size: 0.9375rem; font-family: inherit;
        }
        .form-group input:focus {
            outline: none; border-color: var(--color-primary); box-shadow: 0 0 0 3px var(--color-primary-light);
        }
        .btn { width: 100%; padding: 0.625rem 1rem; border-radius: 8px; border: none; font-weight: 600; font-size: 0.9375rem; cursor: pointer; background: var(--color-primary); color: #fff; font-family: inherit; transition: background 0.15s; }
        .btn:hover { background: var(--color-primary-hover); }
        .alert-error { background: #fef2f2; color: #dc2626; padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1rem; font-size: 0.9375rem; }
        .logo { font-size: 1.75rem; font-weight: 700; color: #f8fafc; margin-bottom: 2rem; text-align: center; }
    </style>
</head>
<body>
    <div style="width: 100%; max-width: 400px;">
        <div class="logo">S3VT Inventory</div>
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
