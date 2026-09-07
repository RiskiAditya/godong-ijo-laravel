<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Requests\DynamicBookingRequest;
use App\Models\PaketWisata;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DynamicBookingRequestValidationTest extends TestCase
{
    use DatabaseTransactions;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test package for validation tests
        PaketWisata::create([
            'nama_paket' => 'Test Package',
            'deskripsi' => 'Test Description',
            'harga' => 100000,
            'kuota' => 50,
        ]);
    }
    /**
     * Test culinary package validation rules
     */
    public function test_accepts_human_readable_package_names()
    {
        $paket = PaketWisata::firstOrCreate(
            ['id' => 1],
            [
                'nama_paket' => 'Test Package',
                'deskripsi' => 'Test Description',
                'harga' => 100000,
                'kuota' => 50,
            ]
        );

        $validator = Validator::make([
            'nama_lengkap' => 'John Doe',
            'no_hp' => '081234567890',
            'email' => 'john@example.com',
            'tanggal_kunjungan' => now()->addDays(7)->format('Y-m-d'),
            'catatan' => 'Test notes',
            'paket_wisata_id' => $paket->id,
            'jenis_paket' => 'The Waterfall Resto',
            'package_specific_data' => [
                'number_of_people' => 5,
                'time_slot' => 'lunch',
                'dietary_requirements' => 'No peanuts',
                'table_preference' => 'near_waterfall',
            ]
        ], (new DynamicBookingRequest())->rules());

        $this->assertFalse($validator->fails(), 'Validator should accept human-readable package names.');
    }

    public function test_culinary_package_validation_rules()
    {
        // Ensure test package exists
        $paket = PaketWisata::firstOrCreate(
            ['id' => 1],
            [
                'nama_paket' => 'Test Package',
                'deskripsi' => 'Test Description',
                'harga' => 100000,
                'kuota' => 50,
            ]
        );
        
        $request = new DynamicBookingRequest();
        $request->merge([
            'jenis_paket' => 'the_waterfall_resto',
        ]);
        
        $rules = $request->rules();
        
        // Assert culinary-specific rules exist
        $this->assertArrayHasKey('package_specific_data.number_of_people', $rules);
        $this->assertArrayHasKey('package_specific_data.time_slot', $rules);
        $this->assertArrayHasKey('package_specific_data.dietary_requirements', $rules);
        $this->assertArrayHasKey('package_specific_data.table_preference', $rules);
        
        // Verify validation works with all required fields
        $validator = Validator::make([
            'nama_lengkap' => 'John Doe',
            'no_hp' => '081234567890',
            'email' => 'john@example.com',
            'tanggal_kunjungan' => now()->addDays(7)->format('Y-m-d'),
            'catatan' => 'Test notes',
            'paket_wisata_id' => $paket->id,
            'jenis_paket' => 'the_waterfall_resto',
            'package_specific_data' => [
                'number_of_people' => 5,
                'time_slot' => 'lunch',
                'dietary_requirements' => 'No peanuts',
                'table_preference' => 'near_waterfall',
            ]
        ], $rules);
        
        $this->assertFalse($validator->fails());
    }

    /**
     * Test fishing package validation rules
     */
    public function test_fishing_package_validation_rules()
    {
        // Ensure test package exists
        $paket = PaketWisata::firstOrCreate(
            ['id' => 1],
            [
                'nama_paket' => 'Test Package',
                'deskripsi' => 'Test Description',
                'harga' => 100000,
                'kuota' => 50,
            ]
        );
        
        $request = new DynamicBookingRequest();
        $request->merge([
            'jenis_paket' => 'fishing_lake',
        ]);
        
        $rules = $request->rules();
        
        // Assert fishing-specific rules exist
        $this->assertArrayHasKey('package_specific_data.fishing_type', $rules);
        $this->assertArrayHasKey('package_specific_data.number_of_rods', $rules);
        $this->assertArrayHasKey('package_specific_data.duration', $rules);
        $this->assertArrayHasKey('package_specific_data.equipment_rental', $rules);
        $this->assertArrayHasKey('package_specific_data.bait_anak_ikan', $rules);
        $this->assertArrayHasKey('package_specific_data.bait_umpan_jadi', $rules);
        $this->assertArrayHasKey('package_specific_data.terms_agreement', $rules);
        
        // Verify validation works with all required fields
        $validator = Validator::make([
            'nama_lengkap' => 'John Doe',
            'no_hp' => '081234567890',
            'email' => 'john@example.com',
            'tanggal_kunjungan' => now()->addDays(7)->format('Y-m-d'),
            'catatan' => 'Test notes',
            'paket_wisata_id' => $paket->id,
            'jenis_paket' => 'fishing_lake',
            'package_specific_data' => [
                'fishing_type' => 'mancing_tarikan',
                'number_of_rods' => 2,
                'duration' => '4 hours',
                'equipment_rental' => true,
                'bait_anak_ikan' => 10,
                'bait_umpan_jadi' => 5,
                'terms_agreement' => true,
            ]
        ], $rules);
        
        if ($validator->fails()) {
            dump($validator->errors()->all());
        }
        
        $this->assertFalse($validator->fails());
    }

    /**
     * Test event package validation rules
     */
    public function test_event_package_validation_rules()
    {
        // Ensure test package exists
        $paket = PaketWisata::firstOrCreate(
            ['id' => 1],
            [
                'nama_paket' => 'Test Package',
                'deskripsi' => 'Test Description',
                'harga' => 100000,
                'kuota' => 50,
            ]
        );
        
        $request = new DynamicBookingRequest();
        $request->merge([
            'jenis_paket' => 'private_room',
        ]);
        
        $rules = $request->rules();
        
        // Assert event-specific rules exist
        $this->assertArrayHasKey('package_specific_data.event_type', $rules);
        $this->assertArrayHasKey('package_specific_data.expected_attendees', $rules);
        $this->assertArrayHasKey('package_specific_data.event_duration', $rules);
        $this->assertArrayHasKey('package_specific_data.setup_preference', $rules);
        $this->assertArrayHasKey('package_specific_data.catering_required', $rules);
        $this->assertArrayHasKey('package_specific_data.decoration_required', $rules);
        $this->assertArrayHasKey('package_specific_data.av_equipment', $rules);
        
        // Verify validation works with all required fields
        $validator = Validator::make([
            'nama_lengkap' => 'John Doe',
            'no_hp' => '081234567890',
            'email' => 'john@example.com',
            'tanggal_kunjungan' => now()->addDays(7)->format('Y-m-d'),
            'catatan' => 'Test notes',
            'paket_wisata_id' => $paket->id,
            'jenis_paket' => 'private_room',
            'package_specific_data' => [
                'event_type' => 'wedding',
                'expected_attendees' => 50,
                'event_duration' => 'full_day',
                'setup_preference' => 'banquet',
                'catering_required' => true,
                'decoration_required' => true,
                'av_equipment' => ['projector', 'sound_system'],
            ]
        ], $rules);
        
        $this->assertFalse($validator->fails());
    }

    /**
     * Test number of people minimum validation for culinary
     */
    public function test_culinary_number_of_people_minimum()
    {
        $request = new DynamicBookingRequest();
        $request->merge([
            'jenis_paket' => 'the_waterfall_resto',
        ]);
        
        $rules = $request->rules();
        
        // Test with 0 people (should fail)
        $validator = Validator::make([
            'jenis_paket' => 'the_waterfall_resto',
            'package_specific_data' => [
                'number_of_people' => 0,
            ]
        ], $rules);
        
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('package_specific_data.number_of_people', $validator->errors()->toArray());
    }

    /**
     * Test fishing terms agreement requirement
     */
    public function test_fishing_terms_agreement_required()
    {
        $request = new DynamicBookingRequest();
        $request->merge([
            'jenis_paket' => 'fishing_lake',
        ]);
        
        $rules = $request->rules();
        
        // Test without terms agreement (should fail)
        $validator = Validator::make([
            'jenis_paket' => 'fishing_lake',
            'package_specific_data' => [
                'fishing_type' => 'mancing_tarikan',
                'number_of_rods' => 1,
                'duration' => '2 hours',
                // Missing terms_agreement
            ]
        ], $rules);
        
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('package_specific_data.terms_agreement', $validator->errors()->toArray());
    }

    /**
     * Test event expected attendees minimum
     */
    public function test_event_expected_attendees_minimum()
    {
        $request = new DynamicBookingRequest();
        $request->merge([
            'jenis_paket' => 'private_room',
        ]);
        
        $rules = $request->rules();
        
        // Test with 5 attendees (should fail - minimum is 10)
        $validator = Validator::make([
            'jenis_paket' => 'private_room',
            'package_specific_data' => [
                'expected_attendees' => 5,
            ]
        ], $rules);
        
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('package_specific_data.expected_attendees', $validator->errors()->toArray());
    }

    /**
     * Test custom duration required when event duration is custom
     */
    public function test_custom_duration_required_if()
    {
        $request = new DynamicBookingRequest();
        $request->merge([
            'jenis_paket' => 'private_room',
        ]);
        
        $rules = $request->rules();
        
        // Test with custom duration but no custom_duration value (should fail)
        $validator = Validator::make([
            'jenis_paket' => 'private_room',
            'package_specific_data' => [
                'event_type' => 'meeting',
                'expected_attendees' => 20,
                'event_duration' => 'custom',
                'setup_preference' => 'theater',
                'catering_required' => false,
                'decoration_required' => false,
                // Missing custom_duration
            ]
        ], $rules);
        
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('package_specific_data.custom_duration', $validator->errors()->toArray());
    }
}
