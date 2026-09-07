# 📊 Status Foto Wisata Edukasi

**Last Update:** August 14, 2026

---

## ✅ Foto yang Sudah Ada

### 🌿 Environmental Programs
| Program | File | Status | Quality Check |
|---------|------|--------|---------------|
| **Learning Animals** | `learning-animals.webp` / `.jpg` | ✅ **ADA** | ✅ Ready to use |
| **Urban Farming** | `urban-farming.webp` / `.jpg` | ✅ **ADA** | ✅ Ready to use |
| Fast Learning Camp | `category-environmental.webp` | ⚠️ Generic placeholder | Need specific photo |

### 🔬 Science Programs
| Program | File | Status | Quality Check |
|---------|------|--------|---------------|
| Aero Glider | - | ❌ **BELUM ADA** | Need to add |
| Profesor Cilik | - | ❌ **BELUM ADA** | Need to add |

### 🎨 Art Programs
| Program | File | Status | Quality Check |
|---------|------|--------|---------------|
| Learning Batik | `category-art.webp` | ⚠️ Generic placeholder | Need specific photo |
| Traditional Dance | `category-art.webp` | ⚠️ Generic placeholder | Need specific photo |
| Junior MasterChef | `category-art.webp` | ⚠️ Generic placeholder | Need specific photo |

### 🎯 Category Cards
| Category | File | Status |
|----------|------|--------|
| Environmental | `category-environmental.webp` / `.jpg` | ✅ ADA |
| Science | `category-science.webp` / `.jpg` | ✅ ADA |
| Art | `category-art.webp` / `.jpg` | ✅ ADA |

### 🏆 Hero & Others
| Item | File | Status |
|------|------|--------|
| Hero Background | `hero-background.webp` / `.jpg` | ✅ ADA |
| Testimonial | `testimonial-photo.webp` / `.jpg` | ✅ ADA |
| Placeholders | `placeholder-*.webp` / `.jpg` | ✅ ADA |

---

## 📊 Summary Statistics

**Total Photos in Folder:** 18 files (9 webp + 9 jpg)

**Status Breakdown:**
- ✅ **Fully Complete:** 2 programs (Learning Animals, Urban Farming)
- ⚠️ **Using Placeholder:** 4 programs (Fast Learning Camp, Learning Batik, Traditional Dance, Junior MasterChef)
- ❌ **Missing:** 2 programs (Aero Glider, Profesor Cilik)

**Completion:** 33% (2 out of 6 specific programs)

---

## 🎯 Priority Actions Needed

### 🔴 HIGH PRIORITY - Missing Photos (Science Programs)

Science programs saat ini **tidak ada foto sama sekali**, hanya icon. Perlu segera ditambahkan!

#### 1. Aero Glider
**File Needed:** `aero-glider.webp` + `aero-glider.jpg`
**Size:** 800x800px (square) atau 1200x800px (landscape)
**Content:** Kids making/flying paper gliders
**References:** Check `PHOTO_REFERENCES.md` → Section 1

#### 2. Profesor Cilik
**File Needed:** `profesor-cilik.webp` + `profesor-cilik.jpg`
**Size:** 800x800px (square) atau 1200x800px (landscape)
**Content:** Kids with lab coat doing experiments
**References:** Check `PHOTO_REFERENCES.md` → Section 2

---

### 🟡 MEDIUM PRIORITY - Generic Placeholders (Art Programs)

Art programs saat ini menggunakan foto generic `category-art.webp` yang sama untuk 3 program berbeda.

#### 3. Learning Batik
**File Needed:** `learning-batik.webp` + `learning-batik.jpg`
**Size:** 1200x800px (landscape)
**Content:** Kids learning batik with canting
**References:** Check `PHOTO_REFERENCES.md` → Section 3

**Current Code:**
```php
'image' => asset('images/education/category-art.webp'),
```

**Should Update To:**
```php
'image' => asset('images/education/learning-batik.webp'),
```

#### 4. Traditional Dance
**File Needed:** `traditional-dance.webp` + `traditional-dance.jpg`
**Size:** 1200x800px (landscape)
**Content:** Kids in traditional dance costume
**References:** Check `PHOTO_REFERENCES.md` → Section 4

**Current Code:**
```php
'image' => asset('images/education/category-art.webp'),
```

**Should Update To:**
```php
'image' => asset('images/education/traditional-dance.webp'),
```

#### 5. Junior MasterChef
**File Needed:** `junior-masterchef.webp` + `junior-masterchef.jpg`
**Size:** 1200x800px (landscape)
**Content:** Kids cooking with chef hat & apron
**References:** Check `PHOTO_REFERENCES.md` → Section 5

**Current Code:**
```php
'image' => asset('images/education/category-art.webp'),
```

**Should Update To:**
```php
'image' => asset('images/education/junior-masterchef.webp'),
```

#### 6. Fast Learning Camp
**File Needed:** `fast-learning-camp.webp` + `fast-learning-camp.jpg`
**Size:** 1200x800px (landscape)
**Content:** Outdoor camp activities, team building
**References:** Check `PHOTO_REFERENCES.md` → Section (Environment)

**Current Code:**
```php
'image' => asset('images/education/category-environmental.webp'),
```

**Should Update To:**
```php
'image' => asset('images/education/fast-learning-camp.webp'),
```

---

## 🔄 Next Steps

### Step 1: Add Missing Photos (Science Programs) 🔴
```bash
# Files to add:
public/images/education/
├── aero-glider.webp
├── aero-glider.jpg
├── profesor-cilik.webp
└── profesor-cilik.jpg
```

**Science programs currently have NO photos** (only using icons), so this is most important!

### Step 2: Update Controller References 📝
Once photos are added, update `app/Http/Controllers/StaticPageController.php`:

```php
// Science Programs - ADD image paths
'sciencePrograms' => [
    [
        'title' => 'Aero Glider',
        'icon' => 'ti-plane',
        'image' => asset('images/education/aero-glider.webp'),
        'imageFallback' => asset('images/education/aero-glider.jpg'),
        // ... rest
    ],
    [
        'title' => 'Profesor Cilik',
        'icon' => 'ti-microscope',
        'image' => asset('images/education/profesor-cilik.webp'),
        'imageFallback' => asset('images/education/profesor-cilik.jpg'),
        // ... rest
    ],
],
```

### Step 3: Replace Art Program Placeholders 🎨
Add specific photos and update controller references from generic `category-art.webp` to specific files.

### Step 4: Clear Cache & Test
```bash
php artisan cache:clear
php artisan view:clear
```

Then visit: http://127.0.0.1:8000/wisata-edukasi

---

## 📸 Photo Specifications Reminder

### Format & Size
- **Primary:** `.webp` (modern, smaller file size)
- **Fallback:** `.jpg` (browser compatibility)
- **Max File Size:** 200KB per file
- **Quality:** 80-85% for webp, 85-90% for jpg

### Dimensions
- **Square cards:** 800x800px (1:1 ratio)
- **Landscape cards:** 1200x800px (3:2 ratio)
- **Hero images:** 1920x1080px (16:9 ratio)

### Style Guidelines
- ✅ Natural lighting
- ✅ Kids looking happy & engaged
- ✅ Action/activity shots (not static poses)
- ✅ Clean background
- ✅ Educational atmosphere
- ❌ Avoid stock photo "too perfect" look

---

## 🔗 Reference Documents

1. **`PHOTO_REFERENCES.md`** - Sumber foto & referensi untuk download
2. **`PHOTO_CHECKLIST.md`** - Quick checklist format compact
3. **`EDUCATION_PHOTO_GUIDE.md`** - Panduan lengkap dengan specs detail

---

## ✅ Current Folder Contents

```
public/images/education/
├── category-art.jpg ✅
├── category-art.webp ✅
├── category-environmental.jpg ✅
├── category-environmental.webp ✅
├── category-science.jpg ✅
├── category-science.webp ✅
├── hero-background.jpg ✅
├── hero-background.webp ✅
├── learning-animals.jpg ✅
├── learning-animals.webp ✅
├── placeholder-blur.jpg ✅
├── placeholder-blur.webp ✅
├── placeholder-education.jpg ✅
├── renewable-energy.jpg ✅
├── renewable-energy.webp ✅
├── testimonial-photo.jpg ✅
├── testimonial-photo.webp ✅
├── urban-farming.jpg ✅
└── urban-farming.webp ✅

MISSING (Need to add):
├── aero-glider.webp ❌
├── aero-glider.jpg ❌
├── profesor-cilik.webp ❌
├── profesor-cilik.jpg ❌
├── learning-batik.webp ❌
├── learning-batik.jpg ❌
├── traditional-dance.webp ❌
├── traditional-dance.jpg ❌
├── junior-masterchef.webp ❌
├── junior-masterchef.jpg ❌
├── fast-learning-camp.webp ❌
└── fast-learning-camp.jpg ❌
```

**Total Files in Folder:** 18 / 30 (60% complete for folder)
**Specific Program Photos:** 2 / 6 complete (33%)

---

## 📞 Need Help?

- Refer to `PHOTO_REFERENCES.md` for download sources
- Use free stock photos from Unsplash, Pexels, or Pixabay
- Check NASA education resources for science program photos
- Consider hiring photographer for authentic Godong Ijo photos

---

**Priority Order:**
1. 🔴 **Aero Glider** (HIGH - No photo at all)
2. 🔴 **Profesor Cilik** (HIGH - No photo at all)
3. 🟡 Learning Batik (MEDIUM - Using generic)
4. 🟡 Traditional Dance (MEDIUM - Using generic)
5. 🟡 Junior MasterChef (MEDIUM - Using generic)
6. 🟢 Fast Learning Camp (LOW - Category photo OK)

**Estimated Time to Complete:**
- Download/source photos: 3-4 hours
- Edit & optimize: 2-3 hours
- Update controller: 30 minutes
- **Total: 6-8 hours**
