<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Pelanggan - RANET</title>
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
            <p class="text-gray-600">Formulir Pendaftaran Pelanggan Baru</p>
        </div>

        <!-- Registration Form -->
        <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-8">
            @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form id="registrationForm" action="{{ route('registration.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Personal Information -->
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">
                        <i class="fas fa-user text-blue-600"></i>
                        Informasi Pribadi
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap *</label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon *</label>
                            <input type="tel" name="phone" value="{{ old('phone') }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nomor KTP/SIM *</label>
                            <input type="text" name="identity_number" value="{{ old('identity_number') }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Address Information -->
                <div class="border-b pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">
                        <i class="fas fa-map-marker-alt text-blue-600"></i>
                        Alamat Lengkap
                    </h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Jalan/Rumah *</label>
                            <textarea name="address" rows="2" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Contoh: Jl. Merdeka No. 123, Blok A">{{ old('address') }}</textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Provinsi *</label>
                                <input type="text" name="province" value="{{ old('province') }}" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Kabupaten/Kota *</label>
                                <input type="text" name="regency" value="{{ old('regency') }}" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Kecamatan *</label>
                                <input type="text" name="district" value="{{ old('district') }}" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Desa/Kelurahan *</label>
                                <input type="text" name="village" value="{{ old('village') }}" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Dusun/Kampung</label>
                                <input type="text" name="hamlet" value="{{ old('hamlet') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Kode Pos</label>
                                <input type="text" name="postal_code" value="{{ old('postal_code') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">RT</label>
                                <input type="text" name="rt" value="{{ old('rt') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">RW</label>
                                <input type="text" name="rw" value="{{ old('rw') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Alamat</label>
                            <textarea name="address_notes" rows="2"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Patokan atau catatan tambahan untuk memudahkan pencarian alamat">{{ old('address_notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Package Selection -->
                <div class="border-b pb-6 package-selection">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6 text-center">
                        <i class="fas fa-box text-blue-600 mr-2"></i>
                        Pilih Paket Layanan
                    </h2>

                    <!-- Package Selection -->
                    <div class="max-w-2xl mx-auto">
                        @foreach($packages as $index => $package)
                        <div class="package-option mb-4">
                            <label class="cursor-pointer block">
                                <input type="radio" name="package_id" value="{{ $package->id }}"
                                    {{ old('package_id') == $package->id ? 'checked' : '' }}
                                    class="sr-only peer package-radio">
                                <div class="border-2 border-gray-200 rounded-lg p-4 peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-blue-300 transition-all duration-200 bg-white">
                                    <div class="flex items-center justify-between">
                                        <!-- Package Info -->
                                        <div class="flex items-center space-x-4">
                                            <!-- Icon -->
                                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                                @if($package->name == 'Paket Basic')
                                                <i class="fas fa-home text-white"></i>
                                                @elseif($package->name == 'Paket Premium')
                                                <i class="fas fa-star text-white"></i>
                                                @elseif($package->name == 'Paket Business')
                                                <i class="fas fa-building text-white"></i>
                                                @else
                                                <i class="fas fa-rocket text-white"></i>
                                                @endif
                                            </div>

                                            <!-- Package Details -->
                                            <div>
                                                <div class="flex items-center space-x-2">
                                                    <h3 class="font-bold text-lg text-gray-800">{{ $package->name }}</h3>
                                                    @if($package->name == 'Paket Premium')
                                                    <span class="bg-orange-500 text-white text-xs px-2 py-1 rounded-full font-semibold">POPULER</span>
                                                    @endif
                                                </div>
                                                <p class="text-gray-600 text-sm">{{ $package->speed }} • Unlimited Kuota • Support 24/7</p>
                                            </div>
                                        </div>

                                        <!-- Price -->
                                        <div class="text-right">
                                            <div class="text-2xl font-bold text-gray-800">
                                                Rp {{ number_format($package->price, 0, ',', '.') }}
                                            </div>
                                            <div class="text-gray-500 text-sm">/bulan</div>
                                        </div>

                                        <!-- Selection Indicator -->
                                        <div class="w-6 h-6 border-2 border-gray-300 rounded-full peer-checked:border-blue-500 peer-checked:bg-blue-500 transition-all duration-200 flex items-center justify-center ml-4">
                                            <i class="fas fa-check text-white text-xs opacity-0 peer-checked:opacity-100"></i>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Package Info -->
                <div class="text-center mt-6 text-sm text-gray-500">
                    Pilih salah satu paket layanan di atas
                </div>
        </div>

        <!-- Installation Notes -->
        <div class="bg-gray-50 p-4 rounded-lg">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-tools text-blue-600"></i>
                Catatan Instalasi
            </h2>
            <textarea name="installation_notes" rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Catatan khusus untuk proses instalasi (opsional)">{{ old('installation_notes') }}</textarea>
        </div>

        <!-- Submit Button -->
        <div class="text-center">
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg transition-colors">
                <i class="fas fa-paper-plane mr-2"></i>
                Daftar Sekarang
            </button>
        </div>
        </form>
    </div>

    <!-- Footer -->
    <div class="text-center mt-8 text-gray-600">
        <p>Sudah menjadi pelanggan? <a href="{{ route('service.application.form') }}" class="text-blue-600 hover:underline">Ajukan Layanan Tambahan</a></p>
        <p class="mt-2">© 2025 RANET. Semua hak dilindungi.</p>
    </div>
    </div>

    <script>
        // Form validation
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            // Check if package is selected
            const packageSelected = document.querySelector('input[name="package_id"]:checked');
            if (!packageSelected) {
                e.preventDefault();
                alert('Silakan pilih paket layanan terlebih dahulu!');
                document.querySelector('.package-selection').scrollIntoView({
                    behavior: 'smooth'
                });
                return false;
            }

            // Check required fields
            const requiredFields = [
                'name', 'phone', 'identity_number', 'address',
                'province', 'regency', 'district', 'village'
            ];

            for (let field of requiredFields) {
                const input = document.querySelector(`[name="${field}"]`);
                if (!input || !input.value.trim()) {
                    e.preventDefault();
                    alert(`Field ${field} harus diisi!`);
                    if (input) {
                        input.focus();
                        input.scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                    return false;
                }
            }
        });

        // Package selection visual feedback
        document.querySelectorAll('.package-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                // Remove any previous error styling
                document.querySelector('.package-selection')?.classList.remove('border-red-500');
            });
        });

        // Real-time validation for required fields
        const requiredFields = ['name', 'phone', 'identity_number', 'address', 'province', 'regency', 'district', 'village'];

        requiredFields.forEach(fieldName => {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (field) {
                field.addEventListener('blur', function() {
                    if (!this.value.trim()) {
                        this.classList.add('border-red-500');
                        this.classList.remove('border-gray-300');
                    } else {
                        this.classList.remove('border-red-500');
                        this.classList.add('border-gray-300');
                    }
                });

                field.addEventListener('input', function() {
                    if (this.value.trim()) {
                        this.classList.remove('border-red-500');
                        this.classList.add('border-gray-300');
                    }
                });
            }
        });
    </script>

</body>

</html>