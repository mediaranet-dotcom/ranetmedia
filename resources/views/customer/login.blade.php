<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pelanggan - RANET</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-wifi text-blue-600 text-2xl mr-3"></i>
                    <h1 class="text-xl md:text-2xl font-bold text-gray-800">RANET</h1>
                </div>
                <a href="{{ route('welcome') }}" class="text-gray-600 hover:text-blue-600">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto">
            <!-- Login Card -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full mb-4">
                        <i class="fas fa-user text-white text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Login Pelanggan</h2>
                    <p class="text-gray-600">Masuk untuk mengakses akun Anda</p>
                </div>

                <!-- Error Messages -->
                @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <div>
                            @foreach ($errors->all() as $error)
                            <p class="text-sm">{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                @if (session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <p class="text-sm">{{ session('error') }}</p>
                    </div>
                </div>
                @endif

                @if (session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                </div>
                @endif

                <!-- Login Form -->
                <form action="{{ route('customer.login.submit') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Phone Number -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-phone mr-2 text-blue-600"></i>Nomor Telepon
                        </label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Contoh: 081234567890">
                        <p class="text-xs text-gray-500 mt-1">Masukkan nomor telepon yang terdaftar</p>
                    </div>

                    <!-- Customer ID (Optional) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-id-card mr-2 text-blue-600"></i>ID/Nomor Pelanggan (Opsional)
                        </label>
                        <input type="text" name="customer_id" value="{{ old('customer_id') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Contoh: CUST-001 atau RANET-202508-0001">
                        <p class="text-xs text-gray-500 mt-1">Bisa menggunakan ID Pelanggan (CUST-001) atau Nomor Pelanggan (RANET-202508-0001)</p>
                    </div>

                    <!-- Login Button -->
                    <button type="submit"
                        class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 px-4 rounded-lg font-semibold hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transform transition-all duration-200 hover:scale-105">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Masuk
                    </button>
                </form>

                <!-- Divider -->
                <div class="relative my-8">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">atau</span>
                    </div>
                </div>

                <!-- Register Link -->
                <div class="text-center">
                    <p class="text-gray-600 mb-4">Belum menjadi pelanggan?</p>
                    <a href="{{ route('registration.form') }}"
                        class="inline-flex items-center justify-center w-full border-2 border-blue-600 text-blue-600 py-3 px-4 rounded-lg font-semibold hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                        <i class="fas fa-user-plus mr-2"></i>
                        Daftar Sekarang
                    </a>
                </div>

                <!-- Help Section -->
                <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-sm font-semibold text-gray-800 mb-2">
                        <i class="fas fa-question-circle mr-2 text-blue-600"></i>Butuh Bantuan?
                    </h3>
                    <div class="text-xs text-gray-600 space-y-1">
                        <p>• Hubungi customer service: <strong>(021) 1234-5678</strong></p>
                        <p>• WhatsApp: <strong>0852-2620-5548</strong></p>
                        <p>• Email: <strong>support@ranet.com</strong></p>
                    </div>
                </div>
            </div>

            <!-- Quick Info -->
            <div class="mt-6 text-center">
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <h3 class="text-sm font-semibold text-gray-800 mb-2">
                        <i class="fas fa-info-circle mr-2 text-blue-600"></i>Setelah Login
                    </h3>
                    <div class="text-xs text-gray-600 space-y-1">
                        <p>✓ Lihat tagihan dan riwayat pembayaran</p>
                        <p>✓ Cek status layanan internet</p>
                        <p>✓ Ajukan layanan tambahan</p>
                        <p>✓ Update informasi kontak</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-6 mt-12">
        <div class="container mx-auto px-4 text-center">
            <p class="text-gray-600 text-sm">
                © 2025 RANET Provider. Semua hak dilindungi.
            </p>
        </div>
    </footer>
</body>

</html>