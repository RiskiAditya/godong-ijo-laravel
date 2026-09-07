# 🔄 Konversi Foto ke WebP

## File yang Perlu Dikonversi ke WebP:

### 1. Aero Glider
**Source:** `public/images/education/aero-glider.jpg`
**Target:** `public/images/education/aero-glider.webp`
**Action:** Perlu convert JPG → WebP

### 2. Fast Learning Camp
**Source:** `public/images/education/fast-learning-camp.jpg`
**Target:** `public/images/education/fast-learning-camp.webp`
**Action:** Perlu convert JPG → WebP

### 3. Profesor Cilik (JPG)
**Source:** `public/images/education/profesor-cilik.webp` ✅ (sudah ada)
**Target:** `public/images/education/profesor-cilik.jpg`
**Action:** Perlu convert WebP → JPG (fallback)

---

## 🛠️ Cara Konversi:

### Option 1: Online Tool (Recommended)
1. Buka **https://squoosh.app**
2. Upload file JPG
3. Pilih WebP format di sebelah kanan
4. Set quality 80-85%
5. Download hasil

### Option 2: Bulk Converter
1. Buka **https://cloudconvert.com/jpg-to-webp**
2. Upload multiple files
3. Convert all
4. Download ZIP

### Option 3: Windows PowerShell (Manual)
```powershell
# Install webp tools jika belum ada
# Download dari: https://developers.google.com/speed/webp/download

# Convert command (jika sudah install cwebp)
cwebp -q 85 "public/images/education/aero-glider.jpg" -o "public/images/education/aero-glider.webp"
cwebp -q 85 "public/images/education/fast-learning-camp.jpg" -o "public/images/education/fast-learning-camp.webp"

# Convert WebP to JPG (jika sudah install dwebp + ImageMagick)
magick "public/images/education/profesor-cilik.webp" "public/images/education/profesor-cilik.jpg"
```

---

## ✅ Setelah Konversi Selesai:

Files yang harus ada di `public/images/education/`:
- ✅ `aero-glider.webp` (NEW)
- ✅ `aero-glider.jpg` (sudah ada)
- ✅ `profesor-cilik.webp` (sudah ada)
- ✅ `profesor-cilik.jpg` (NEW)
- ✅ `fast-learning-camp.webp` (NEW)
- ✅ `fast-learning-camp.jpg` (sudah ada)

---

## 🔧 Quick Action - Manual Conversion

**Jika tidak punya tools:**
1. Upload `aero-glider.jpg` ke Squoosh.app → download sebagai `aero-glider.webp`
2. Upload `fast-learning-camp.jpg` ke Squoosh.app → download sebagai `fast-learning-camp.webp`
3. Upload `profesor-cilik.webp` ke Squoosh.app → download sebagai `profesor-cilik.jpg` (pilih JPG format)

**Target Quality:**
- WebP: 80-85%
- JPG: 85-90%
- Max file size: 200KB

---

Setelah selesai, lanjutkan ke update controller!
