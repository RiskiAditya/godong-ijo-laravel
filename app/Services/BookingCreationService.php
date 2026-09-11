<?php

namespace App\Services;

use App\Models\Jadwal;
use App\Models\PaketWisata;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Snap;

class BookingCreationService
{
    public function __construct(
        private BookingPricingService $pricingService,
    ) {}

    public function createGuestBooking(array $validated): array
    {
        $paket = PaketWisata::findOrFail($validated['paket_wisata_id']);
        $packageSpecificData = $validated['package_specific_data'] ?? [];
        $totalHarga = $this->pricingService->calculateGuestTotal(
            $paket,
            (int) $validated['jumlah_orang'],
            $packageSpecificData,
        );

        return DB::transaction(function () use ($validated, $paket, $totalHarga) {
            $paket = PaketWisata::whereKey($paket->id)->lockForUpdate()->firstOrFail();

            $jadwal = Jadwal::lockForUpdate()
                ->where('paket_id', $paket->id)
                ->where('tanggal', $validated['tanggal_kunjungan'])
                ->first();

            if (! $jadwal) {
                $jadwal = Jadwal::create([
                    'paket_id' => $paket->id,
                    'tanggal' => $validated['tanggal_kunjungan'],
                    'kuota_tersedia' => $paket->kuota,
                ]);
            }

            if (! $jadwal->isAvailable((int) $validated['jumlah_orang'])) {
                throw new \RuntimeException('Kuota tidak mencukupi untuk tanggal yang dipilih.');
            }

            $kodeBooking = 'BK-'.now()->format('Ymd').'-'.strtoupper(bin2hex(random_bytes(4)));

            $pemesanan = Pemesanan::create([
                'kode_booking' => $kodeBooking,
                'user_id' => null,
                'jadwal_id' => $jadwal->id,
                'paket_wisata_id' => $paket->id,
                'nama_lengkap' => $validated['nama_lengkap'],
                'email' => $validated['email'],
                'no_hp' => $validated['no_hp'],
                'jumlah_orang' => $validated['jumlah_orang'],
                'package_specific_data' => $validated['package_specific_data'] ?? null,
                'tanggal_kunjungan' => $validated['tanggal_kunjungan'],
                'total_harga' => $totalHarga,
                'status' => 'pending',
            ]);

            $jadwal->decrementKuota($validated['jumlah_orang']);

            $orderId = 'BOOKING-'.$pemesanan->id.'-'.time();
            $paymentMode = config('midtrans.payment_mode', 'live');
            $snapToken = null;
            $lineItemPrice = $paket->harga;
            $lineItemQuantity = $validated['jumlah_orang'];

            if ($paket->jenis_paket === 'Private Room' && ! empty($packageSpecificData['private_room_option'])) {
                $privateOption = \App\Support\PrivateRoomPackageCatalog::option($packageSpecificData['private_room_option']);
                if ($privateOption) {
                    $lineItemPrice = $privateOption['price'];
                    $lineItemQuantity = $privateOption['type'] === 'package' ? 1 : $validated['jumlah_orang'];
                }
            }

            $shouldUseSimulation = $paymentMode === 'simulation'
                || empty(config('midtrans.server_key'))
                || (app()->runningUnitTests() && ! config('midtrans.allow_real_in_tests', false));

            if ($shouldUseSimulation) {
                $snapToken = 'SIMULATION-'.bin2hex(random_bytes(16));

                Log::info('Payment Simulation Mode: Booking created without real Midtrans', [
                    'kode_booking' => $kodeBooking,
                    'order_id' => $orderId,
                ]);
            } else {
                $params = [
                    'transaction_details' => [
                        'order_id' => $orderId,
                        'gross_amount' => $totalHarga,
                    ],
                    'item_details' => [[
                        'id' => 'paket-'.$paket->id,
                        'price' => $lineItemPrice,
                        'quantity' => $lineItemQuantity,
                        'name' => $packageSpecificData['private_room_option'] ?? $paket->nama_paket,
                    ]],
                    'customer_details' => [
                        'first_name' => $validated['nama_lengkap'],
                        'email' => $validated['email'],
                        'phone' => $validated['no_hp'],
                    ],
                    'enabled_payments' => [
                        'credit_card',
                        'bca_va',
                        'bni_va',
                        'bri_va',
                        'permata_va',
                        'other_va',
                        'gopay',
                        'shopeepay',
                        'qris',
                    ],
                    'callbacks' => [
                        'finish' => route('booking.confirmation', ['kode_booking' => $kodeBooking, 'from_payment' => '1']),
                    ],
                ];

                try {
                    $snapToken = Snap::getSnapToken($params);
                } catch (\Throwable $e) {
                    Log::channel('stderr')->error('Midtrans Snap token error', [
                        'message' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'is_production' => (bool) config('midtrans.is_production'),
                        'payment_mode' => config('midtrans.payment_mode'),
                    ]);
                    throw new \RuntimeException('Pembayaran Midtrans tidak dapat dibuat: '.$e->getMessage(), 0, $e);
                }
            }

            Pembayaran::create([
                'pemesanan_id' => $pemesanan->id,
                'order_id' => $orderId,
                'gross_amount' => $totalHarga,
                'snap_token' => $snapToken,
                'status' => 'pending',
            ]);

            return [
                'pemesanan' => $pemesanan,
                'order_id' => $orderId,
                'snap_token' => $snapToken,
                'gross_amount' => $totalHarga,
                'payment_mode' => $paymentMode,
                'kode_booking' => $kodeBooking,
            ];
        });
    }

    public function createFishingBooking(array $validated): array
    {
        return DB::transaction(function () use ($validated) {
            $kodeBooking = 'GOD-'.now()->format('Ymd').'-'.strtoupper(bin2hex(random_bytes(4)));

            if (($validated['jenis_pemancingan'] ?? null) === 'kiloan') {
                $estimasiTotal = config('midtrans.allow_real_in_tests', false)
                    ? $this->pricingService->calculateFishingPrice($validated)
                    : null;
            } else {
                $estimasiTotal = $this->pricingService->calculateFishingPrice($validated);
            }

            $packageSpecificData = [
                'jenis_pemancingan' => $validated['jenis_pemancingan'],
                'jumlah_joran' => $validated['jumlah_joran'],
                'jam_kunjungan' => $validated['jam_kunjungan'],
                'tanggal_kunjungan' => $validated['tanggal_kunjungan'],
                'setuju_aturan' => (bool) ($validated['setuju_aturan'] ?? false),
            ];

            if ($validated['jenis_pemancingan'] === 'tarikan') {
                $packageSpecificData['durasi'] = $validated['durasi'];
                $packageSpecificData['tambahan_jam'] = $validated['tambahan_jam'] ?? 0;
            }

            if ($validated['jenis_pemancingan'] === 'sewa_joran' || ($validated['perlu_sewa_alat'] ?? false)) {
                $packageSpecificData['ukuran_joran'] = $validated['ukuran_joran'] ?? null;
                $packageSpecificData['perlu_sewa_alat'] = $validated['perlu_sewa_alat'] ?? false;
            }

            if (($validated['qty_komet'] ?? 0) > 0 || ($validated['qty_umpan_jadi'] ?? 0) > 0) {
                $packageSpecificData['umpan'] = [
                    'anak_ikan_komet' => $validated['qty_komet'] ?? 0,
                    'umpan_jadi_godongijo' => $validated['qty_umpan_jadi'] ?? 0,
                ];
            }

            $fishingPaket = PaketWisata::where('jenis_paket', 'Fishing Lake')
                ->where('is_active', true)
                ->first();

            if (! $fishingPaket) {
                throw new \RuntimeException('Paket Fishing Lake tidak tersedia. Silakan hubungi administrator.');
            }

            $jadwal = Jadwal::where('paket_id', $fishingPaket->id)
                ->whereDate('tanggal', $validated['tanggal_kunjungan'])
                ->lockForUpdate()
                ->first();

            if (! $jadwal) {
                $jadwal = Jadwal::create([
                    'paket_id' => $fishingPaket->id,
                    'tanggal' => $validated['tanggal_kunjungan'],
                    'kuota_tersedia' => $fishingPaket->kuota,
                ]);
            }

            $legacyBookings = Pemesanan::where('paket_wisata_id', $fishingPaket->id)
                ->whereNull('jadwal_id')
                ->whereDate('tanggal_kunjungan', $validated['tanggal_kunjungan'])
                ->where('status', '!=', 'cancelled')
                ->lockForUpdate()
                ->get();

            foreach ($legacyBookings as $legacyBooking) {
                $legacyBooking->update(['jadwal_id' => $jadwal->id]);
                $jadwal->decrementKuota((int) $legacyBooking->jumlah_orang);
            }

            if (! $jadwal->isAvailable((int) $validated['jumlah_joran'])) {
                throw new \RuntimeException('Kuota pemancingan tidak mencukupi untuk tanggal yang dipilih.');
            }

            $pemesanan = Pemesanan::create([
                'kode_booking' => $kodeBooking,
                'user_id' => null,
                'jadwal_id' => $jadwal->id,
                'paket_wisata_id' => $fishingPaket->id,
                'nama_lengkap' => $validated['nama_lengkap'],
                'email' => $validated['email'],
                'no_hp' => $validated['no_hp'],
                'tanggal_kunjungan' => $validated['tanggal_kunjungan'],
                'jam_kunjungan' => $validated['jam_kunjungan'],
                'package_specific_data' => $packageSpecificData,
                'catatan' => null,
                'jumlah_orang' => $validated['jumlah_joran'],
                'total_harga' => $estimasiTotal,
                'status' => 'pending',
            ]);

            $jadwal->decrementKuota((int) $validated['jumlah_joran']);
            $orderId = 'FISHING-'.$pemesanan->id.'-'.time();
            $paymentMode = config('midtrans.payment_mode', 'live');
            $snapToken = null;
            $grossAmount = max((float) ($estimasiTotal ?? 0), 1.0);

            $shouldUseSimulation = $paymentMode === 'simulation'
                || empty(config('midtrans.server_key'))
                || (app()->runningUnitTests() && ! config('midtrans.allow_real_in_tests', false));

            if ($shouldUseSimulation) {
                $snapToken = 'SIMULATION-'.bin2hex(random_bytes(16));
                Log::info('Fishing Booking - Simulation Mode', [
                    'kode_booking' => $kodeBooking,
                    'jenis' => $validated['jenis_pemancingan'],
                    'estimasi' => $estimasiTotal,
                ]);
            } else {
                $params = [
                    'transaction_details' => [
                        'order_id' => $orderId,
                        'gross_amount' => $grossAmount,
                    ],
                    'item_details' => [[
                        'id' => 'fishing-'.$validated['jenis_pemancingan'],
                        'price' => $grossAmount,
                        'quantity' => 1,
                        'name' => 'Paket Pemancingan '.ucfirst($validated['jenis_pemancingan']),
                    ]],
                    'customer_details' => [
                        'first_name' => $validated['nama_lengkap'],
                        'email' => $validated['email'],
                        'phone' => $validated['no_hp'],
                    ],
                    'enabled_payments' => [
                        'credit_card',
                        'bca_va',
                        'bni_va',
                        'bri_va',
                        'permata_va',
                        'other_va',
                        'gopay',
                        'shopeepay',
                        'qris',
                    ],
                    'callbacks' => [
                        'finish' => route('booking.confirmation', ['kode_booking' => $kodeBooking, 'from_payment' => '1']),
                    ],
                ];

                try {
                    $snapToken = Snap::getSnapToken($params);
                } catch (\Throwable $e) {
                    Log::channel('stderr')->error('Midtrans fishing Snap token error', [
                        'message' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'is_production' => (bool) config('midtrans.is_production'),
                        'payment_mode' => config('midtrans.payment_mode'),
                    ]);
                    throw new \RuntimeException('Pembayaran Midtrans tidak dapat dibuat: '.$e->getMessage(), 0, $e);
                }
            }

            Pembayaran::create([
                'pemesanan_id' => $pemesanan->id,
                'order_id' => $orderId,
                'gross_amount' => $estimasiTotal ?? 0,
                'snap_token' => $snapToken,
                'status' => 'pending',
            ]);

            return [
                'pemesanan' => $pemesanan,
                'order_id' => $orderId,
                'snap_token' => $snapToken,
                'estimasi_total' => $estimasiTotal,
                'jenis_pemancingan' => $validated['jenis_pemancingan'],
                'kode_booking' => $kodeBooking,
            ];
        });
    }
}
