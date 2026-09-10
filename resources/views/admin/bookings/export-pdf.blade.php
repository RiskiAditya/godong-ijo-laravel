<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Booking - {{ now()->format('d M Y') }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 20mm 15mm;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 9px;
            line-height: 1.5;
            color: #1a1a1a;
            background: white;
        }
        
        .document {
            max-width: 100%;
        }
        
        /* Header */
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e5e5e5;
        }
        
        .company-info h1 {
            font-size: 18px;
            font-weight: 600;
            color: #2d5a27;
            margin-bottom: 2px;
            letter-spacing: -0.3px;
        }
        
        .company-info p {
            font-size: 10px;
            color: #737373;
            font-weight: 400;
        }
        
        .report-meta {
            text-align: right;
        }
        
        .report-title {
            font-size: 11px;
            font-weight: 600;
            color: #404040;
            margin-bottom: 6px;
        }
        
        .report-date {
            font-size: 9px;
            color: #737373;
        }
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: #fafafa;
            padding: 12px 14px;
            border-radius: 6px;
            border-left: 3px solid #2d5a27;
        }
        
        .stat-label {
            font-size: 8.5px;
            color: #737373;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 4px;
        }
        
        .stat-value {
            font-size: 16px;
            font-weight: 700;
            color: #1a1a1a;
            letter-spacing: -0.5px;
        }
        
        .stat-value small {
            font-size: 10px;
            font-weight: 500;
            color: #737373;
            margin-left: 3px;
        }
        
        /* Table */
        .table-container {
            margin-bottom: 25px;
        }
        
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border: 1px solid #e5e5e5;
            border-radius: 6px;
            overflow: hidden;
        }
        
        thead {
            background: linear-gradient(180deg, #2d5a27 0%, #265020 100%);
        }
        
        th {
            padding: 10px 8px;
            text-align: left;
            font-size: 8.5px;
            font-weight: 600;
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border-bottom: 2px solid #1e3d1a;
        }
        
        th.align-right {
            text-align: right;
        }
        
        td {
            padding: 9px 8px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 9px;
            color: #404040;
        }
        
        td.align-right {
            text-align: right;
        }
        
        tbody tr {
            background: white;
            transition: background 0.15s;
        }
        
        tbody tr:nth-child(even) {
            background: #fafafa;
        }
        
        tbody tr:hover {
            background: #f5f9f5;
        }
        
        tbody tr:last-child td {
            border-bottom: none;
        }
        
        .booking-code {
            font-family: 'Courier New', monospace;
            font-size: 8.5px;
            font-weight: 600;
            color: #2d5a27;
        }
        
        .customer-name {
            font-weight: 500;
            color: #1a1a1a;
        }
        
        .customer-contact {
            font-size: 8px;
            color: #737373;
            margin-top: 1px;
        }
        
        .package-name {
            font-weight: 500;
        }
        
        .date-text {
            color: #737373;
            font-size: 8.5px;
        }
        
        .price-text {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: #1a1a1a;
        }
        
        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 7.5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .status-badge.paid {
            background: #dcfce7;
            color: #166534;
        }
        
        .status-badge.pending {
            background: #fef3c7;
            color: #92400e;
        }
        
        .status-badge.cancelled {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .status-badge.expired {
            background: #f3f4f6;
            color: #4b5563;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #a3a3a3;
        }
        
        .empty-state svg {
            width: 48px;
            height: 48px;
            margin-bottom: 12px;
            opacity: 0.4;
        }
        
        /* Footer */
        .report-footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e5e5;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .footer-note {
            font-size: 8px;
            color: #a3a3a3;
        }
        
        .footer-page {
            font-size: 8px;
            color: #737373;
        }
        
        /* Print Styles */
        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            
            .no-print {
                display: none !important;
            }
            
            .report-footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
            }
        }
        
        /* Action Buttons */
        .action-bar {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 12px;
            background: white;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            z-index: 1000;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background: #2d5a27;
            color: white;
        }
        
        .btn-primary:hover {
            background: #1e3d1a;
        }
        
        .btn-secondary {
            background: #f3f4f6;
            color: #404040;
        }
        
        .btn-secondary:hover {
            background: #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="document">
        <!-- Header -->
        <div class="report-header">
            <div class="company-info">
                <h1>Godong Ijo</h1>
                <p>Ecotainment & Resto</p>
            </div>
            <div class="report-meta">
                <div class="report-title">Laporan Data Booking</div>
                <div class="report-date">{{ now()->translatedFormat('d F Y, H:i') }} WIB</div>
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Booking</div>
                <div class="stat-value">{{ $bookings->count() }} <small>transaksi</small></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Pengunjung</div>
                <div class="stat-value">{{ number_format($bookings->sum('jumlah_orang')) }} <small>orang</small></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Pendapatan</div>
                <div class="stat-value">Rp {{ number_format($bookings->sum('total_harga') / 1000000, 1) }}jt</div>
            </div>
        </div>
        
        <!-- Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 11%;">Kode Booking</th>
                        <th style="width: 17%;">Pelanggan</th>
                        <th style="width: 16%;">Paket Wisata</th>
                        <th style="width: 9%;">Kunjungan</th>
                        <th style="width: 7%;" class="align-right">Jumlah</th>
                        <th style="width: 12%;" class="align-right">Total Harga</th>
                        <th style="width: 9%;">Status</th>
                        <th style="width: 10%;">Pembayaran</th>
                        <th style="width: 9%;">Dibuat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                    <tr>
                        <td>
                            <div class="booking-code">{{ $booking->kode_booking }}</div>
                        </td>
                        <td>
                            <div class="customer-name">{{ $booking->nama_lengkap }}</div>
                            <div class="customer-contact">{{ $booking->no_hp }}</div>
                        </td>
                        <td>
                            <div class="package-name">{{ $booking->paketWisata->nama_paket ?? 'N/A' }}</div>
                        </td>
                        <td>
                            <div class="date-text">
                                {{ $booking->jadwal ? $booking->jadwal->tanggal->format('d/m/Y') : '-' }}
                            </div>
                        </td>
                        <td class="align-right">{{ $booking->jumlah_orang }} org</td>
                        <td class="align-right">
                            <div class="price-text">{{ number_format($booking->total_harga / 1000, 0) }}k</div>
                        </td>
                        <td>
                            <span class="status-badge {{ $booking->status }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="date-text">
                                {{ $booking->pembayaran->payment_type ?? '-' }}
                            </div>
                        </td>
                        <td>
                            <div class="date-text">{{ $booking->created_at->format('d/m/Y') }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9">
                            <div class="empty-state">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <div>Tidak ada data booking</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Footer -->
        <div class="report-footer">
            <div class="footer-note">
                Laporan ini digenerate secara otomatis oleh sistem
            </div>
            <div class="footer-page">
                Halaman 1 dari 1
            </div>
        </div>
    </div>
    
    <!-- Action Buttons -->
    <div class="action-bar no-print">
        <button onclick="window.print()" class="btn btn-primary">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Print / Save PDF
        </button>
        <button onclick="goBack()" class="btn btn-secondary">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </button>
    </div>
    
    <script>
        function goBack() {
            // Try to go back in history
            if (window.history.length > 1) {
                window.history.back();
            } else {
                // If no history, go to bookings page
                window.location.href = '{{ route('admin.bookings.index') }}';
            }
        }
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + P for print
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
            
            // ESC to go back
            if (e.key === 'Escape') {
                goBack();
            }
        });
    </script>
</body>
</html>
