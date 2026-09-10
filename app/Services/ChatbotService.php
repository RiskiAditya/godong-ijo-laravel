<?php

namespace App\Services;

use App\Models\AdminNotification;
use App\Models\Jadwal;
use App\Models\PaketWisata;
use App\Models\Pemesanan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChatbotService
{
    private const GREETING_KEYWORDS = ['halo', 'hai', 'hello', 'pagi', 'siang', 'sore', 'malam'];

    private const ADMIN_BOOKING_SEARCH_KEYWORDS = [
        'cari booking',
        'search booking',
        'temukan booking',
        'cari pemesanan',
        'booking atas nama',
    ];

    private const ADMIN_LOW_STOCK_KEYWORDS = [
        'paket hampir penuh',
        'kuota hampir penuh',
        'kuota menipis',
        'hampir habis',
        'hampir penuh',
    ];

    private const ADMIN_HELP_KEYWORDS = [
        'bantuan',
        'help',
        'fitur admin',
        'menu admin',
        'bisa apa',
        'cara pakai',
        'cari booking',
    ];

    private const ADMIN_FEATURE_KEYWORDS = [
        'pelanggan',
        'customer',
        'paket aktif',
        'paket tersedia',
        'aktivitas terbaru',
        'notifikasi',
        'belum dibaca',
        'pengaturan',
    ];

    private const ADMIN_SUMMARY_KEYWORDS = [
        'ringkasan',
        'summary',
        'pendapatan',
        'omzet',
        'ramai',
        'terlaris',
        'booking hari ini',
        'booking bulan',
        'pending',
        'status booking',
        'laporan',
        'dashboard',
        'performa',
        'statistik',
    ];

    private const PRICE_KEYWORDS = ['harga', 'price', 'biaya', 'tarif', 'paket'];
    private const OPENING_KEYWORDS = ['jam', 'buka', 'operasional', 'tutup'];
    private const LOCATION_KEYWORDS = ['lokasi', 'alamat', 'dimana', 'maps', 'depok'];
    private const BOOKING_KEYWORDS = ['booking', 'pesan', 'reservasi', 'kunjungan'];

    /**
     * Return a helpful answer using the live package catalog and common FAQs.
     */
    public function reply(string $message, string $audience = 'public'): array
    {
        $message = trim($message);
        $normalized = Str::lower($message);

        if ($message === '') {
            return $this->response('Tulis pertanyaanmu dulu ya. Aku bisa bantu soal paket, harga, jam buka, lokasi, dan booking.');
        }

        if ($this->containsAny($normalized, self::GREETING_KEYWORDS)) {
            return $this->response($this->greetingText($audience), [
                'quick_replies' => $this->quickReplies($audience),
            ]);
        }

        if ($audience === 'admin' && $this->extractBookingCode($message) !== null) {
            $bookingCode = $this->extractBookingCode($message);

            return $this->response($this->bookingLookup($bookingCode), [
                'quick_replies' => $this->quickReplies($audience),
            ]);
        }

        if ($audience === 'admin' && $this->containsAny($normalized, self::ADMIN_BOOKING_SEARCH_KEYWORDS)) {
            return $this->response($this->adminBookingSearch($message), [
                'quick_replies' => $this->quickReplies($audience),
            ]);
        }

        if ($audience === 'admin' && $this->containsAny($normalized, self::ADMIN_LOW_STOCK_KEYWORDS)) {
            return $this->response($this->lowQuotaPackages(), [
                'quick_replies' => $this->quickReplies($audience),
            ]);
        }

        if ($audience === 'admin' && $this->containsAny($normalized, self::ADMIN_HELP_KEYWORDS)) {
            return $this->response($this->adminHelp(), [
                'quick_replies' => $this->quickReplies($audience),
            ]);
        }

        if ($audience === 'admin' && $this->containsAny($normalized, self::ADMIN_FEATURE_KEYWORDS)) {
            return $this->adminFeatureSummary($normalized);
        }

        if ($audience === 'admin' && $this->containsAny($normalized, self::ADMIN_SUMMARY_KEYWORDS)) {
            return $this->response($this->adminSummary($normalized), [
                'quick_replies' => $this->quickReplies($audience),
            ]);
        }

        if ($this->containsAny($normalized, self::PRICE_KEYWORDS)) {
            $catalogResponse = $this->packageCatalogResponse();
            if ($catalogResponse !== null) {
                return $catalogResponse;
            }
        }

        if ($this->containsAny($normalized, self::OPENING_KEYWORDS)) {
            return $this->response('Godong Ijo buka setiap hari. The Waterfall Resto beroperasi pukul 10.00-21.00, sedangkan Fishing Lake pukul 09.00-21.00.');
        }

        if ($this->containsAny($normalized, ['booking private room', 'pesan private room', 'reservasi private room', 'private room gimana', 'private room bagaimana', 'cara booking private'])) {
            return $this->response(
                "Cara booking Private Room:\n"
                . "1. Buka halaman Paket Private Room: " . route('packages.category', 'private-room') . "\n"
                . "2. Pilih paket Wedding, Engagement, Gathering, atau Meeting sesuai kebutuhan.\n"
                . "3. Klik BOOK NOW, lalu isi nama, email, nomor WhatsApp, tanggal acara, jenis acara, durasi, dan jumlah peserta.\n"
                . "4. Periksa estimasi total dan lanjutkan ke pembayaran.\n\n"
                . "Paket Wedding dan Engagement menggunakan harga paket, sedangkan Gathering dan Meeting menggunakan harga per pax dengan minimum peserta sesuai pilihan paket.",
                ['quick_replies' => ['Lihat paket Private Room', 'Lihat harga paket', 'Cara booking']]
            );
        }

        if ($this->containsAny($normalized, self::LOCATION_KEYWORDS)) {
            return $this->response('Lokasi Godong Ijo: Jalan Cinangka Raya Km 10 No. 60, Serua, Bojongsari, Kota Depok, Jawa Barat 16517.');
        }

        if ($this->containsAny($normalized, self::BOOKING_KEYWORDS)) {
            return $this->response('Untuk booking, pilih paket di halaman utama lalu klik Pesan. Siapkan nama, nomor WhatsApp, tanggal kunjungan, dan jumlah orang.');
        }

        if ($this->containsAny($normalized, ['admin', 'dashboard', 'booking hari ini', 'pendapatan'])) {
            return $audience === 'admin'
                ? $this->response('Untuk melihat angka terbaru, gunakan kartu statistik Dashboard dan menu Laporan. Detail booking dapat dikelola dari menu Booking.')
                : $this->response('Aku bisa bantu pertanyaan seputar paket wisata dan kunjungan Godong Ijo.');
        }

        return $this->response(
            'Aku belum menemukan jawaban yang tepat. Coba tanyakan tentang paket, harga, jam buka, lokasi, atau cara booking. Untuk bantuan langsung, hubungi WhatsApp ' . config('app.whatsapp.display') . '.',
            ['quick_replies' => $this->quickReplies($audience)]
        );
    }

    private function response(string $text, array $meta = []): array
    {
        return array_merge([
            'message' => $text,
            'source' => 'faq',
        ], $meta);
    }

    private function greetingText(string $audience): string
    {
        return $audience === 'admin'
            ? 'Halo Admin. Aku bisa bantu membaca ringkasan paket dan memberi panduan singkat operasional booking.'
            : 'Halo. Aku bisa bantu cari info paket, harga, jam buka, lokasi, dan cara booking Godong Ijo.';
    }

    private function quickReplies(string $audience): array
    {
        if ($audience === 'admin') {
            return [
                'Bantuan fitur admin',
                'Cari booking',
                'Berapa booking pending?',
                'Paket hampir penuh',
                'Paket paling ramai bulan ini?',
                'Berapa notifikasi belum dibaca?',
                'Ada berapa pelanggan?',
                'Aktivitas terbaru apa saja?',
                'Ringkasan pendapatan dan booking',
                'Cara cari booking berdasarkan kode',
            ];
        }

        return ['Lihat harga paket', 'Jam buka', 'Cara booking'];
    }

    private function extractBookingCode(string $message): ?string
    {
        if (! preg_match('/\b(BK-[A-Z0-9-]+)\b/i', $message, $matches)) {
            return null;
        }

        return strtoupper($matches[1]);
    }

    private function packageCatalogResponse(): ?array
    {
        $packages = PaketWisata::query()
            ->where('is_active', true)
            ->orderBy('nama_paket')
            ->get(['nama_paket', 'harga', 'diskon_persen', 'jenis_paket']);

        if ($packages->isEmpty()) {
            return null;
        }

        $lines = $packages->map(function (PaketWisata $package) {
            $price = $package->harga > 0
                ? 'Rp ' . number_format($package->final_price, 0, ',', '.')
                : 'Hubungi kami';
            $discount = $package->diskon_persen > 0 ? ' (diskon ' . $package->diskon_persen . '%)' : '';

            return '- ' . $package->nama_paket . ': ' . $price . $discount;
        })->implode("\n");

        return $this->response("Berikut paket yang tersedia saat ini:\n{$lines}\n\nHarga dapat berubah mengikuti ketersediaan dan periode kunjungan.");
    }

    private function adminHelp(): string
    {
        return "Aku bisa membantu semua area admin:\n"
            . "- Dashboard: booking hari ini, bulan ini, pending, dan total pendapatan.\n"
            . "- Booking: cari detail dengan kode seperti BK-20260906-ABC123, cek status, dan panduan update status.\n"
            . "- Paket Wisata: daftar paket aktif, harga, diskon, dan paket paling ramai.\n"
            . "- Pelanggan: ringkasan jumlah pelanggan dan riwayat booking.\n"
            . "- Laporan: pendapatan, rata-rata transaksi, tren harian, dan status booking.\n"
            . "- Aktivitas: perubahan paket dan booking terbaru.\n"
            . "- Notifikasi: status notifikasi admin dan booking yang perlu ditindaklanjuti.\n"
            . "- Pengaturan: lokasi menu untuk konfigurasi admin.\n\n"
            . "Contoh pertanyaan: 'berapa booking pending?', 'paket paling ramai?', atau 'cek BK-20260906-ABC123'.";
    }

    private function bookingLookup(string $bookingCode): string
    {
        $booking = Pemesanan::with('paketWisata:id,nama_paket')
            ->where('kode_booking', $bookingCode)
            ->first();

        if (! $booking) {
            return "Booking {$bookingCode} tidak ditemukan di database saat ini.";
        }

        $packageName = $booking->paketWisata?->nama_paket ?? 'Paket tidak diketahui';
        $visitDate = $booking->tanggal_kunjungan?->format('d/m/Y') ?? 'Belum ditentukan';

        return "Detail booking {$booking->kode_booking}:\n"
            . '- Nama: ' . $booking->nama_lengkap . "\n"
            . '- Paket: ' . $packageName . "\n"
            . '- Status: ' . ucfirst((string) $booking->status) . "\n"
            . '- Tanggal kunjungan: ' . $visitDate . "\n"
            . '- Jumlah orang: ' . ($booking->jumlah_orang ?? 0) . "\n"
            . '- Total: Rp ' . number_format((float) ($booking->total_harga ?? 0), 0, ',', '.');
    }

    private function adminBookingSearch(string $message): string
    {
        $term = preg_replace('/\b(cari|search|temukan|cek|lihat)\s+(booking|pemesanan)\b/i', '', $message);
        $term = preg_replace('/\b(booking|pemesanan|berdasarkan|dengan|atas nama|nama|kode|email|whatsapp|nomor)\b/i', '', (string) $term);
        $term = trim(preg_replace('/\s+/', ' ', (string) $term));

        if ($term === '') {
            return 'Tulis kode booking, nama pelanggan, email, atau nomor WhatsApp. Contoh: cari booking Budi.';
        }

        $bookings = Pemesanan::with('paketWisata:id,nama_paket')
            ->where(function ($query) use ($term) {
                $like = '%' . $term . '%';
                $query->where('kode_booking', 'like', $like)
                    ->orWhere('nama_lengkap', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('no_hp', 'like', $like);
            })
            ->latest()
            ->limit(5)
            ->get();

        if ($bookings->isEmpty()) {
            return "Tidak ada booking yang cocok dengan '{$term}'. Coba gunakan kode booking, nama, email, atau nomor WhatsApp yang lebih lengkap.";
        }

        $lines = $bookings->map(function (Pemesanan $booking) {
            $package = $booking->paketWisata?->nama_paket ?? 'Paket tidak diketahui';
            $date = $booking->tanggal_kunjungan?->format('d/m/Y') ?? 'Belum ditentukan';

            return '- ' . $booking->kode_booking . ' | ' . $booking->nama_lengkap
                . "\n  {$package} · {$date} · " . ucfirst((string) $booking->status);
        })->implode("\n");

        return "Ditemukan {$bookings->count()} booking untuk '{$term}':\n{$lines}\n\nKetik kode booking untuk melihat detail lengkap.";
    }

    private function lowQuotaPackages(): string
    {
        $today = Carbon::today();
        $schedules = Jadwal::with('paket:id,nama_paket,kuota')
            ->whereDate('tanggal', '>=', $today)
            ->whereDate('tanggal', '<=', $today->copy()->addDays(30))
            ->whereHas('paket', fn ($query) => $query->where('is_active', true))
            ->orderBy('tanggal')
            ->get()
            ->filter(function (Jadwal $schedule) {
                $total = (int) ($schedule->paket?->kuota ?? 0);
                $available = (int) $schedule->kuota_tersedia;
                $threshold = max(5, (int) ceil($total * 0.2));

                return $total > 0 && $available < $total && $available <= $threshold;
            })
            ->sortBy(fn (Jadwal $schedule) => [
                (int) $schedule->kuota_tersedia,
                $schedule->tanggal?->timestamp ?? PHP_INT_MAX,
            ])
            ->take(5);

        if ($schedules->isEmpty()) {
            return 'Belum ada paket yang hampir penuh untuk 30 hari ke depan. Kuota masih dalam batas aman.';
        }

        $lines = $schedules->map(function (Jadwal $schedule) {
            $package = $schedule->paket?->nama_paket ?? 'Paket tidak diketahui';
            $available = max(0, (int) $schedule->kuota_tersedia);
            $total = (int) $schedule->paket->kuota;
            $status = $available === 0 ? 'PENUH' : "tersisa {$available}/{$total}";

            return '- ' . $package . ' · ' . $schedule->tanggal->format('d/m/Y') . " · {$status}";
        })->implode("\n");

        return "Perhatian, ada paket dengan kuota menipis dalam 30 hari ke depan:\n{$lines}";
    }

    private function adminFeatureSummary(string $question): array
    {
        if ($this->containsAny($question, ['pelanggan', 'customer'])) {
            $customers = Pemesanan::whereNotNull('email')->distinct()->count('email');

            return $this->response("Ada {$customers} pelanggan unik berdasarkan email booking. Detail pelanggan tersedia di menu Pelanggan.", [
                'quick_replies' => $this->quickReplies('admin'),
            ]);
        }

        if ($this->containsAny($question, ['paket aktif', 'paket tersedia'])) {
            $activePackages = PaketWisata::where('is_active', true)->count();

            return $this->response("Saat ini ada {$activePackages} paket aktif. Gunakan menu Paket Wisata untuk melihat, menambah, mengubah, atau menonaktifkan paket.", [
                'quick_replies' => $this->quickReplies('admin'),
            ]);
        }

        if ($this->containsAny($question, ['notifikasi', 'belum dibaca'])) {
            $unread = AdminNotification::where('is_read', false)->count();

            return $this->response("Ada {$unread} notifikasi admin yang belum dibaca. Buka ikon lonceng di topbar untuk melihat detailnya.", [
                'quick_replies' => $this->quickReplies('admin'),
            ]);
        }

        if ($this->containsAny($question, ['aktivitas terbaru'])) {
            $packages = PaketWisata::where('updated_at', '>=', now()->subDays(7))->count();
            $bookings = Pemesanan::where('created_at', '>=', now()->subDays(7))->count();

            return $this->response("Dalam 7 hari terakhir: {$bookings} booking baru dan {$packages} perubahan paket. Detail lengkap ada di menu Riwayat Aktivitas.", [
                'quick_replies' => $this->quickReplies('admin'),
            ]);
        }

        return $this->response('Gunakan menu Pengaturan untuk konfigurasi sistem admin. Untuk melihat angka operasional, tanyakan ringkasan pendapatan atau booking.', [
            'quick_replies' => $this->quickReplies('admin'),
        ]);
    }

    private function adminSummary(string $question): string
    {
        $now = now();
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();

        $periodQuery = Pemesanan::query()->whereBetween('created_at', [$monthStart, $monthEnd]);
        $totalBookings = (clone $periodQuery)->count();
        $paidBookings = (clone $periodQuery)->where('status', 'paid');
        $revenue = (clone $paidBookings)->sum('total_harga');
        $pending = (clone $periodQuery)->where('status', 'pending')->count();
        $today = Pemesanan::whereDate('created_at', $now->toDateString())->count();

        $topPackage = (clone $periodQuery)
            ->select('paket_wisata_id', DB::raw('COUNT(*) as booking_count'))
            ->with('paketWisata:id,nama_paket')
            ->groupBy('paket_wisata_id')
            ->orderByDesc('booking_count')
            ->first();

        $statusSummary = (clone $periodQuery)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->map(fn ($total, $status) => ucfirst((string) $status) . ': ' . $total)
            ->values()
            ->implode(', ');

        $topPackageText = $topPackage?->paketWisata
            ? $topPackage->paketWisata->nama_paket . ' (' . $topPackage->booking_count . ' booking)'
            : 'Belum ada booking';
        $statusText = $statusSummary !== '' ? $statusSummary : 'Belum ada data';

        return "Ringkasan admin bulan " . $now->translatedFormat('F Y') . ":\n"
            . '- Total booking: ' . $totalBookings . "\n"
            . '- Booking hari ini: ' . $today . "\n"
            . '- Pendapatan dari booking paid: Rp ' . number_format($revenue, 0, ',', '.') . "\n"
            . '- Booking pending: ' . $pending . "\n"
            . '- Paket paling ramai: ' . $topPackageText . "\n"
            . '- Status booking: ' . $statusText . "\n\n"
            . 'Data dihitung dari booking yang dibuat pada bulan berjalan.';
    }

    private function containsAny(string $message, array $terms): bool
    {
        foreach ($terms as $term) {
            if (Str::contains($message, $term)) {
                return true;
            }
        }

        return false;
    }
}
