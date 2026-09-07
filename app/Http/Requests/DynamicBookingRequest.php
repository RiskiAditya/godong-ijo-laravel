<?php

namespace App\Http\Requests;

use App\Support\FishingTypeCatalog;
use App\Support\PackageTypeCatalog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DynamicBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $normalizedJenisPaket = $this->normalizeJenisPaket($this->input('jenis_paket'));

        if ($normalizedJenisPaket && $this->input('jenis_paket') !== $normalizedJenisPaket) {
            $this->merge(['jenis_paket' => $normalizedJenisPaket]);
        }

        $rules = [
            // Common fields validation
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'no_hp' => [
                'required',
                'string',
                'regex:/^(08|\+62|62)[0-9]{8,13}$/',
                'min:10',
                'max:15'
            ],
            'email' => ['nullable', 'email:rfc', 'max:255'],
            'tanggal_kunjungan' => [
                'required',
                'date',
                'after_or_equal:today',
                'before_or_equal:' . now()->addDays(365)->format('Y-m-d')
            ],
            'catatan' => ['nullable', 'string', 'max:1000'],
            'paket_wisata_id' => ['required', 'exists:paket_wisata,id'],
            'jenis_paket' => [
                'required',
                Rule::in([
                    'the_waterfall_resto',
                    'fishing_lake',
                    'private_room',
                    'The Waterfall Resto',
                    'Fishing Lake',
                    'Private Room',
                ])
            ],
        ];

        // Package-specific validation based on jenis_paket
        $jenispaket = $normalizedJenisPaket ?? $this->input('jenis_paket');

        switch ($jenispaket) {
            case 'the_waterfall_resto':
                $rules = array_merge($rules, [
                    'package_specific_data.number_of_people' => ['required', 'integer', 'min:1'],
                    'package_specific_data.time_slot' => ['required', Rule::in(['lunch', 'dinner'])],
                    'package_specific_data.dietary_requirements' => ['nullable', 'string', 'max:500'],
                    'package_specific_data.table_preference' => ['nullable', Rule::in(['indoor', 'outdoor', 'near_waterfall'])],
                ]);
                break;

            case 'fishing_lake':
                $rules = array_merge($rules, [
                    'package_specific_data.fishing_type' => [
                        'required',
                        Rule::in(FishingTypeCatalog::DYNAMIC_TYPES)
                    ],
                    'package_specific_data.number_of_rods' => ['required', 'integer', 'min:1'],
                    'package_specific_data.duration' => ['required', 'string'],
                    'package_specific_data.equipment_rental' => ['nullable', 'boolean'],
                    'package_specific_data.bait_anak_ikan' => ['nullable', 'integer', 'min:0'],
                    'package_specific_data.bait_umpan_jadi' => ['nullable', 'integer', 'min:0'],
                    'package_specific_data.terms_agreement' => ['required', 'accepted'],
                ]);
                break;

            case 'private_room':
                $rules = array_merge($rules, [
                    'package_specific_data.event_type' => [
                        'required',
                        Rule::in(['gathering', 'meeting', 'wedding', 'engagement', 'other'])
                    ],
                    'package_specific_data.expected_attendees' => ['required', 'integer', 'min:10'],
                    'package_specific_data.event_duration' => ['required', Rule::in(['half_day', 'full_day', 'custom'])],
                    'package_specific_data.custom_duration' => ['required_if:package_specific_data.event_duration,custom', 'nullable', 'string', 'max:100'],
                    'package_specific_data.setup_preference' => ['required', Rule::in(['theater', 'u_shape', 'classroom', 'banquet'])],
                    'package_specific_data.catering_required' => ['required', 'boolean'],
                    'package_specific_data.decoration_required' => ['required', 'boolean'],
                    'package_specific_data.av_equipment' => ['nullable', 'array'],
                    'package_specific_data.av_equipment.*' => [Rule::in(['projector', 'sound_system', 'microphone', 'whiteboard'])],
                ]);
                break;
        }

        return $rules;
    }

    /**
     * Get custom validation messages in Indonesian.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // Common field messages
            'nama_lengkap.required' => 'Nama lengkap wajib diisi',
            'nama_lengkap.string' => 'Nama lengkap harus berupa teks',
            'nama_lengkap.max' => 'Nama lengkap maksimal 255 karakter',

            'no_hp.required' => 'Nomor telepon wajib diisi',
            'no_hp.regex' => 'Format nomor telepon tidak valid. Gunakan format 08xxx, +62xxx, atau 62xxx',
            'no_hp.min' => 'Nomor telepon minimal 10 digit',
            'no_hp.max' => 'Nomor telepon maksimal 15 digit',

            'email.email' => 'Format email tidak valid',
            'email.max' => 'Email maksimal 255 karakter',

            'tanggal_kunjungan.required' => 'Tanggal booking wajib diisi',
            'tanggal_kunjungan.date' => 'Format tanggal tidak valid',
            'tanggal_kunjungan.after_or_equal' => 'Tanggal booking harus hari ini atau setelahnya',
            'tanggal_kunjungan.before_or_equal' => 'Booking maksimal 1 tahun ke depan',

            'catatan.string' => 'Catatan harus berupa teks',
            'catatan.max' => 'Catatan maksimal 1000 karakter',

            'paket_wisata_id.required' => 'Paket wisata wajib dipilih',
            'paket_wisata_id.exists' => 'Paket wisata tidak ditemukan',

            'jenis_paket.required' => 'Jenis paket wajib dipilih',
            'jenis_paket.in' => 'Jenis paket tidak valid',

            // Culinary package messages
            'package_specific_data.number_of_people.required' => 'Jumlah orang wajib diisi',
            'package_specific_data.number_of_people.integer' => 'Jumlah orang harus berupa angka',
            'package_specific_data.number_of_people.min' => 'Jumlah orang minimal 1',

            'package_specific_data.time_slot.required' => 'Waktu kunjungan wajib dipilih',
            'package_specific_data.time_slot.in' => 'Waktu kunjungan tidak valid',

            'package_specific_data.dietary_requirements.string' => 'Kebutuhan diet harus berupa teks',
            'package_specific_data.dietary_requirements.max' => 'Kebutuhan diet maksimal 500 karakter',

            'package_specific_data.table_preference.in' => 'Preferensi meja tidak valid',

            // Fishing package messages
            'package_specific_data.fishing_type.required' => 'Jenis mancing wajib dipilih',
            'package_specific_data.fishing_type.in' => 'Jenis mancing tidak valid',

            'package_specific_data.number_of_rods.required' => 'Jumlah joran wajib diisi',
            'package_specific_data.number_of_rods.integer' => 'Jumlah joran harus berupa angka',
            'package_specific_data.number_of_rods.min' => 'Jumlah joran minimal 1',

            'package_specific_data.duration.required' => 'Durasi wajib dipilih',
            'package_specific_data.duration.string' => 'Durasi harus berupa teks',

            'package_specific_data.equipment_rental.boolean' => 'Sewa peralatan harus berupa ya/tidak',

            'package_specific_data.bait_anak_ikan.integer' => 'Jumlah anak ikan harus berupa angka',
            'package_specific_data.bait_anak_ikan.min' => 'Jumlah anak ikan minimal 0',

            'package_specific_data.bait_umpan_jadi.integer' => 'Jumlah umpan jadi harus berupa angka',
            'package_specific_data.bait_umpan_jadi.min' => 'Jumlah umpan jadi minimal 0',

            'package_specific_data.terms_agreement.required' => 'Anda harus menyetujui peraturan mancing',
            'package_specific_data.terms_agreement.accepted' => 'Anda harus menyetujui peraturan mancing',

            // Event package messages
            'package_specific_data.event_type.required' => 'Jenis acara wajib dipilih',
            'package_specific_data.event_type.in' => 'Jenis acara tidak valid',

            'package_specific_data.expected_attendees.required' => 'Jumlah peserta wajib diisi',
            'package_specific_data.expected_attendees.integer' => 'Jumlah peserta harus berupa angka',
            'package_specific_data.expected_attendees.min' => 'Jumlah peserta minimal 10',

            'package_specific_data.event_duration.required' => 'Durasi acara wajib dipilih',
            'package_specific_data.event_duration.in' => 'Durasi acara tidak valid',

            'package_specific_data.custom_duration.required_if' => 'Durasi custom wajib diisi',
            'package_specific_data.custom_duration.string' => 'Durasi custom harus berupa teks',
            'package_specific_data.custom_duration.max' => 'Durasi custom maksimal 100 karakter',

            'package_specific_data.setup_preference.required' => 'Pengaturan ruangan wajib dipilih',
            'package_specific_data.setup_preference.in' => 'Pengaturan ruangan tidak valid',

            'package_specific_data.catering_required.required' => 'Pilihan katering wajib diisi',
            'package_specific_data.catering_required.boolean' => 'Pilihan katering harus berupa ya/tidak',

            'package_specific_data.decoration_required.required' => 'Pilihan dekorasi wajib diisi',
            'package_specific_data.decoration_required.boolean' => 'Pilihan dekorasi harus berupa ya/tidak',

            'package_specific_data.av_equipment.array' => 'Peralatan AV harus berupa array',
            'package_specific_data.av_equipment.*.in' => 'Peralatan AV tidak valid',
        ];
    }

    /**
     * Normalize human-readable package names to internal canonical values.
     *
     * @param mixed $value
     * @return string|null
     */
    private function normalizeJenisPaket($value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        $trimmed = trim($value);
        if ($trimmed === '') {
            return null;
        }

        return PackageTypeCatalog::normalizeInternal($trimmed);
    }

    /**
     * Helper method to strip HTML tags and their content (for script/style tags)
     * 
     * @param string $value
     * @return string
     */
    private function stripHtmlTags(string $value): string
    {
        // Remove script and style tags along with their content
        $value = preg_replace('/<(script|style)[^>]*>.*?<\/\1>/is', '', $value);
        // Remove all remaining HTML tags
        $value = strip_tags($value);
        return $value;
    }

    /**
     * Prepare the data for validation.
     * 
     * Implements input sanitization per requirements 25.1-25.5:
     * - 25.1: Strip HTML tags from text inputs except notes
     * - 25.2: Escape special characters in notes field
     * - 25.3: Trim whitespace from all text inputs
     * - 25.4: Normalize phone number to standard Indonesian format
     * - 25.5: Convert all inputs to UTF-8 encoding
     */
    protected function prepareForValidation(): void
    {
        // Sanitize inputs before validation
        $sanitized = [];

        // 25.1, 25.3: Strip HTML tags and trim whitespace from nama_lengkap
        if ($this->has('nama_lengkap')) {
            $value = $this->input('nama_lengkap');
            // 25.5: Ensure UTF-8 encoding
            $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
            // 25.3: Trim whitespace
            $value = trim($value);
            // 25.1: Strip HTML tags and script/style content
            $value = $this->stripHtmlTags($value);
            $sanitized['nama_lengkap'] = $value;
        }

        // 25.1, 25.3: Strip HTML tags and trim whitespace from email
        if ($this->has('email')) {
            $value = $this->input('email');
            // 25.5: Ensure UTF-8 encoding
            $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
            // 25.3: Trim whitespace
            $value = trim($value);
            // 25.1: Strip HTML tags and script/style content
            $value = $this->stripHtmlTags($value);
            $sanitized['email'] = $value;
        }

        // 25.3, 25.4: Normalize phone number to standard Indonesian format
        if ($this->has('no_hp')) {
            $phone = $this->input('no_hp');
            // 25.5: Ensure UTF-8 encoding
            $phone = mb_convert_encoding($phone, 'UTF-8', 'UTF-8');
            // 25.3: Trim whitespace
            $phone = trim($phone);
            // 25.4: Normalize to standard format - remove spaces and special chars except +
            $phone = preg_replace('/[^0-9+]/', '', $phone);
            // 25.4: Standardize prefix format
            if (preg_match('/^0/', $phone)) {
                // Convert 08xxx to 628xxx
                $phone = '62' . substr($phone, 1);
            } elseif (preg_match('/^\+62/', $phone)) {
                // Convert +62xxx to 62xxx
                $phone = substr($phone, 1);
            }
            $sanitized['no_hp'] = $phone;
        }

        // 25.2, 25.3: Escape special characters in notes field
        if ($this->has('catatan')) {
            $value = $this->input('catatan');
            // 25.5: Ensure UTF-8 encoding
            $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
            // 25.3: Trim whitespace
            $value = trim($value);
            // 25.2: Escape special characters for safe storage (HTML entities)
            $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            $sanitized['catatan'] = $value;
        }

        // 25.3: Trim whitespace from tanggal_kunjungan
        if ($this->has('tanggal_kunjungan')) {
            $value = $this->input('tanggal_kunjungan');
            $sanitized['tanggal_kunjungan'] = trim($value);
        }

        // Sanitize package-specific data
        if ($this->has('package_specific_data')) {
            $packageData = $this->input('package_specific_data');
            
            // 25.1, 25.3: Sanitize dietary_requirements (culinary package)
            if (isset($packageData['dietary_requirements'])) {
                $value = $packageData['dietary_requirements'];
                // 25.5: Ensure UTF-8 encoding
                $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                // 25.3: Trim whitespace
                $value = trim($value);
                // 25.1: Strip HTML tags and script/style content
                $value = $this->stripHtmlTags($value);
                $packageData['dietary_requirements'] = $value;
            }
            
            // 25.1, 25.3: Sanitize table_preference (culinary package)
            if (isset($packageData['table_preference'])) {
                $value = $packageData['table_preference'];
                $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                $value = trim($value);
                $value = $this->stripHtmlTags($value);
                $packageData['table_preference'] = $value;
            }
            
            // 25.1, 25.3: Sanitize time_slot (culinary package)
            if (isset($packageData['time_slot'])) {
                $value = $packageData['time_slot'];
                $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                $value = trim($value);
                $value = $this->stripHtmlTags($value);
                $packageData['time_slot'] = $value;
            }
            
            // 25.1, 25.3: Sanitize fishing_type (fishing package)
            if (isset($packageData['fishing_type'])) {
                $value = $packageData['fishing_type'];
                $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                $value = trim($value);
                $value = $this->stripHtmlTags($value);
                $packageData['fishing_type'] = $value;
            }
            
            // 25.1, 25.3: Sanitize duration (fishing package)
            if (isset($packageData['duration'])) {
                $value = $packageData['duration'];
                $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                $value = trim($value);
                $value = $this->stripHtmlTags($value);
                $packageData['duration'] = $value;
            }
            
            // 25.1, 25.3: Sanitize event_type (private room package)
            if (isset($packageData['event_type'])) {
                $value = $packageData['event_type'];
                $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                $value = trim($value);
                $value = $this->stripHtmlTags($value);
                $packageData['event_type'] = $value;
            }
            
            // 25.1, 25.3: Sanitize custom_duration (private room package)
            if (isset($packageData['custom_duration'])) {
                $value = $packageData['custom_duration'];
                $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                $value = trim($value);
                $value = strip_tags($value);
                $packageData['custom_duration'] = $value;
            }
            
            // 25.1, 25.3: Sanitize event_duration (private room package)
            if (isset($packageData['event_duration'])) {
                $value = $packageData['event_duration'];
                $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                $value = trim($value);
                $value = strip_tags($value);
                $packageData['event_duration'] = $value;
            }
            
            // 25.1, 25.3: Sanitize setup_preference (private room package)
            if (isset($packageData['setup_preference'])) {
                $value = $packageData['setup_preference'];
                $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                $value = trim($value);
                $value = strip_tags($value);
                $packageData['setup_preference'] = $value;
            }
            
            // 25.1, 25.3: Sanitize av_equipment array (private room package)
            if (isset($packageData['av_equipment']) && is_array($packageData['av_equipment'])) {
                $packageData['av_equipment'] = array_map(function($item) {
                    $item = mb_convert_encoding($item, 'UTF-8', 'UTF-8');
                    $item = trim($item);
                    $item = strip_tags($item);
                    return $item;
                }, $packageData['av_equipment']);
            }

            $sanitized['package_specific_data'] = $packageData;
        }

        // Merge sanitized data back
        $this->merge($sanitized);
    }
}
