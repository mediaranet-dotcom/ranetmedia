<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Layanan - RANET</title>
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
            <p class="text-gray-600">Formulir Pengajuan Layanan Tambahan</p>
        </div>

        <!-- Service Application Form -->
        <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-lg p-8">
            @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('service.application.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Customer Information -->
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">
                        <i class="fas fa-user text-blue-600"></i>
                        Informasi Pelanggan
                    </h2>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Pelanggan *</label>
                        <input type="text" name="customer_number" value="{{ old('customer_number') }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Contoh: RANET-202508-0001">
                        <p class="text-sm text-gray-500 mt-1">Masukkan nomor pelanggan yang Anda terima saat pendaftaran</p>
                    </div>
                </div>

                <!-- Package Selection -->
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6 text-center">
                        <i class="fas fa-box text-blue-600 mr-2"></i>
                        Pilih Paket Layanan Baru
                    </h2>

                    <!-- Package Slider -->
                    <div class="relative max-w-md mx-auto">
                        <div class="package-slider overflow-hidden rounded-xl">
                            <div class="package-container flex transition-transform duration-500 ease-in-out" id="packageContainer">
                                @foreach($packages as $index => $package)
                                <div class="package-slide w-full flex-shrink-0 px-2">
                                    <label class="cursor-pointer block">
                                        <input type="radio" name="package_id" value="{{ $package->id }}"
                                            {{ old('package_id') == $package->id ? 'checked' : '' }}
                                            class="sr-only peer package-radio" data-index="{{ $index }}">
                                        <div class="package-card border-2 border-gray-200 rounded-xl p-6 peer-checked:border-blue-500 peer-checked:bg-gradient-to-br peer-checked:from-blue-50 peer-checked:to-indigo-50 hover:border-blue-300 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 relative overflow-hidden bg-white">
                                            <!-- Background Pattern -->
                                            <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-blue-100 to-transparent rounded-full transform translate-x-8 -translate-y-8"></div>

                                            <!-- Package Header -->
                                            <div class="text-center mb-4">
                                                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full mb-3">
                                                    @if($package->name == 'Paket Basic')
                                                    <i class="fas fa-home text-white text-xl"></i>
                                                    @elseif($package->name == 'Paket Premium')
                                                    <i class="fas fa-star text-white text-xl"></i>
                                                    @elseif($package->name == 'Paket Business')
                                                    <i class="fas fa-building text-white text-xl"></i>
                                                    @else
                                                    <i class="fas fa-rocket text-white text-xl"></i>
                                                    @endif
                                                </div>
                                                <h3 class="font-bold text-xl text-gray-800 mb-1">{{ $package->name }}</h3>
                                                @if($package->name == 'Paket Premium')
                                                <span class="inline-block bg-gradient-to-r from-orange-400 to-red-500 text-white text-xs px-3 py-1 rounded-full font-semibold">TERPOPULER</span>
                                                @endif
                                            </div>

                                            <!-- Price -->
                                            <div class="text-center mb-4">
                                                <div class="text-3xl font-bold text-gray-800 mb-1">
                                                    Rp {{ number_format($package->price, 0, ',', '.') }}
                                                </div>
                                                <div class="text-gray-500 text-sm">/bulan</div>
                                            </div>

                                            <!-- Features -->
                                            <div class="space-y-3 mb-6">
                                                <div class="flex items-center">
                                                    <i class="fas fa-tachometer-alt text-blue-500 mr-3"></i>
                                                    <span class="text-gray-700 font-medium">{{ $package->speed }}</span>
                                                </div>
                                                <div class="flex items-center">
                                                    <i class="fas fa-infinity text-green-500 mr-3"></i>
                                                    <span class="text-gray-700">Unlimited Kuota</span>
                                                </div>
                                                <div class="flex items-center">
                                                    <i class="fas fa-headset text-purple-500 mr-3"></i>
                                                    <span class="text-gray-700">Support 24/7</span>
                                                </div>
                                                @if($package->name != 'Paket Basic')
                                                <div class="flex items-center">
                                                    <i class="fas fa-tools text-orange-500 mr-3"></i>
                                                    <span class="text-gray-700">Free Instalasi</span>
                                                </div>
                                                @endif
                                                @if($package->name == 'Paket Business' || $package->name == 'Paket Ultra')
                                                <div class="flex items-center">
                                                    <i class="fas fa-user-tie text-indigo-500 mr-3"></i>
                                                    <span class="text-gray-700">Dedicated Support</span>
                                                </div>
                                                @endif
                                            </div>

                                            <!-- Description -->
                                            <p class="text-sm text-gray-600 text-center leading-relaxed">{{ $package->description }}</p>

                                            <!-- Selection Indicator -->
                                            <div class="absolute top-4 right-4 w-6 h-6 border-2 border-gray-300 rounded-full peer-checked:border-blue-500 peer-checked:bg-blue-500 transition-all duration-200 flex items-center justify-center">
                                                <i class="fas fa-check text-white text-xs opacity-0 peer-checked:opacity-100"></i>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Navigation Arrows -->
                        <button type="button" id="prevBtn" class="absolute left-0 top-1/2 transform -translate-y-1/2 -translate-x-4 bg-white rounded-full p-3 shadow-lg hover:shadow-xl transition-all duration-200 border border-gray-200 hover:border-blue-300 z-10">
                            <i class="fas fa-chevron-left text-gray-600"></i>
                        </button>
                        <button type="button" id="nextBtn" class="absolute right-0 top-1/2 transform -translate-y-1/2 translate-x-4 bg-white rounded-full p-3 shadow-lg hover:shadow-xl transition-all duration-200 border border-gray-200 hover:border-blue-300 z-10">
                            <i class="fas fa-chevron-right text-gray-600"></i>
                        </button>
                    </div>

                    <!-- Dots Indicator -->
                    <div class="flex justify-center mt-6 space-x-2" id="dotsContainer">
                        @foreach($packages as $index => $package)
                        <button type="button" class="dot w-3 h-3 rounded-full bg-gray-300 hover:bg-gray-400 transition-colors duration-200" data-index="{{ $index }}"></button>
                        @endforeach
                    </div>

                    <!-- Package Counter -->
                    <div class="text-center mt-4 text-sm text-gray-500">
                        <span id="currentPackage">1</span> dari {{ count($packages) }} paket
                    </div>
                </div>

                <!-- Installation Details -->
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">
                        <i class="fas fa-map-marker-alt text-blue-600"></i>
                        Detail Instalasi
                    </h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Instalasi</label>
                            <textarea name="installation_address" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Kosongkan jika sama dengan alamat terdaftar, atau isi jika berbeda">{{ old('installation_address') }}</textarea>
                            <p class="text-sm text-gray-500 mt-1">Isi hanya jika alamat instalasi berbeda dengan alamat yang terdaftar</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Instalasi</label>
                            <textarea name="installation_notes" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Catatan khusus untuk proses instalasi (opsional)">{{ old('installation_notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Information Notice -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-blue-800 mb-2">
                        <i class="fas fa-info-circle mr-2"></i>
                        Informasi Penting
                    </h3>
                    <ul class="text-blue-700 text-sm space-y-1">
                        <li>• Tim kami akan menghubungi Anda dalam 1-2 hari kerja untuk konfirmasi</li>
                        <li>• Survey lokasi akan dilakukan untuk memastikan kelayakan instalasi</li>
                        <li>• Biaya instalasi dan aktivasi akan diinformasikan saat konfirmasi</li>
                        <li>• Proses instalasi biasanya memakan waktu 1-3 hari kerja setelah survey</li>
                    </ul>
                </div>

                <!-- Submit Button -->
                <div class="text-center">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg transition-colors">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Ajukan Layanan
                    </button>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 text-gray-600">
            <p>Belum menjadi pelanggan? <a href="{{ route('registration.form') }}" class="text-blue-600 hover:underline">Daftar Sekarang</a></p>
            <p class="mt-2">© 2025 RANET. Semua hak dilindungi.</p>
        </div>
    </div>

    <script src="{{ asset('js/package-slider.js') }}"></script>


</body>

</html>