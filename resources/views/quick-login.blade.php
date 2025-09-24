<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quick Login - Ranet Provider</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }

        .logo {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo h1 {
            color: #333;
            margin: 0;
            font-size: 2rem;
        }

        .logo p {
            color: #666;
            margin: 0.5rem 0 0 0;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: bold;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            box-sizing: border-box;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.2);
        }

        .btn {
            width: 100%;
            padding: 0.75rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .credentials {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            border-left: 4px solid #28a745;
        }

        .credentials h3 {
            margin: 0 0 0.5rem 0;
            color: #28a745;
        }

        .credentials p {
            margin: 0.25rem 0;
            font-family: monospace;
            background: white;
            padding: 0.25rem 0.5rem;
            border-radius: 3px;
        }

        .links {
            text-align: center;
            margin-top: 1rem;
        }

        .links a {
            color: #667eea;
            text-decoration: none;
            margin: 0 0.5rem;
        }

        .links a:hover {
            text-decoration: underline;
        }

        .status {
            padding: 0.75rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            text-align: center;
        }

        .status.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="logo">
            <h1>🌐 Ranet Provider</h1>
            <p>Admin Panel Login</p>
        </div>

        <div class="credentials">
            <h3>🔑 Login Credentials</h3>
            <p><strong>Email:</strong> admin@admin.com</p>
            <p><strong>Password:</strong> password</p>
        </div>

        @if(session('success'))
        <div class="status success">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="status error">
            {{ session('error') }}
        </div>
        @endif

        <form method="POST" action="{{ route('quick.login.submit') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="admin@admin.com" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" value="password" required>
            </div>

            <button type="submit" class="btn">
                🚀 Login to Admin Panel
            </button>
        </form>

        <div class="links">
            <a href="{{ url('/admin') }}">🏠 Admin Dashboard</a>
            <a href="{{ url('/admin/login') }}">🔐 Filament Login</a>
            @auth
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" style="background: none; border: none; color: #667eea; text-decoration: none; cursor: pointer;">
                    🚪 Logout
                </button>
            </form>
            @endauth
        </div>

        <div style="margin-top: 2rem; padding: 1rem; background: #e9ecef; border-radius: 5px; font-size: 0.9rem;">
            <h4 style="margin: 0 0 0.5rem 0;">📋 After Login Access:</h4>
            <ul style="margin: 0; padding-left: 1.5rem;">
                <li>Invoice Generator: <code>/admin/invoice-generator</code></li>
                <li>Customer Management: <code>/admin/customers</code></li>
                <li>Service Management: <code>/admin/services</code></li>
                <li>Payment Management: <code>/admin/payments</code></li>
            </ul>
        </div>
    </div>
</body>

</html>