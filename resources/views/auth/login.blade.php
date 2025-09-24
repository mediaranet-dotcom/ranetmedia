<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Ranet Provider</title>
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
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .logo {
            font-size: 2rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 1rem;
        }

        .subtitle {
            color: #666;
            margin-bottom: 2rem;
        }

        .admin-link {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px;
            transition: background 0.3s;
        }

        .admin-link:hover {
            background: #5a6fd8;
        }

        .info {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 5px;
            margin: 1rem 0;
            border-left: 4px solid #667eea;
        }

        .credentials {
            background: #e8f4fd;
            padding: 1rem;
            border-radius: 5px;
            margin: 1rem 0;
            text-align: left;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="logo">🌐 RANET PROVIDER</div>
        <div class="subtitle">Internet Service Provider Management</div>

        <div class="info">
            <strong>🎯 Akses Admin Panel</strong><br>
            Klik tombol di bawah untuk masuk ke admin panel
        </div>

        <a href="/admin" class="admin-link">
            🚀 Masuk ke Admin Panel
        </a>

        <div class="credentials">
            <strong>🔑 Login Credentials:</strong><br>
            <strong>Email:</strong> admin@example.com<br>
            <strong>Password:</strong> password
        </div>

        <div class="info">
            <small>
                <strong>💡 Info:</strong><br>
                • Sistem menggunakan Filament Admin Panel<br>
                • Login otomatis akan muncul jika belum login<br>
                • Setelah login, akses fitur Auto Invoice
            </small>
        </div>
    </div>
</body>

</html>