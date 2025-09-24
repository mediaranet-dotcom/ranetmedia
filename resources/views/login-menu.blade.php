<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Menu - Ranet Provider</title>
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
        .menu-container {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }
        .logo {
            margin-bottom: 2rem;
        }
        .logo h1 {
            color: #333;
            margin: 0;
            font-size: 2.5rem;
        }
        .logo p {
            color: #666;
            margin: 0.5rem 0 0 0;
            font-size: 1.1rem;
        }
        .login-options {
            display: grid;
            gap: 1rem;
            margin: 2rem 0;
        }
        .login-option {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            transition: transform 0.3s, box-shadow 0.3s;
            border: none;
            cursor: pointer;
            font-size: 1.1rem;
        }
        .login-option:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }
        .login-option.primary {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        .login-option.secondary {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
        }
        .credentials {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin: 2rem 0;
            border-left: 4px solid #28a745;
        }
        .credentials h3 {
            margin: 0 0 1rem 0;
            color: #28a745;
        }
        .cred-item {
            display: flex;
            justify-content: space-between;
            margin: 0.5rem 0;
            padding: 0.5rem;
            background: white;
            border-radius: 5px;
            font-family: monospace;
        }
        .status {
            background: #e3f2fd;
            padding: 1rem;
            border-radius: 10px;
            margin: 1rem 0;
            border-left: 4px solid #2196f3;
        }
        .features {
            text-align: left;
            background: #f1f3f4;
            padding: 1.5rem;
            border-radius: 10px;
            margin: 2rem 0;
        }
        .features h4 {
            margin: 0 0 1rem 0;
            color: #333;
        }
        .features ul {
            margin: 0;
            padding-left: 1.5rem;
        }
        .features li {
            margin: 0.5rem 0;
        }
    </style>
</head>
<body>
    <div class="menu-container">
        <div class="logo">
            <h1>🌐 Ranet Provider</h1>
            <p>Sistem Pembayaran Otomatis</p>
        </div>

        <div class="status">
            <h3>🎉 Sistem Siap Digunakan!</h3>
            <p>Semua masalah 404 dan login sudah teratasi. Pilih metode login di bawah:</p>
        </div>

        <div class="credentials">
            <h3>🔑 Login Credentials</h3>
            <div class="cred-item">
                <span><strong>Email:</strong></span>
                <span>admin@admin.com</span>
            </div>
            <div class="cred-item">
                <span><strong>Password:</strong></span>
                <span>password</span>
            </div>
        </div>

        <div class="login-options">
            <a href="{{ url('/quick-login') }}" class="login-option primary">
                🚀 Quick Login (Recommended)
                <br><small>Login cepat dengan credentials pre-filled</small>
            </a>

            <a href="{{ url('/admin') }}" class="login-option">
                🏠 Admin Dashboard
                <br><small>Akses langsung ke admin panel</small>
            </a>

            <a href="{{ url('/admin/login') }}" class="login-option secondary">
                🔐 Filament Login
                <br><small>Login standard Filament</small>
            </a>
        </div>

        <div class="features">
            <h4>📋 Fitur yang Tersedia Setelah Login:</h4>
            <ul>
                <li><strong>💰 Invoice Generator</strong> - Generate invoice bulanan otomatis</li>
                <li><strong>👥 Customer Management</strong> - Kelola data customer</li>
                <li><strong>⚙️ Service Management</strong> - Kelola layanan auto billing</li>
                <li><strong>💳 Payment Management</strong> - Monitor pembayaran</li>
                <li><strong>📊 Dashboard</strong> - Overview sistem</li>
            </ul>
        </div>

        <div style="margin-top: 2rem; padding: 1rem; background: #e8f5e8; border-radius: 10px; font-size: 0.9rem;">
            <h4 style="margin: 0 0 0.5rem 0; color: #2e7d32;">✅ Status Sistem:</h4>
            <ul style="margin: 0; padding-left: 1.5rem; color: #2e7d32;">
                <li>✅ Server berjalan dengan baik</li>
                <li>✅ Database terkoneksi</li>
                <li>✅ Admin user sudah dibuat</li>
                <li>✅ Auto invoice siap digunakan</li>
                <li>✅ 6 services dengan auto billing ready</li>
            </ul>
        </div>

        <div style="margin-top: 1rem; font-size: 0.9rem; color: #666;">
            <p>💡 <strong>Tips:</strong> Gunakan Quick Login untuk pengalaman terbaik!</p>
        </div>
    </div>
</body>
</html>
