# Logo RANET

## Logo Sistem Sudah Dikonfigurasi

Logo RANET sudah dikonfigurasi untuk sistem:

### **Logo Utama:**
- **File**: `ranet-logo.svg` (SVG format untuk kualitas terbaik)
- **Lokasi**: `public/images/ranet-logo.svg`
- **Ukuran**: 200x80 pixels (rasio 2.5:1)
- **Warna**: Amber (#f59e0b) dengan ikon network putih

### **Konfigurasi:**
- **Admin Panel**: Logo tampil di sidebar dan topbar
- **Brand Name**: "RANET Provider"
- **Height**: 2.5rem (40px)
- **Component**: Custom component di `resources/views/components/ranet-logo.blade.php`

### **Mengganti Logo:**

#### **Cara 1: Upload via Admin Panel (Recommended)**
1. **Buka**: Admin → Pengaturan → Pengaturan Perusahaan
2. **Edit** company setting yang aktif
3. **Upload** logo di field "Logo Perusahaan"
4. **Format**: JPG, JPEG, PNG, SVG, GIF (max 2MB)

#### **Cara 2: Replace File Langsung**
1. **Ganti file**: `public/images/ranet-logo.svg` dengan logo baru
2. **Pastikan nama sama** agar otomatis terganti
3. **Format**: SVG, PNG, JPG

#### **Format yang Didukung:**
- **JPG/JPEG**: ✅ File kecil, background solid
- **PNG**: ✅ Background transparan, kualitas bagus
- **SVG**: ✅ Scalable, kualitas terbaik
- **GIF**: ✅ Support animasi (tidak recommended)

#### **Ukuran yang Disarankan:**
- **Dimensi**: 400x200px, 600x300px, atau 800x400px
- **Rasio**: 2:1 atau 3:1 (landscape)
- **File Size**: Max 2MB (biasanya 100-500KB cukup)
- **Resolution**: 72-150 DPI untuk web

## Logo yang Sudah Terintegrasi

Logo akan muncul di:
- ✅ **Header email** - ukuran maksimal 200px (150px di mobile)
- ✅ **Footer email** - ukuran maksimal 120px (100px di mobile)
- ✅ **Website link** - www.adau.net.id (clickable)

## Responsive Design

Logo sudah dioptimalkan untuk:
- 📱 **Mobile** - ukuran otomatis menyesuaikan
- 💻 **Desktop** - tampilan optimal
- 📧 **Email clients** - kompatibel dengan berbagai email client

## Catatan

Jika logo tidak muncul, pastikan:
1. File logo ada di `public/images/ranet-logo.png`
2. Nama file sesuai (case-sensitive)
3. Format file didukung (PNG, JPG, SVG)
4. Ukuran file tidak terlalu besar
