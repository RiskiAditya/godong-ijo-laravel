<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds paket_wisata_id column to pemesanan table to enable direct relationship
     * to paket_wisata, avoiding the jadwal->paket chain that breaks when jadwal_id is null.
     * 
     * Also backfills null jadwal_id values by finding or creating matching jadwal records.
     */
    public function up(): void
    {
        // Step 1: Add paket_wisata_id column
        Schema::table('pemesanan', function (Blueprint $table) {
            // Add paket_wisata_id column as unsigned big integer nullable
            $table->unsignedBigInteger('paket_wisata_id')->nullable()->after('jadwal_id');
            
            // Add foreign key constraint to paket_wisata.id with cascade delete
            $table->foreign('paket_wisata_id')
                  ->references('id')
                  ->on('paket_wisata')
                  ->onDelete('cascade');
            
            // Add index on paket_wisata_id for performance
            $table->index('paket_wisata_id');
        });
        
        // Step 2: Backfill logic for null jadwal_id records
        $backfillCount = 0;
        $createdJadwalCount = 0;
        
        DB::transaction(function () use (&$backfillCount, &$createdJadwalCount) {
            // Query all pemesanan records where jadwal_id IS NULL
            $pemesananRecords = DB::table('pemesanan')
                ->whereNull('jadwal_id')
                ->get();
            
            echo "\n=== Starting Jadwal Backfill Process ===\n";
            echo "Found " . $pemesananRecords->count() . " pemesanan records with null jadwal_id\n\n";
            
            foreach ($pemesananRecords as $pemesanan) {
                // Determine paket_wisata_id from existing data
                // Strategy: Check if this pemesanan has package_specific_data indicating fishing booking
                $packageSpecificData = json_decode($pemesanan->package_specific_data ?? '{}', true);
                $isFishingBooking = isset($packageSpecificData['jenis_pemancingan']);
                
                if ($isFishingBooking) {
                    // This is a fishing booking - find or create fishing paket
                    // First, try to find existing fishing paket
                    $fishingPaket = DB::table('paket_wisata')
                        ->where('jenis_paket', 'mancing')
                        ->first();
                    
                    if (!$fishingPaket) {
                        // Create a default fishing package if it doesn't exist
                        $fishingPaketId = DB::table('paket_wisata')->insertGetId([
                            'nama_paket' => 'Paket Pemancingan',
                            'jenis_paket' => 'mancing',
                            'deskripsi' => 'Paket pemancingan (tarikan/kiloan/sewa joran)',
                            'harga' => 0, // Fishing has dynamic pricing
                            'kuota' => 50,
                            'is_active' => true,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        echo "Created new fishing paket with ID: {$fishingPaketId}\n";
                    } else {
                        $fishingPaketId = $fishingPaket->id;
                    }
                    
                    $paketWisataId = $fishingPaketId;
                    
                    // Look for existing jadwal matching paket_id and tanggal_kunjungan
                    $jadwal = DB::table('jadwal')
                        ->where('paket_id', $paketWisataId)
                        ->where('tanggal', $pemesanan->tanggal_kunjungan)
                        ->first();
                    
                    if (!$jadwal) {
                        // Create new jadwal record
                        $paket = DB::table('paket_wisata')->find($paketWisataId);
                        $jadwalId = DB::table('jadwal')->insertGetId([
                            'paket_id' => $paketWisataId,
                            'tanggal' => $pemesanan->tanggal_kunjungan,
                            'kuota_tersedia' => $paket->kuota,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $createdJadwalCount++;
                        echo "Created jadwal ID {$jadwalId} for fishing booking {$pemesanan->kode_booking}\n";
                    } else {
                        $jadwalId = $jadwal->id;
                    }
                    
                    // Update pemesanan with jadwal_id and paket_wisata_id
                    DB::table('pemesanan')
                        ->where('id', $pemesanan->id)
                        ->update([
                            'jadwal_id' => $jadwalId,
                            'paket_wisata_id' => $paketWisataId,
                            'updated_at' => now(),
                        ]);
                    
                    $backfillCount++;
                    echo "Backfilled pemesanan ID {$pemesanan->id} ({$pemesanan->kode_booking}) - Fishing\n";
                    
                } else {
                    // This is a regular booking with missing jadwal_id
                    // This shouldn't happen in normal operation, but we need to handle it
                    // We cannot automatically determine paket_wisata_id without more information
                    echo "WARNING: Non-fishing pemesanan ID {$pemesanan->id} ({$pemesanan->kode_booking}) has null jadwal_id\n";
                    echo "  - This record cannot be automatically backfilled\n";
                    echo "  - Manual intervention may be required\n";
                }
            }
        });
        
        // Step 3: Validate data integrity after backfill
        echo "\n=== Starting Post-Backfill Validation ===\n";
        
        // Count remaining pemesanan records with null jadwal_id
        $nullJadwalCount = DB::table('pemesanan')->whereNull('jadwal_id')->count();
        
        if ($nullJadwalCount > 0) {
            // Fetch details of affected records for the exception message
            $affectedRecords = DB::table('pemesanan')
                ->whereNull('jadwal_id')
                ->select('id', 'kode_booking', 'tanggal_kunjungan', 'paket_wisata_id')
                ->get();
            
            $details = $affectedRecords->map(function ($record) {
                return "ID: {$record->id}, Booking: {$record->kode_booking}, Date: {$record->tanggal_kunjungan}, Paket: {$record->paket_wisata_id}";
            })->implode("\n  - ");
            
            throw new \Exception(
                "Migration validation failed: {$nullJadwalCount} pemesanan records still have null jadwal_id after backfill.\n" .
                "Affected records:\n  - {$details}"
            );
        }
        echo "✓ All pemesanan records have non-null jadwal_id\n";
        
        // Verify all jadwal_id values reference existing jadwal records
        $invalidJadwalRefs = DB::table('pemesanan')
            ->leftJoin('jadwal', 'pemesanan.jadwal_id', '=', 'jadwal.id')
            ->whereNotNull('pemesanan.jadwal_id')
            ->whereNull('jadwal.id')
            ->count();
        
        if ($invalidJadwalRefs > 0) {
            throw new \Exception(
                "Migration validation failed: {$invalidJadwalRefs} pemesanan records have jadwal_id referencing non-existent jadwal records"
            );
        }
        echo "✓ All jadwal_id values reference existing jadwal records\n";
        
        // Verify all created jadwal records have valid paket_id referencing paket_wisata
        // We check all jadwal records created during this migration (by timestamp)
        $invalidPaketRefs = DB::table('jadwal')
            ->leftJoin('paket_wisata', 'jadwal.paket_id', '=', 'paket_wisata.id')
            ->whereNull('paket_wisata.id')
            ->where('jadwal.created_at', '>=', DB::raw('DATE_SUB(NOW(), INTERVAL 1 MINUTE)'))
            ->count();
        
        if ($invalidPaketRefs > 0) {
            throw new \Exception(
                "Migration validation failed: {$invalidPaketRefs} newly created jadwal records have invalid paket_id references"
            );
        }
        echo "✓ All created jadwal records have valid paket_id references\n";
        
        // Count total pemesanan records processed
        $totalPemesanan = DB::table('pemesanan')->count();
        
        // Step 4: Output comprehensive summary
        echo "\n=== Migration Summary ===\n";
        echo "Total pemesanan records in database: {$totalPemesanan}\n";
        echo "Pemesanan records backfilled: {$backfillCount}\n";
        echo "Jadwal records created: {$createdJadwalCount}\n";
        echo "\n=== Validation Results ===\n";
        echo "✓ Zero pemesanan records with null jadwal_id\n";
        echo "✓ All jadwal_id references are valid\n";
        echo "✓ All paket_id references are valid\n";
        echo "\nBackfill and validation completed successfully.\n";
        
        // Log to Laravel log as well
        \Log::info('Migration add_paket_wisata_id_and_backfill_jadwal_id completed', [
            'total_pemesanan' => $totalPemesanan,
            'backfilled_count' => $backfillCount,
            'created_jadwal_count' => $createdJadwalCount,
            'validation_passed' => true,
        ]);
    }

    /**
     * Reverse the migrations.
     * 
     * Removes paket_wisata_id column and its constraints from pemesanan table.
     * Note: Cannot rollback jadwal_id backfill (data change, not schema).
     */
    public function down(): void
    {
        Schema::table('pemesanan', function (Blueprint $table) {
            // Drop foreign key constraint on paket_wisata_id
            $table->dropForeign(['paket_wisata_id']);
            
            // Drop index on paket_wisata_id
            $table->dropIndex(['paket_wisata_id']);
            
            // Drop paket_wisata_id column from pemesanan table
            $table->dropColumn('paket_wisata_id');
        });
    }
};
