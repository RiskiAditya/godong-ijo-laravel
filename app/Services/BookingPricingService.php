<?php

namespace App\Services;

use App\Models\PaketWisata;
use App\Support\PrivateRoomPackageCatalog;

class BookingPricingService
{
    public function getFishingPricingConfig(?PaketWisata $fishingPaket = null): array
    {
        $fishingPaket ??= PaketWisata::where('jenis_paket', 'Fishing Lake')
            ->where('is_active', true)
            ->first();

        return [
            'base_price' => (float) ($fishingPaket?->harga ?? 0),
            'komet' => 15000,
            'umpan_jadi' => 25000,
            'sewa_alat' => 50000,
            'tambahan_jam' => 20000,
        ];
    }

    public function calculateGuestTotal(PaketWisata $paket, int $jumlahOrang, array $packageSpecificData = []): float
    {
        if ($paket->jenis_paket === 'Private Room' && ! empty($packageSpecificData['private_room_option'])) {
            $privateOption = PrivateRoomPackageCatalog::option($packageSpecificData['private_room_option']);

            if ($privateOption) {
                return $privateOption['type'] === 'package'
                    ? (float) $privateOption['price']
                    : (float) $privateOption['price'] * $jumlahOrang;
            }
        }

        $bookingConfig = $paket->booking_config ?? [];

        return (($bookingConfig['price_type'] ?? 'per_person') === 'package')
            ? (float) $paket->harga
            : (float) $paket->harga * $jumlahOrang;
    }

    public function calculateFishingPrice(array $data): float
    {
        $umpanTotal = 0;
        $sewaTotal = 0;
        $mancingTotal = 0;

        $pricing = $this->getFishingPricingConfig();
        $basePrice = $pricing['base_price'];

        if (($data['qty_komet'] ?? 0) > 0) {
            $umpanTotal += (int) $data['qty_komet'] * $pricing['komet'];
        }

        if (($data['qty_umpan_jadi'] ?? 0) > 0) {
            $umpanTotal += (int) $data['qty_umpan_jadi'] * $pricing['umpan_jadi'];
        }

        if (($data['perlu_sewa_alat'] ?? false) && ! empty($data['ukuran_joran'])) {
            $sewaTotal += $pricing['sewa_alat'];
        }

        if (($data['jenis_pemancingan'] ?? null) === 'tarikan') {
            $mancingTotal = $basePrice * (int) ($data['jumlah_joran'] ?? 1);

            if (! empty($data['tambahan_jam'])) {
                $mancingTotal += (int) $data['tambahan_jam'] * $pricing['tambahan_jam'];
            }
        } elseif (($data['jenis_pemancingan'] ?? null) === 'sewa_joran') {
            $mancingTotal = $basePrice * (int) ($data['jumlah_joran'] ?? 1);
        } else {
            $mancingTotal = $basePrice * (int) ($data['jumlah_joran'] ?? 1);
        }

        return $mancingTotal + $sewaTotal + $umpanTotal;
    }
}
