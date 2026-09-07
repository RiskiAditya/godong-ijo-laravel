# 🎨 Admin Panel - Visual Guide

## 🖼️ Layout Structure

```
┌─────────────────────────────────────────────────────────┐
│                  ADMIN PANEL LAYOUT                      │
└─────────────────────────────────────────────────────────┘

Desktop View (> 1024px):
┌──────────────┬────────────────────────────────────────┐
│              │  TOP BAR                               │
│   SIDEBAR    │  [☰]  Page Title    [👤 Admin Name]   │
│              ├────────────────────────────────────────┤
│  Godong Ijo  │                                        │
│  Admin Panel │                                        │
│              │        MAIN CONTENT AREA               │
│ ─────────── │                                        │
│ 📊 Dashboard │     [Cards, Tables, Forms, etc]        │
│ 📋 Booking   │                                        │
│ 📦 Paket     │                                        │
│              │                                        │
│ 🚪 Logout    │                                        │
└──────────────┴────────────────────────────────────────┘

Mobile View (< 768px):
┌────────────────────────────────────────┐
│ [☰]  Page Title    [👤 Admin]         │ ← Top Bar
├────────────────────────────────────────┤
│                                        │
│        MAIN CONTENT AREA               │
│                                        │
│     [Responsive Content]               │
│                                        │
└────────────────────────────────────────┘

[Sidebar hidden, shown on hamburger click]
```

---

## 🎨 Color Scheme

```
Primary Colors:
┌────────────┬────────────┬────────────┐
│  Green 700 │  Green 800 │  Green 900 │
│  #15803d   │  #166534   │  #14532d   │
│  Sidebar   │   Hover    │   Accent   │
└────────────┴────────────┴────────────┘

Status Colors:
┌────────────┬────────────┬────────────┬────────────┐
│   Green    │   Yellow   │    Red     │    Gray    │
│  Success   │  Warning   │   Danger   │ Secondary  │
│   Paid     │  Pending   │ Cancelled  │  Expired   │
└────────────┴────────────┴────────────┴────────────┘

Background:
┌────────────┬────────────┐
│  Gray 100  │   White    │
│  #f3f4f6   │  #ffffff   │
│   Body     │   Cards    │
└────────────┴────────────┘
```

---

## 📊 Dashboard Components

### Statistics Cards
```
┌─────────────────────┐  ┌─────────────────────┐
│ Booking Hari Ini    │  │ Booking Bulan Ini   │
│  [📅 Blue Icon]     │  │  [📅 Green Icon]    │
│                     │  │                     │
│       42            │  │       328           │
└─────────────────────┘  └─────────────────────┘

┌─────────────────────┐  ┌─────────────────────┐
│ Total Pendapatan    │  │ Booking Pending     │
│  [💰 Yellow Icon]   │  │  [⏰ Red Icon]      │
│                     │  │                     │
│ Rp 49.200.000       │  │       15            │
└─────────────────────┘  └─────────────────────┘
```

### Recent Bookings Table
```
┌──────────────────────────────────────────────────────────────┐
│ Kode     │ Nama      │ Paket   │ Total  │ Status  │ Tanggal │
├──────────┼───────────┼─────────┼────────┼─────────┼─────────┤
│ BK001    │ John Doe  │ Paket A │ 150K   │ [Paid]  │ 20 Jul  │
│ BK002    │ Jane      │ Paket B │ 200K   │[Pending]│ 20 Jul  │
│ BK003    │ Bob       │ Paket A │ 150K   │ [Paid]  │ 19 Jul  │
└──────────┴───────────┴─────────┴────────┴─────────┴─────────┘
                     [Lihat Semua Booking →]
```

---

## 📋 Booking Management

### Filter Section
```
┌─────────────────────────────────────────────────────────────┐
│ Cari: [_______________]  Status: [▼Semua Status]           │
│                                                             │
│ Dari Tanggal: [📅 dd/mm/yyyy]  Sampai: [📅 dd/mm/yyyy]    │
│                                                             │
│ [🔍 Filter]  [↻ Reset]                                     │
└─────────────────────────────────────────────────────────────┘
```

### Bookings Table
```
┌────────────────────────────────────────────────────────────────┐
│ Kode   │ Nama    │ WhatsApp │ Tgl Kunjungan │ Status │ Aksi  │
├────────┼─────────┼──────────┼───────────────┼────────┼───────┤
│ BK001  │ John    │ 0812xxx  │ 25 Jul 2026   │[Paid]  │ 👁 🗑 │
│ BK002  │ Jane    │ 0813xxx  │ 26 Jul 2026   │[Pending]│👁 🗑 │
└────────┴─────────┴──────────┴───────────────┴────────┴───────┘
                    ← 1 2 3 4 5 → (Pagination)
```

### Booking Detail
```
┌─────────────────────────────────┬──────────────────────┐
│ INFORMASI BOOKING               │  UBAH STATUS         │
│                                 │                      │
│ Kode Booking: BK20260725000123  │  Status: [▼Pending] │
│ Status: [Pending Badge]         │                      │
│ Tanggal Booking: 25 Jul 10:30   │  [💾 Simpan Status] │
│ Tanggal Kunjungan: 30 Jul 2026  │                      │
│                                 ├──────────────────────┤
├─────────────────────────────────┤ INFORMASI PEMBAYARAN │
│ INFORMASI PELANGGAN             │                      │
│                                 │ Order ID: ORDER-xxx  │
│ Nama: John Doe                  │ Transaction ID: xxx  │
│ Email: john@example.com         │ Metode: Bank Transfer│
│ WhatsApp: 081234567890          │                      │
│ [💬 Kirim Pesan]                ├──────────────────────┤
│                                 │ AKSI LAINNYA         │
├─────────────────────────────────┤                      │
│ DETAIL PAKET                    │ [🗑 Hapus Booking]   │
│                                 │                      │
│ Paket: Adventure Alam           │                      │
│ Jumlah Orang: 4 orang           │                      │
│ Total: Rp 600.000               │                      │
└─────────────────────────────────┴──────────────────────┘
```

---

## 📦 Paket Wisata Management

### Paket List
```
┌─────────────────────────────────────────────────────────────┐
│  [➕ Tambah Paket Wisata]                                   │
└─────────────────────────────────────────────────────────────┘

┌────────────────────────────────────────────────────────────┐
│ Foto   │ Nama Paket        │ Harga     │ Kuota │ Status  │ Aksi │
├────────┼───────────────────┼───────────┼───────┼─────────┼──────┤
│ [IMG]  │ Adventure Alam    │ 150.000   │  50   │ Aktif   │👁✏🗑│
│        │ Petualangan seru..│           │       │[Green]  │      │
├────────┼───────────────────┼───────────┼───────┼─────────┼──────┤
│ [IMG]  │ Relax Package     │ 200.000   │  30   │Nonaktif │👁✏🗑│
│        │ Santai dan nyaman.│           │       │ [Gray]  │      │
└────────┴───────────────────┴───────────┴───────┴─────────┴──────┘
```

### Create/Edit Form
```
┌─────────────────────────────────────────────────────────────┐
│  Nama Paket *                                               │
│  [_____________________________________________________]    │
│                                                             │
│  Deskripsi *                                                │
│  [_____________________________________________________]    │
│  [                                                      ]    │
│  [                                                      ]    │
│                                                             │
│  Harga (Rp) *           Kuota Per Hari *                   │
│  [_______________]      [_______________]                   │
│                                                             │
│  Foto Paket                                                 │
│  [📁 Choose File]  Format: JPEG, PNG. Max 2MB              │
│                                                             │
│  Preview:                                                   │
│  ┌─────────────┐                                           │
│  │   [IMAGE]   │                                           │
│  └─────────────┘                                           │
│                                                             │
│  ☑ Paket Aktif (Ditampilkan di website)                    │
│                                                             │
│  [💾 Simpan Paket]  [❌ Batal]                              │
└─────────────────────────────────────────────────────────────┘
```

### Paket Detail
```
┌─────────────────────────────────┬──────────────────────┐
│ ┌─────────────────────────────┐ │  AKSI                │
│ │                             │ │                      │
│ │      [FOTO PAKET]           │ │ [✏ Edit Paket]      │
│ │                             │ │                      │
│ └─────────────────────────────┘ │ [🗑 Hapus Paket]    │
│                                 │                      │
│ # Adventure Alam                ├──────────────────────┤
│ [Aktif Badge]                   │ STATISTIK            │
│                                 │                      │
│ Deskripsi:                      │ Total Booking: 156   │
│ Nikmati petualangan seru di...  │ Booking Aktif: 24    │
│                                 │                      │
│ ┌──────────────┬──────────────┐ │                      │
│ │   Harga      │ Kuota/Hari   │ │                      │
│ │ Rp 150.000   │    50        │ │                      │
│ └──────────────┴──────────────┘ │                      │
│                                 │                      │
│ 📅 Dibuat: 20 Jul 2026 10:00    │                      │
│ ✏ Update: 21 Jul 2026 14:30     │                      │
└─────────────────────────────────┴──────────────────────┘
```

---

## 🔐 Login Page

```
         ┌─────────────────────────────────────┐
         │                                     │
         │        Godong Ijo                   │
         │       Admin Panel                   │
         │                                     │
         ├─────────────────────────────────────┤
         │                                     │
         │        Login Admin                  │
         │                                     │
         │  👤 Username                        │
         │  [_____________________________]    │
         │                                     │
         │  🔒 Password                        │
         │  [_____________________________]    │
         │                                     │
         │  ☐ Ingat saya                       │
         │                                     │
         │  [🔑 Login]                         │
         │                                     │
         ├─────────────────────────────────────┤
         │ Kredensial Default:                 │
         │ Username: admin                     │
         │ Password: admin123                  │
         └─────────────────────────────────────┘
```

---

## 🎯 Status Badges

```
Status Booking:
┌────────────┬────────────┬────────────┬────────────┐
│   Paid     │  Pending   │ Cancelled  │  Expired   │
│  [Green]   │  [Yellow]  │   [Red]    │   [Gray]   │
└────────────┴────────────┴────────────┴────────────┘

Status Paket:
┌────────────┬────────────┐
│   Aktif    │ Nonaktif   │
│  [Green]   │   [Gray]   │
└────────────┴────────────┘

Status Pembayaran:
┌────────────┬────────────┬────────────┬────────────┐
│  Berhasil  │  Pending   │   Gagal    │  Expired   │
│  [Green]   │  [Yellow]  │   [Red]    │   [Gray]   │
└────────────┴────────────┴────────────┴────────────┘
```

---

## 📱 Responsive Behavior

### Desktop (> 1024px)
- Sidebar: Always visible (256px width)
- Content: Full width minus sidebar
- Tables: Full width, all columns visible
- Cards: Grid layout (2-4 columns)

### Tablet (768-1024px)
- Sidebar: Still visible but narrower
- Content: Adjusted width
- Tables: Horizontal scroll if needed
- Cards: Grid 2-3 columns

### Mobile (< 768px)
- Sidebar: Hidden, hamburger menu
- Content: Full width
- Tables: Horizontal scroll
- Cards: Single column
- Touch-friendly buttons (larger)

---

## 🎨 Icons Usage

```
Navigation:
📊 Dashboard
📋 Booking
📦 Paket Wisata
🚪 Logout

Actions:
👁 View/Detail
✏ Edit
🗑 Delete
💾 Save
❌ Cancel
🔍 Search
↻ Reset
➕ Add

Status:
✅ Success
⚠️ Warning
❌ Error
⏰ Pending
💰 Money
📅 Calendar
👤 User
💬 Message/WhatsApp
```

---

## 💡 Interactive Elements

### Buttons
```
Primary:    [Green background, white text]
Secondary:  [Gray background, dark text]
Danger:     [Red background, white text]
Success:    [Green background, white text]
Warning:    [Yellow background, dark text]

Sizes:
Small:   py-1 px-3
Normal:  py-2 px-4
Large:   py-3 px-6
```

### Form Inputs
```
Text Input:    [White bg, gray border, green focus ring]
Select:        [White bg, gray border, dropdown arrow]
Checkbox:      [Green when checked]
File Upload:   [Custom styled button]
Textarea:      [White bg, gray border, resizable]
```

### Tables
```
Header:   [Gray 50 background]
Row:      [White background]
Hover:    [Gray 50 background]
Border:   [Gray 200 separator]
```

---

## 🌟 Animation & Effects

```
Transitions:
- All: 300ms ease
- Hover effects: scale(1.05)
- Focus rings: 2px solid green-600
- Modal backdrop: blur + fade in
- Sidebar slide: translateX animation

Flash Messages:
- Fade in: 300ms
- Stay: 5 seconds
- Fade out: 300ms
- Close button: X top-right
```

---

## 📐 Spacing & Typography

```
Spacing Scale:
- xs: 0.25rem (4px)
- sm: 0.5rem (8px)
- md: 1rem (16px)
- lg: 1.5rem (24px)
- xl: 2rem (32px)
- 2xl: 3rem (48px)

Typography:
- Headings: Bold, 1.5-2.5rem
- Body: Regular, 0.875-1rem
- Small: 0.75-0.875rem
- Monospace: For codes (Courier New)

Line Heights:
- Tight: 1.25
- Normal: 1.5
- Relaxed: 1.75
```

---

**Visual Guide ini membantu memahami layout & design admin panel tanpa perlu melihat kode!** 🎨
