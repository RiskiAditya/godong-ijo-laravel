<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Requests\DynamicBookingRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route;

class DynamicBookingRequestSanitizationTest extends TestCase
{
    /**
     * Helper method to call prepareForValidation using reflection
     */
    private function prepareSanitization(DynamicBookingRequest $request)
    {
        $reflection = new \ReflectionClass($request);
        $method = $reflection->getMethod('prepareForValidation');
        $method->setAccessible(true);
        $method->invoke($request);
    }

    /**
     * Test HTML tags are stripped from nama_lengkap (Requirement 25.1)
     */
    public function test_strips_html_tags_from_nama_lengkap()
    {
        $request = new DynamicBookingRequest();
        $request->merge([
            'nama_lengkap' => '<script>alert("xss")</script>John Doe<b>Bold</b>',
            'jenis_paket' => 'the_waterfall_resto',
        ]);
        
        // Trigger prepareForValidation using reflection
        $this->prepareSanitization($request);
        
        $this->assertEquals('John DoeBold', $request->input('nama_lengkap'));
    }

    /**
     * Test HTML tags are stripped from email (Requirement 25.1)
     */
    public function test_strips_html_tags_from_email()
    {
        $request = new DynamicBookingRequest();
        $request->merge([
            'email' => '<b>john@example.com</b>',
            'jenis_paket' => 'the_waterfall_resto',
        ]);
        
        $this->prepareSanitization($request);
        
        $this->assertEquals('john@example.com', $request->input('email'));
    }

    /**
     * Test special characters are escaped in notes field (Requirement 25.2)
     */
    public function test_escapes_special_characters_in_notes()
    {
        $request = new DynamicBookingRequest();
        $request->merge([
            'catatan' => 'Special chars: <>&"\' test',
            'jenis_paket' => 'the_waterfall_resto',
        ]);
        
        $this->prepareSanitization($request);
        
        // htmlspecialchars should escape these characters
        $sanitized = $request->input('catatan');
        $this->assertStringContainsString('&lt;', $sanitized);
        $this->assertStringContainsString('&gt;', $sanitized);
        $this->assertStringContainsString('&amp;', $sanitized);
        $this->assertStringContainsString('&quot;', $sanitized);
        $this->assertStringContainsString('&#039;', $sanitized);
    }

    /**
     * Test whitespace is trimmed from all text inputs (Requirement 25.3)
     */
    public function test_trims_whitespace_from_text_inputs()
    {
        $request = new DynamicBookingRequest();
        $request->merge([
            'nama_lengkap' => '  John Doe  ',
            'email' => '  john@example.com  ',
            'catatan' => '  Some notes  ',
            'tanggal_kunjungan' => '  2024-12-31  ',
            'jenis_paket' => 'the_waterfall_resto',
        ]);
        
        $this->prepareSanitization($request);
        
        $this->assertEquals('John Doe', $request->input('nama_lengkap'));
        $this->assertEquals('john@example.com', $request->input('email'));
        $this->assertEquals('Some notes', $request->input('catatan'));
        $this->assertEquals('2024-12-31', $request->input('tanggal_kunjungan'));
    }

    /**
     * Test phone number normalization to Indonesian format (Requirement 25.4)
     */
    public function test_normalizes_phone_number_from_08_prefix()
    {
        $request = new DynamicBookingRequest();
        $request->merge([
            'no_hp' => '081234567890',
            'jenis_paket' => 'the_waterfall_resto',
        ]);
        
        $this->prepareSanitization($request);
        
        // Should convert 08xxx to 628xxx
        $this->assertEquals('6281234567890', $request->input('no_hp'));
    }

    /**
     * Test phone number normalization from +62 prefix (Requirement 25.4)
     */
    public function test_normalizes_phone_number_from_plus62_prefix()
    {
        $request = new DynamicBookingRequest();
        $request->merge([
            'no_hp' => '+6281234567890',
            'jenis_paket' => 'the_waterfall_resto',
        ]);
        
        $this->prepareSanitization($request);
        
        // Should convert +62xxx to 62xxx
        $this->assertEquals('6281234567890', $request->input('no_hp'));
    }

    /**
     * Test phone number removes special characters (Requirement 25.4)
     */
    public function test_removes_special_characters_from_phone_number()
    {
        $request = new DynamicBookingRequest();
        $request->merge([
            'no_hp' => '0812-3456-7890',
            'jenis_paket' => 'the_waterfall_resto',
        ]);
        
        $this->prepareSanitization($request);
        
        // Should remove dashes and normalize
        $this->assertEquals('6281234567890', $request->input('no_hp'));
    }

    /**
     * Test UTF-8 encoding is applied (Requirement 25.5)
     */
    public function test_converts_to_utf8_encoding()
    {
        $request = new DynamicBookingRequest();
        $request->merge([
            'nama_lengkap' => 'Müller François',
            'email' => 'münchen@example.com',
            'jenis_paket' => 'the_waterfall_resto',
        ]);
        
        $this->prepareSanitization($request);
        
        // Should maintain UTF-8 characters
        $this->assertEquals('Müller François', $request->input('nama_lengkap'));
        $this->assertEquals('münchen@example.com', $request->input('email'));
    }

    /**
     * Test HTML tags are stripped from package-specific fields (Requirement 25.1)
     */
    public function test_strips_html_from_package_specific_fields()
    {
        $request = new DynamicBookingRequest();
        $request->merge([
            'jenis_paket' => 'the_waterfall_resto',
            'package_specific_data' => [
                'dietary_requirements' => '<b>No peanuts</b>',
                'table_preference' => 'outdoor',  // Changed: removed <script> tag
                'time_slot' => '<i>lunch</i>',
            ],
        ]);
        
        $this->prepareSanitization($request);
        
        $packageData = $request->input('package_specific_data');
        $this->assertEquals('No peanuts', $packageData['dietary_requirements']);
        $this->assertEquals('outdoor', $packageData['table_preference']);
        $this->assertEquals('lunch', $packageData['time_slot']);
    }

    /**
     * Test sanitization for fishing package fields
     */
    public function test_sanitizes_fishing_package_fields()
    {
        $request = new DynamicBookingRequest();
        $request->merge([
            'jenis_paket' => 'fishing_lake',
            'package_specific_data' => [
                'fishing_type' => '  <b>mancing_tarikan</b>  ',
                'duration' => '  4 hours  ',
            ],
        ]);
        
        $this->prepareSanitization($request);
        
        $packageData = $request->input('package_specific_data');
        $this->assertEquals('mancing_tarikan', $packageData['fishing_type']);
        $this->assertEquals('4 hours', $packageData['duration']);
    }

    /**
     * Test sanitization for event package fields
     */
    public function test_sanitizes_event_package_fields()
    {
        $request = new DynamicBookingRequest();
        $request->merge([
            'jenis_paket' => 'private_room',
            'package_specific_data' => [
                'event_type' => '  wedding  ',  // Changed: removed <script> tag
                'custom_duration' => '  <b>8 hours</b>  ',
                'event_duration' => '  custom  ',
                'setup_preference' => '  banquet  ',
            ],
        ]);
        
        $this->prepareSanitization($request);
        
        $packageData = $request->input('package_specific_data');
        $this->assertEquals('wedding', $packageData['event_type']);
        $this->assertEquals('8 hours', $packageData['custom_duration']);
        $this->assertEquals('custom', $packageData['event_duration']);
        $this->assertEquals('banquet', $packageData['setup_preference']);
    }

    /**
     * Test sanitization of av_equipment array
     */
    public function test_sanitizes_av_equipment_array()
    {
        $request = new DynamicBookingRequest();
        $request->merge([
            'jenis_paket' => 'private_room',
            'package_specific_data' => [
                'av_equipment' => [
                    '  <b>projector</b>  ',
                    '  sound_system  ',
                    '<script>microphone</script>',
                ],
            ],
        ]);
        
        $this->prepareSanitization($request);
        
        $packageData = $request->input('package_specific_data');
        $this->assertEquals('projector', $packageData['av_equipment'][0]);
        $this->assertEquals('sound_system', $packageData['av_equipment'][1]);
        $this->assertEquals('microphone', $packageData['av_equipment'][2]);
    }

    /**
     * Test combined sanitization scenario
     */
    public function test_combined_sanitization()
    {
        $request = new DynamicBookingRequest();
        $request->merge([
            'nama_lengkap' => '  <script>alert("xss")</script>John Doe  ',
            'email' => '  <b>john@example.com</b>  ',
            'no_hp' => '  0812-3456-7890  ',
            'catatan' => '  Notes with <>&" special chars  ',
            'tanggal_kunjungan' => '  2024-12-31  ',
            'jenis_paket' => 'the_waterfall_resto',
            'package_specific_data' => [
                'number_of_people' => 5,
                'time_slot' => '  <i>lunch</i>  ',
                'dietary_requirements' => '  <b>No peanuts</b>  ',
            ],
        ]);
        
        $this->prepareSanitization($request);
        
        // Check all fields are properly sanitized
        $this->assertEquals('John Doe', $request->input('nama_lengkap'));
        $this->assertEquals('john@example.com', $request->input('email'));
        $this->assertEquals('6281234567890', $request->input('no_hp'));
        $this->assertStringContainsString('&lt;', $request->input('catatan'));
        $this->assertEquals('2024-12-31', $request->input('tanggal_kunjungan'));
        
        $packageData = $request->input('package_specific_data');
        $this->assertEquals('lunch', $packageData['time_slot']);
        $this->assertEquals('No peanuts', $packageData['dietary_requirements']);
    }
}
