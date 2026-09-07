<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test Fishing Booking Form</title>
    
    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    
    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50">
    
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Form Pemancingan - Testing Page</h1>
            <p class="text-gray-600 mb-6">Klik tombol di bawah untuk membuka form booking pemancingan.</p>
            
            <button @click="$dispatch('open-fishing-modal')" 
                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition">
                🎣 Buka Form Pemancingan
            </button>
        </div>

        {{-- Info Section --}}
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
            <h2 class="text-xl font-bold text-blue-900 mb-3">ℹ️ Informasi Testing</h2>
            <div class="space-y-2 text-sm text-blue-800">
                <p><strong>Endpoint:</strong> POST /api/booking/fishing</p>
                <p><strong>CSRF Token:</strong> Sudah disertakan otomatis</p>
                <p><strong>Mode Pembayaran:</strong> {{ env('PAYMENT_MODE', 'live') }}</p>
            </div>
        </div>

        {{-- Harga Reference --}}
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">📊 Daftar Harga</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-bold text-lg text-gray-800 mb-3">Jenis Pemancingan</h3>
                    <ul class="space-y-2 text-sm">
                        <li class="flex justify-between">
                            <span>Sewa Joran (standar)</span>
                            <span class="font-medium">Rp 20.000 / joran</span>
                        </li>
                        <li class="flex justify-between">
                            <span>Sewa Joran (besar)</span>
                            <span class="font-medium">Rp 50.000 / joran</span>
                        </li>
                        <li class="flex justify-between">
                            <span>Mancing Tarikan (2 jam)</span>
                            <span class="font-medium">Rp 80.000 / joran</span>
                        </li>
                        <li class="flex justify-between">
                            <span>Mancing Tarikan (4 jam)</span>
                            <span class="font-medium">Rp 110.000 / joran</span>
                        </li>
                        <li class="flex justify-between">
                            <span>Tambahan Jam Tarikan</span>
                            <span class="font-medium">Rp 40.000 / jam</span>
                        </li>
                        <li class="flex justify-between">
                            <span>Mancing Jackpot (4 jam)</span>
                            <span class="font-medium">Rp 210.000 / joran</span>
                        </li>
                        <li class="flex justify-between">
                            <span>Mancing Kiloan</span>
                            <span class="font-medium text-blue-600">Dihitung saat ditimbang</span>
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-bold text-lg text-gray-800 mb-3">Umpan</h3>
                    <ul class="space-y-2 text-sm">
                        <li class="flex justify-between">
                            <span>Anak Ikan Komet</span>
                            <span class="font-medium">Rp 11.000 / 3 ekor</span>
                        </li>
                        <li class="flex justify-between">
                            <span>Umpan Jadi Godongijo</span>
                            <span class="font-medium">Rp 11.000 / pack</span>
                        </li>
                    </ul>

                    <h3 class="font-bold text-lg text-gray-800 mb-3 mt-6">Aturan</h3>
                    <ul class="space-y-1 text-sm text-gray-600">
                        <li>✅ Jam operasional: 09:00 - 21:00 WIB</li>
                        <li>❌ Dilarang: umpan lure/kroto/cuka/daging hewan</li>
                        <li>⚖️ Mancing Kiloan: Ikan wajib ditimbang & tidak boleh dilepas</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Include Fishing Booking Modal --}}
    <div x-data 
         @open-fishing-modal.window="$refs.fishingModal.openModal()">
        <div x-ref="fishingModal">
            @include('components.fishing-booking-modal')
        </div>
    </div>

</body>
</html>
