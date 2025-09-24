<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RANET - Internet Cepat & Terpercaya</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-wifi text-blue-600 text-2xl md:text-3xl mr-2 md:mr-3"></i>
                    <h1 class="text-xl md:text-2xl font-bold text-gray-800">RANET</h1>
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden md:flex space-x-6">
                    <a href="#home" class="text-gray-600 hover:text-blue-600">Beranda</a>
                    <a href="#packages" class="text-gray-600 hover:text-blue-600">Paket</a>
                    <a href="#contact" class="text-gray-600 hover:text-blue-600">Kontak</a>
                    <a href="{{ route('customer.login') }}" class="text-gray-600 hover:text-blue-600">
                        <i class="fas fa-sign-in-alt mr-1"></i>Login
                    </a>
                    <a href="{{ route('registration.form') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Daftar</a>
                </nav>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-btn" class="md:hidden text-gray-600 hover:text-blue-600">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>

            <!-- Mobile Navigation -->
            <nav id="mobile-menu" class="hidden md:hidden mt-4 pb-4 border-t border-gray-200">
                <div class="flex flex-col space-y-3 pt-4">
                    <a href="#home" class="text-gray-600 hover:text-blue-600 py-2">Beranda</a>
                    <a href="#packages" class="text-gray-600 hover:text-blue-600 py-2">Paket</a>
                    <a href="#contact" class="text-gray-600 hover:text-blue-600 py-2">Kontak</a>
                    <a href="{{ route('customer.login') }}" class="text-gray-600 hover:text-blue-600 py-2">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login Pelanggan
                    </a>
                    <a href="{{ route('registration.form') }}" class="bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700 text-center">
                        <i class="fas fa-user-plus mr-2"></i>Daftar Sekarang
                    </a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="home" class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-12 md:py-20">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl md:text-6xl font-bold mb-4 md:mb-6">Internet Cepat & Terpercaya</h2>
            <p class="text-lg md:text-2xl mb-6 md:mb-8 px-4">Nikmati koneksi internet berkualitas tinggi untuk rumah dan bisnis Anda</p>

            <!-- Mobile-first button layout -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center max-w-md mx-auto sm:max-w-none">
                <a href="{{ route('registration.form') }}"
                    class="w-full sm:w-auto bg-white text-blue-600 px-6 md:px-8 py-3 rounded-lg font-bold hover:bg-gray-100 transition-colors text-center">
                    <i class="fas fa-user-plus mr-2"></i>
                    Daftar Sekarang
                </a>
                <a href="{{ route('customer.login') }}"
                    class="w-full sm:w-auto border-2 border-white text-white px-6 md:px-8 py-3 rounded-lg font-bold hover:bg-white hover:text-blue-600 transition-colors text-center">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Login Pelanggan
                </a>
            </div>

            <!-- Quick access for existing customers -->
            <div class="mt-8 text-sm opacity-90">
                <p>Sudah menjadi pelanggan?
                    <a href="{{ route('customer.login') }}" class="underline hover:text-blue-200">
                        Login untuk cek tagihan & status layanan
                    </a>
                </p>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <h3 class="text-3xl font-bold text-center text-gray-800 mb-12">Mengapa Memilih RANET?</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center p-6 bg-white rounded-lg shadow-lg">
                    <i class="fas fa-bolt text-blue-600 text-4xl mb-4"></i>
                    <h4 class="text-xl font-bold text-gray-800 mb-2">Kecepatan Tinggi</h4>
                    <p class="text-gray-600">Internet super cepat hingga 100 Mbps untuk semua kebutuhan digital Anda</p>
                </div>
                <div class="text-center p-6 bg-white rounded-lg shadow-lg">
                    <i class="fas fa-shield-alt text-blue-600 text-4xl mb-4"></i>
                    <h4 class="text-xl font-bold text-gray-800 mb-2">Jaringan Stabil</h4>
                    <p class="text-gray-600">Koneksi yang stabil dan handal dengan uptime 99.9%</p>
                </div>
                <div class="text-center p-6 bg-white rounded-lg shadow-lg">
                    <i class="fas fa-headset text-blue-600 text-4xl mb-4"></i>
                    <h4 class="text-xl font-bold text-gray-800 mb-2">Support 24/7</h4>
                    <p class="text-gray-600">Tim support yang siap membantu Anda kapan saja</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Packages Section -->
    <section id="packages" class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <h3 class="text-3xl font-bold text-center text-gray-800 mb-4">Paket Layanan Internet</h3>
            <p class="text-center text-gray-600 mb-12">Pilih paket yang sesuai dengan kebutuhan Anda</p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-{{ count($packages) > 3 ? '4' : count($packages) }} gap-8 max-w-6xl mx-auto">
                @foreach($packages as $package)
                <div class="bg-white border-2 border-gray-200 rounded-xl p-6 hover:border-blue-500 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 relative overflow-hidden">

                    @if($package->name == 'Paket Premium')
                    <!-- Popular Badge -->
                    <div class="absolute top-0 right-0 bg-gradient-to-r from-orange-400 to-red-500 text-white text-xs px-3 py-1 rounded-bl-lg font-semibold">
                        TERPOPULER
                    </div>
                    @endif

                    <!-- Background Pattern -->
                    <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-blue-100 to-transparent rounded-full transform translate-x-8 -translate-y-8"></div>

                    <!-- Package Header -->
                    <div class="text-center mb-6">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full mb-4">
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
                        <h4 class="font-bold text-xl text-gray-800 mb-2">{{ $package->name }}</h4>
                    </div>

                    <!-- Price -->
                    <div class="text-center mb-6">
                        <div class="text-3xl font-bold text-gray-800 mb-1">
                            Rp {{ number_format($package->price, 0, ',', '.') }}
                        </div>
                        <div class="text-gray-500 text-sm">/bulan</div>
                    </div>

                    <!-- Features -->
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center">
                            <i class="fas fa-tachometer-alt text-blue-500 mr-3 w-5"></i>
                            <span class="text-gray-700 font-medium">{{ $package->speed }}</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-infinity text-green-500 mr-3 w-5"></i>
                            <span class="text-gray-700">Unlimited Kuota</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-headset text-purple-500 mr-3 w-5"></i>
                            <span class="text-gray-700">Support 24/7</span>
                        </div>
                        @if($package->name != 'Paket Basic')
                        <div class="flex items-center">
                            <i class="fas fa-tools text-orange-500 mr-3 w-5"></i>
                            <span class="text-gray-700">Free Instalasi</span>
                        </div>
                        @endif
                        @if($package->name == 'Paket Business' || $package->name == 'Paket Ultra')
                        <div class="flex items-center">
                            <i class="fas fa-user-tie text-indigo-500 mr-3 w-5"></i>
                            <span class="text-gray-700">Dedicated Support</span>
                        </div>
                        @endif
                    </div>

                    <!-- Description -->
                    <p class="text-sm text-gray-600 text-center mb-6 leading-relaxed">{{ $package->description }}</p>

                    <!-- CTA Button -->
                    <div class="text-center">
                        <a href="{{ route('registration.form') }}"
                            class="w-full inline-block bg-gradient-to-r from-blue-500 to-indigo-600 text-white py-3 px-6 rounded-lg font-semibold hover:from-blue-600 hover:to-indigo-700 transition-all duration-200 transform hover:scale-105">
                            Pilih Paket Ini
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Additional Info -->
            <div class="text-center mt-12">
                <p class="text-gray-600 mb-4">Semua paket sudah termasuk:</p>
                <div class="flex flex-wrap justify-center gap-6 text-sm text-gray-700">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        <span>Modem WiFi</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        <span>Instalasi Rp.350.000,-*</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        <span>Garansi 1 Tahun</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        <span>No FUP</span>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-4">*Syarat dan ketentuan berlaku</p>
            </div>
        </div>
    </section>

    <!-- Quick Links Section -->
    <section class="bg-gray-100 py-16">
        <div class="container mx-auto px-4">
            <h3 class="text-3xl font-bold text-center text-gray-800 mb-12">Akses Cepat</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white p-8 rounded-lg shadow-lg text-center">
                    <i class="fas fa-user-plus text-blue-600 text-5xl mb-4"></i>
                    <h4 class="text-2xl font-bold text-gray-800 mb-4">Pelanggan Baru</h4>
                    <p class="text-gray-600 mb-6">Daftar sebagai pelanggan baru dan nikmati internet berkualitas tinggi</p>
                    <a href="{{ route('registration.form') }}"
                        class="bg-blue-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-700 transition-colors">
                        Daftar Sekarang
                    </a>
                </div>
                <div class="bg-white p-8 rounded-lg shadow-lg text-center">
                    <i class="fas fa-cog text-green-600 text-5xl mb-4"></i>
                    <h4 class="text-2xl font-bold text-gray-800 mb-4">Layanan Tambahan</h4>
                    <p class="text-gray-600 mb-6">Upgrade paket atau tambah layanan untuk pelanggan existing</p>
                    <a href="{{ route('service.application.form') }}"
                        class="bg-green-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-green-700 transition-colors">
                        Ajukan Layanan
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-16">
        <div class="container mx-auto px-4">
            <h3 class="text-3xl font-bold text-center text-gray-800 mb-12">Hubungi Kami</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h4 class="text-xl font-bold text-gray-800 mb-4">Informasi Kontak</h4>
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <i class="fas fa-phone text-blue-600 mr-4"></i>
                            <span>-</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fab fa-whatsapp text-green-600 mr-4"></i>
                            <span>-</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-envelope text-blue-600 mr-4"></i>
                            <span>-</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-map-marker-alt text-blue-600 mr-4"></i>
                            <Kec.>Ds. Wonodadi Kec. Plantungan Kab.Kendal</span>
                        </div>
                    </div>
                </div>
                <div>
                    <h4 class="text-xl font-bold text-gray-800 mb-4">Jam Operasional</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span>Senin - Jumat</span>
                            <span>08:00 - 17:00 WIB</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Sabtu</span>
                            <span>08:00 - 15:00 WIB</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Minggu</span>
                            <span>Tutup</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Admin Section -->
    <!-- <section class="bg-gray-800 text-white py-16">
        <div class="container mx-auto px-4 text-center">
            <h3 class="text-3xl font-bold mb-4">Portal Admin</h3>
            <p class="text-xl mb-8">Akses portal admin untuk mengelola pelanggan dan layanan</p>
            <a href="/admin"
                class="bg-blue-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-blue-700 transition-colors">
                <i class="fas fa-user-shield mr-2"></i>
                Login Admin
            </a>
        </div>
    </section> -->

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h5 class="text-lg font-bold mb-4">RANET</h5>
                    <p class="text-gray-400">Penyedia layanan internet terpercaya dengan teknologi terdepan untuk memenuhi kebutuhan digital Anda.</p>
                </div>
                <div>
                    <h5 class="text-lg font-bold mb-4">Layanan</h5>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="{{ route('registration.form') }}" class="hover:text-white">Pendaftaran Baru</a></li>
                        <li><a href="{{ route('service.application.form') }}" class="hover:text-white">Layanan Tambahan</a></li>
                        <!-- <li><a href="/admin" class="hover:text-white">Portal Admin</a></li> -->
                    </ul>
                </div>
                <div>
                    <h5 class="text-lg font-bold mb-4">Kontak</h5>
                    <ul class="space-y-2 text-gray-400">
                        <li>Telepon: (021) 1234-5678</li>
                        <li>WhatsApp: 0812-3456-7890</li>
                        <li>Email: info@ranet.com</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2025 RANET. Semua hak dilindungi.</p>
            </div>
        </div>
    </footer>

    <!-- Mobile Menu & Smooth Scrolling JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile Menu Toggle
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const mobileMenu = document.getElementById('mobile-menu');

            if (mobileMenuBtn && mobileMenu) {
                mobileMenuBtn.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');

                    // Toggle icon
                    const icon = mobileMenuBtn.querySelector('i');
                    if (mobileMenu.classList.contains('hidden')) {
                        icon.className = 'fas fa-bars text-xl';
                    } else {
                        icon.className = 'fas fa-times text-xl';
                    }
                });

                // Close mobile menu when clicking on links
                const mobileLinks = mobileMenu.querySelectorAll('a');
                mobileLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        if (this.getAttribute('href').startsWith('#')) {
                            mobileMenu.classList.add('hidden');
                            const icon = mobileMenuBtn.querySelector('i');
                            icon.className = 'fas fa-bars text-xl';
                        }
                    });
                });

                // Close mobile menu when clicking outside
                document.addEventListener('click', function(event) {
                    if (!mobileMenuBtn.contains(event.target) && !mobileMenu.contains(event.target)) {
                        mobileMenu.classList.add('hidden');
                        const icon = mobileMenuBtn.querySelector('i');
                        icon.className = 'fas fa-bars text-xl';
                    }
                });
            }

            // Smooth Scrolling
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>