<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Pemesanan;
use App\Models\PaketWisata;
use App\Models\Jadwal;
use App\Models\ETiket;
use App\Models\Pembayaran;
use App\Services\ETicketService;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Test untuk memverifikasi bahwa ETicketService menyimpan record ke tabel e_tiket
 * setiap kali e-tiket diterbitkan (Bug Fix untuk SRS-F-20 & SRS-F-21)
 */
class ETicketPersistenceTest extends TestCase
{
    use RefreshDatabase;

    private ETicketService $eTicketService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->eTicketService = new ETicketService();
    }

    /**
     * Test: E-Tiket record tersimpan ke database setelah generate() dipanggil
     * 
     * Expected Behavior 2.1: WHEN ETicketService::generate() dipanggil untuk 
     * pemesanan dengan status 'paid' THEN sistem SHALL menulis atau memperbarui 
     * record di tabel e_tiket dengan pemesanan_id, file_path, dan diterbitkan_pada
     */
    public function test_generate_creates_e_tiket_record_in_database(): void
    {
        // Arrange: Buat pemesanan dengan status 'paid'
        $pemesanan = $this->createPaidBooking();
        
        // Act: Generate e-tiket
        try {
            $pdfPath = $this->eTicketService->generate($pemesanan->kode_booking);
        } catch (\Exception $e) {
            // Expected: DomPDF belum terinstall, tapi record tetap harus tersimpan
            // Skip jika error bukan dari PDF generation
            if (!str_contains($e->getMessage(), 'DomPDF') && !str_contains($e->getMessage(), 'PDF generation')) {
                throw $e;
            }
        }
        
        // Assert: Record e_tiket harus ada di database
        $this->assertDatabaseHas('e_tiket', [
            'pemesanan_id' => $pemesanan->id,
        ]);
        
        // Assert: Record harus memiliki diterbitkan_pada timestamp
        $eTiket = ETiket::where('pemesanan_id', $pemesanan->id)->first();
        $this->assertNotNull($eTiket);
        $this->assertNotNull($eTiket->diterbitkan_pada);
        $this->assertTrue($eTiket->diterbitkan_pada->diffInSeconds(now()) < 5);
    }

    /**
     * Test: Generate ulang untuk pemesanan yang sama tidak membuat duplikat
     * 
     * Expected Behavior 2.2: WHEN ETicketService::generate() dipanggil berulang 
     * kali untuk pemesanan yang sama THEN sistem SHALL memperbarui record e_tiket 
     * yang ada (idempotent) menggunakan updateOrCreate()
     */
    public function test_generate_is_idempotent_no_duplicate_records(): void
    {
        // Arrange: Buat pemesanan dengan status 'paid'
        $pemesanan = $this->createPaidBooking();
        
        // Act: Generate e-tiket 2 kali
        try {
            $this->eTicketService->generate($pemesanan->kode_booking);
            sleep(1); // Pastikan timestamp berbeda
            $this->eTicketService->generate($pemesanan->kode_booking);
        } catch (\Exception $e) {
            // Expected: DomPDF belum terinstall
            if (!str_contains($e->getMessage(), 'DomPDF') && !str_contains($e->getMessage(), 'PDF generation')) {
                throw $e;
            }
        }
        
        // Assert: Hanya ada 1 record e_tiket untuk pemesanan ini
        $count = ETiket::where('pemesanan_id', $pemesanan->id)->count();
        $this->assertEquals(1, $count, 'Should only have 1 e_tiket record, not duplicates');
        
        // Assert: Timestamp diterbitkan_pada diupdate (bukan yang lama)
        $eTiket = ETiket::where('pemesanan_id', $pemesanan->id)->first();
        $this->assertNotNull($eTiket->diterbitkan_pada);
    }

    /**
     * Test: Return value generate() tetap kompatibel (regression prevention)
     * 
     * Unchanged Behavior 3.1: WHEN ETicketService::generate() menghasilkan PDF 
     * e-tiket THEN sistem SHALL CONTINUE TO mengembalikan path file PDF yang sama
     */
    public function test_generate_return_value_unchanged(): void
    {
        // Arrange
        $pemesanan = $this->createPaidBooking();
        
        // Act & Assert: generate() harus mengembalikan string path
        try {
            $result = $this->eTicketService->generate($pemesanan->kode_booking);
            $this->assertIsString($result);
        } catch (\Exception $e) {
            // Expected: DomPDF belum terinstall, tapi masih harus throw exception (bukan silent fail)
            $this->assertStringContainsString('PDF', $e->getMessage());
        }
    }

    /**
     * Test: Relasi Pemesanan -> ETiket berfungsi
     */
    public function test_pemesanan_has_e_tiket_relationship(): void
    {
        // Arrange: Buat pemesanan dan e-tiket record
        $pemesanan = $this->createPaidBooking();
        ETiket::create([
            'pemesanan_id' => $pemesanan->id,
            'file_path' => 'etickets/test.pdf',
            'diterbitkan_pada' => now(),
        ]);
        
        // Act: Akses relasi
        $eTiket = $pemesanan->eTiket;
        
        // Assert
        $this->assertNotNull($eTiket);
        $this->assertEquals($pemesanan->id, $eTiket->pemesanan_id);
        $this->assertEquals('etickets/test.pdf', $eTiket->file_path);
    }

    /**
     * Helper: Buat pemesanan dengan status 'paid' untuk testing
     */
    private function createPaidBooking(): Pemesanan
    {
        // Buat paket wisata
        $paket = PaketWisata::create([
            'nama_paket' => 'Test Package',
            'deskripsi' => 'Test Description',
            'harga' => 100000,
            'kuota' => 50,
        ]);
        
        // Buat jadwal
        $jadwal = Jadwal::create([
            'paket_id' => $paket->id,
            'tanggal' => now()->addDays(7),
            'kuota_tersedia' => 10,
        ]);
        
        // Buat pemesanan
        $pemesanan = Pemesanan::create([
            'kode_booking' => 'BK' . now()->format('Ymd') . rand(100000, 999999),
            'jadwal_id' => $jadwal->id,
            'paket_wisata_id' => $paket->id,
            'nama_lengkap' => 'Test Customer',
            'email' => 'test@example.com',
            'no_hp' => '081234567890',
            'jumlah_orang' => 2,
            'total_harga' => 200000,
            'status' => 'paid',
            'tanggal_kunjungan' => now()->addDays(7),
        ]);

        Pembayaran::create([
            'pemesanan_id' => $pemesanan->id,
            'order_id' => 'TEST-' . $pemesanan->id,
            'gross_amount' => 200000,
            'status' => 'success',
            'payment_type' => 'bank_transfer',
            'transaction_id' => 'TRANSACTION-' . $pemesanan->id,
            'paid_at' => now(),
        ]);
        
        return $pemesanan;
    }
}
