<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Berhasil - RANET</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-wifi text-blue-600"></i>
                RANET
            </h1>
        </div>

        <!-- Success Message -->
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-8 text-center">
            <div class="mb-6">
                <i class="fas fa-check-circle text-green-500 text-6xl mb-4"></i>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Pengajuan Layanan Berhasil!</h2>
                <p class="text-gray-600">Terima kasih atas pengajuan layanan tambahan Anda</p>
            </div>

            <!-- Customer Number -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-blue-800 mb-2">Nomor Pelanggan:</h3>
                <div class="text-2xl font-bold text-blue-600 font-mono">{{ $customerNumber }}</div>
                <p class="text-sm text-blue-700 mt-2">Referensi untuk komunikasi terkait pengajuan ini</p>
            </div>

            <!-- Next Steps -->
            <div class="text-left mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Langkah Selanjutnya:</h3>
                <div class="space-y-3">
                    <div class="flex items-start">
                        <i class="fas fa-phone text-blue-600 mt-1 mr-3"></i>
                        <div>
                            <strong>Konfirmasi Tim Kami</strong>
                            <p class="text-gray-600 text-sm">dalam 1-2 hari kerja untuk membahas detail layanan</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-calculator text-blue-600 mt-1 mr-3"></i>
                        <div>
                            <strong>Penawaran Harga</strong>
                            <p class="text-gray-600 text-sm">Tim akan memberikan rincian biaya instalasi dan aktivasi</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-map-marked-alt text-blue-600 mt-1 mr-3"></i>
                        <div>
                            <strong>Survey Lokasi</strong>
                            <p class="text-gray-600 text-sm">Teknisi akan melakukan survey untuk memastikan kelayakan</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-tools text-blue-600 mt-1 mr-3"></i>
                        <div>
                            <strong>Penjadwalan Instalasi</strong>
                            <p class="text-gray-600 text-sm">Setelah persetujuan, kami akan menjadwalkan instalasi</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Hubungi Kami:</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="flex items-center">
                        <i class="fas fa-phone text-blue-600 mr-2"></i>
                        <span>Telepon: (021) 1234-5678</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fab fa-whatsapp text-green-600 mr-2"></i>
                        <span>WhatsApp: 0812-3456-7890</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-envelope text-blue-600 mr-2"></i>
                        <span>Email: info@ranet.com</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-clock text-blue-600 mr-2"></i>
                        <span>Jam Kerja: 08:00 - 17:00 WIB</span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-3">
                <a href="{{ route('service.application.form') }}"
                    class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Ajukan Layanan Lain
                </a>
                <br>
                <a href="{{ route('registration.form') }}"
                    class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                    <i class="fas fa-user-plus mr-2"></i>
                    Daftarkan Pelanggan Baru
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 text-gray-600">
            <p>© 2025 RANET. Semua hak dilindungi.</p>
        </div>
    </div>
</body>

</html>