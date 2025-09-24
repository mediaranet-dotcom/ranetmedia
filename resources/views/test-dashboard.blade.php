<!DOCTYPE html>
<html>
<head>
    <title>Test Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .container { max-width: 800px; margin: 0 auto; }
        .card { background: #f5f5f5; padding: 20px; margin: 20px 0; border-radius: 8px; }
        .success { background: #d4edda; color: #155724; }
        .info { background: #d1ecf1; color: #0c5460; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎉 Test Dashboard - Admin Panel Working!</h1>
        
        <div class="card success">
            <h3>✅ Authentication Status</h3>
            @auth
                <p><strong>User:</strong> {{ Auth::user()->name }} ({{ Auth::user()->email }})</p>
                <p><strong>Status:</strong> Successfully authenticated!</p>
            @else
                <p><strong>Status:</strong> Not authenticated</p>
            @endauth
        </div>

        <div class="card info">
            <h3>📊 System Information</h3>
            <p><strong>Laravel Version:</strong> {{ app()->version() }}</p>
            <p><strong>PHP Version:</strong> {{ PHP_VERSION }}</p>
            <p><strong>Current Time:</strong> {{ now()->format('Y-m-d H:i:s') }}</p>
        </div>

        <div class="card">
            <h3>🔗 Navigation</h3>
            <p><a href="/admin">← Back to Filament Admin</a></p>
            <p><a href="/admin/login">Login Page</a></p>
            <p><a href="/simple-login">Simple Login</a></p>
        </div>

        <div class="card">
            <h3>🧪 Test Results</h3>
            <p>✅ Dashboard page loads successfully</p>
            <p>✅ Blade templating works</p>
            <p>✅ Authentication check works</p>
            <p>✅ Laravel application is functional</p>
        </div>
    </div>
</body>
</html>
