# Requirements Document

## Introduction

Fitur Booking & Payment dengan Midtrans Sandbox memungkinkan pengunjung landing page Godong Ijo untuk melakukan pemesanan paket wisata dan membayar secara online tanpa perlu login atau registrasi. Sistem ini mengintegrasikan form booking dengan payment gateway Midtrans menggunakan Snap API dalam mode sandbox untuk testing, serta mengelola data pemesanan dan status pembayaran melalui webhook notification.

## Glossary

- **Booking_Modal**: Popup modal yang berisi form pemesanan paket wisata
- **Booking_Form**: Form input untuk data pemesanan (nama, email, no HP, tanggal, jumlah orang)
- **Booking_Controller**: Laravel controller yang menangani request pemesanan dan generate Snap Token
- **Midtrans_Snap**: Payment gateway interface dari Midtrans untuk menampilkan halaman pembayaran
- **Snap_Token**: Token unik yang di-generate oleh Midtrans untuk inisiasi pembayaran
- **Webhook_Handler**: Endpoint yang menerima notification dari Midtrans tentang status pembayaran
- **Pemesanan_Record**: Record di database tabel pemesanan yang menyimpan data booking
- **Pembayaran_Record**: Record di database tabel pembayaran yang menyimpan data transaksi dan status payment
- **Order_ID**: Unique identifier untuk transaksi pembayaran dalam format `ORDER-{timestamp}-{random}`
- **Transaction_ID**: ID transaksi dari Midtrans setelah pembayaran berhasil
- **Payment_Status**: Status pembayaran (pending, success, failed, expired)

## Requirements

### Requirement 1: Booking Modal Display

**User Story:** Sebagai pengunjung, saya ingin melihat popup modal booking ketika klik tombol "Pesan Sekarang", sehingga saya dapat mengisi data pemesanan dengan mudah.

#### Acceptance Criteria

1. WHEN user clicks button "Pesan Sekarang" pada paket wisata, THE Booking_Modal SHALL display dengan backdrop blur
2. THE Booking_Modal SHALL contain Booking_Form dengan semua field yang diperlukan
3. THE Booking_Modal SHALL auto-fill field "Paket yang dipilih" dengan nama paket yang dipilih (readonly)
4. WHEN Booking_Modal is displayed, THE page scrolling SHALL be disabled
5. WHEN user clicks backdrop atau tombol close, THE Booking_Modal SHALL close dan page scrolling SHALL be enabled kembali

### Requirement 2: Booking Form Input Validation

**User Story:** Sebagai pengunjung, saya ingin form booking memvalidasi input saya, sehingga saya tidak mengirim data yang tidak valid.

#### Acceptance Criteria

1. THE Booking_Form SHALL require field nama lengkap dengan minimum 3 karakter
2. THE Booking_Form SHALL require field email dengan format email yang valid (mengandung @ dan domain)
3. THE Booking_Form SHALL require field no HP/WhatsApp dengan format nomor Indonesia (minimal 10 digit, maksimal 15 digit)
4. THE Booking_Form SHALL require field tanggal kunjungan yang tidak boleh lebih awal dari hari ini
5. THE Booking_Form SHALL require field jumlah orang dengan nilai minimum 1 dan maximum 100
6. WHEN user submits form dengan data tidak valid, THE Booking_Form SHALL display error message untuk setiap field yang tidak valid
7. WHEN all fields are valid, THE submit button SHALL be enabled

### Requirement 3: Total Harga Calculation

**User Story:** Sebagai pengunjung, saya ingin melihat total harga yang dihitung otomatis, sehingga saya tahu berapa yang harus saya bayar.

#### Acceptance Criteria

1. THE Booking_Form SHALL display field "Total harga" yang calculated dan readonly
2. WHEN user mengubah jumlah orang, THE total harga SHALL be recalculated sebagai jumlah orang × harga paket
3. THE total harga SHALL be formatted dalam format Rupiah (Rp XXX.XXX)
4. WHEN paket tidak memiliki harga fixed (contoh: "Hubungi Kami", "Custom"), THE total harga field SHALL display pesan "Hubungi kami untuk harga"

### Requirement 4: Snap Token Generation

**User Story:** Sebagai sistem, saya perlu generate Snap Token dari Midtrans, sehingga user dapat melakukan pembayaran.

#### Acceptance Criteria

1. WHEN user submits valid Booking_Form, THE Booking_Controller SHALL create Pemesanan_Record dengan status "pending"
2. THE Booking_Controller SHALL generate unique Order_ID dalam format `ORDER-{timestamp}-{random}`
3. THE Booking_Controller SHALL send request ke Midtrans API untuk generate Snap_Token dengan order details
4. THE Midtrans request SHALL include customer_details (nama, email, phone), transaction_details (Order_ID, gross_amount), dan item_details
5. WHEN Midtrans returns Snap_Token successfully, THE Booking_Controller SHALL create Pembayaran_Record dengan Snap_Token dan status "pending"
6. WHEN Midtrans returns error, THE Booking_Controller SHALL return error response ke frontend tanpa membuat Pembayaran_Record

### Requirement 5: Midtrans Snap Payment Display

**User Story:** Sebagai pengunjung, saya ingin diarahkan ke halaman pembayaran Midtrans, sehingga saya dapat memilih metode pembayaran dan menyelesaikan transaksi.

#### Acceptance Criteria

1. WHEN Snap_Token berhasil di-generate, THE frontend SHALL trigger Midtrans_Snap popup dengan Snap_Token
2. THE Midtrans_Snap SHALL display payment options (Virtual Account, E-wallet, Credit Card, dll)
3. WHEN user completes payment di Midtrans_Snap, THE Midtrans_Snap SHALL close dan redirect ke finish URL
4. WHEN user cancels payment atau closes popup, THE Booking_Modal SHALL remain open dengan pesan informasi

### Requirement 6: Payment Webhook Handler

**User Story:** Sebagai sistem, saya perlu menerima notification dari Midtrans tentang status pembayaran, sehingga status pemesanan dapat diupdate secara real-time.

#### Acceptance Criteria

1. THE Webhook_Handler SHALL accept POST request dari Midtrans notification URL
2. THE Webhook_Handler SHALL verify signature dari Midtrans untuk memastikan authenticity
3. WHEN signature is valid, THE Webhook_Handler SHALL extract transaction_status dan Order_ID dari notification payload
4. WHEN transaction_status is "capture" atau "settlement", THE Webhook_Handler SHALL update Pembayaran_Record status menjadi "success" dan update Pemesanan_Record status menjadi "paid"
5. WHEN transaction_status is "pending", THE Webhook_Handler SHALL update Pembayaran_Record status menjadi "pending" tanpa mengubah Pemesanan_Record
6. WHEN transaction_status is "deny", "cancel", atau "expire", THE Webhook_Handler SHALL update Pembayaran_Record status menjadi "failed" dan update Pemesanan_Record status menjadi "cancelled"
7. THE Webhook_Handler SHALL save complete Midtrans response payload ke field midtrans_response dalam format JSON
8. THE Webhook_Handler SHALL return HTTP 200 response ke Midtrans untuk acknowledge notification

### Requirement 7: Database Schema for Pemesanan

**User Story:** Sebagai sistem, saya perlu menyimpan data pemesanan ke database, sehingga data dapat dikelola dan dilacak.

#### Acceptance Criteria

1. THE database SHALL have tabel "pemesanan" dengan kolom: id, nama, email, no_hp, paket_wisata_id, tanggal_kunjungan, jumlah_orang, total_harga, status, created_at, updated_at
2. THE kolom paket_wisata_id SHALL be foreign key yang reference ke tabel paket_wisata
3. THE kolom status SHALL be enum dengan nilai: "pending", "paid", "cancelled", "expired"
4. THE kolom total_harga SHALL store nilai decimal dengan precision 12 dan scale 2
5. THE tabel pemesanan SHALL have index pada kolom created_at untuk query performance

### Requirement 8: Database Schema for Pembayaran

**User Story:** Sebagai sistem, saya perlu menyimpan data pembayaran dari Midtrans ke database, sehingga riwayat transaksi dapat dilacak.

#### Acceptance Criteria

1. THE database SHALL have tabel "pembayaran" dengan kolom: id, pemesanan_id, order_id, transaction_id, payment_type, gross_amount, status, snap_token, midtrans_response, created_at, updated_at
2. THE kolom pemesanan_id SHALL be foreign key yang reference ke tabel pemesanan dengan cascade delete
3. THE kolom order_id SHALL be unique dan indexed untuk lookup cepat
4. THE kolom status SHALL be enum dengan nilai: "pending", "success", "failed", "expired"
5. THE kolom midtrans_response SHALL be JSON type untuk menyimpan complete response dari Midtrans
6. THE kolom gross_amount SHALL store nilai decimal dengan precision 12 dan scale 2

### Requirement 9: Frontend UX and Loading States

**User Story:** Sebagai pengunjung, saya ingin melihat loading indicator dan feedback visual, sehingga saya tahu sistem sedang memproses request saya.

#### Acceptance Criteria

1. WHEN user clicks submit button, THE Booking_Form SHALL display loading spinner dan disable submit button
2. THE loading state SHALL display text "Memproses pemesanan..." selama request ke backend
3. WHEN Snap_Token generation berhasil, THE loading state SHALL change menjadi "Membuka halaman pembayaran..."
4. WHEN request fails, THE Booking_Form SHALL display error notification dengan pesan error yang jelas
5. WHEN payment completes successfully, THE system SHALL display success notification dengan booking details
6. WHEN payment fails atau cancelled, THE system SHALL display appropriate error notification dengan option untuk retry

### Requirement 10: Midtrans SDK Integration

**User Story:** Sebagai developer, saya perlu mengintegrasikan Midtrans PHP SDK ke Laravel backend, sehingga dapat berkomunikasi dengan Midtrans API.

#### Acceptance Criteria

1. THE Laravel project SHALL install package "midtrans/midtrans-php" via Composer
2. THE Laravel config SHALL have file "midtrans.php" yang berisi server_key, client_key, dan is_production setting
3. THE Midtrans configuration SHALL use sandbox credentials (is_production = false)
4. THE Booking_Controller SHALL initialize Midtrans Config dengan server_key dari config
5. THE Midtrans API requests SHALL be sent ke sandbox URL (api.sandbox.midtrans.com)

### Requirement 11: CSRF Protection for Forms

**User Story:** Sebagai sistem, saya perlu memproteksi form dari CSRF attacks, sehingga hanya request yang valid yang diproses.

#### Acceptance Criteria

1. THE Booking_Form SHALL include CSRF token dalam setiap POST request
2. THE Booking_Controller SHALL verify CSRF token untuk setiap incoming request
3. WHEN CSRF token is invalid atau missing, THE Booking_Controller SHALL return 419 error response
4. THE Webhook_Handler SHALL be excluded dari CSRF verification karena dipanggil oleh external service (Midtrans)

### Requirement 12: Response Error Handling

**User Story:** Sebagai pengunjung, saya ingin melihat pesan error yang jelas ketika terjadi kesalahan, sehingga saya tahu apa yang harus dilakukan.

#### Acceptance Criteria

1. WHEN network request fails, THE frontend SHALL display error "Koneksi gagal. Silakan cek koneksi internet Anda."
2. WHEN server returns 500 error, THE frontend SHALL display error "Terjadi kesalahan pada server. Silakan coba lagi."
3. WHEN Midtrans API returns error, THE frontend SHALL display error message dari Midtrans response
4. WHEN validation fails, THE frontend SHALL display specific error untuk setiap field yang tidak valid
5. THE error notifications SHALL auto-dismiss setelah 5 detik atau dapat di-close manual oleh user
