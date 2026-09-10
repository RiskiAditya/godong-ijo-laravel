<?php

namespace App\Http\Requests;

use App\Support\FishingTypeCatalog;
use App\Support\PackageTypeCatalog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DynamicBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

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

        $jenisPaket = $normalizedJenisPaket ?? $this->input('jenis_paket');

        return array_merge($rules, $this->packageSpecificRules($jenisPaket));
    }

    private function packageSpecificRules(?string $jenisPaket): array
    {
        switch ($jenisPaket) {
            case 'the_waterfall_resto':
                return [
                    'package_specific_data.number_of_people' => ['required', 'integer', 'min:1'],
                    'package_specific_data.time_slot' => ['required', Rule::in(['lunch', 'dinner'])],
                    'package_specific_data.dietary_requirements' => ['nullable', 'string', 'max:500'],
                    'package_specific_data.table_preference' => ['nullable', Rule::in(['indoor', 'outdoor', 'near_waterfall'])],
                ];

            case 'fishing_lake':
                return [
                    'package_specific_data.fishing_type' => ['required', Rule::in(FishingTypeCatalog::DYNAMIC_TYPES)],
                    'package_specific_data.number_of_rods' => ['required', 'integer', 'min:1'],
                    'package_specific_data.duration' => ['required', 'string'],
                    'package_specific_data.equipment_rental' => ['nullable', 'boolean'],
                    'package_specific_data.bait_anak_ikan' => ['nullable', 'integer', 'min:0'],
                    'package_specific_data.bait_umpan_jadi' => ['nullable', 'integer', 'min:0'],
                    'package_specific_data.terms_agreement' => ['required', 'accepted'],
                ];

            case 'private_room':
                return [
                    'package_specific_data.event_type' => ['required', Rule::in(['gathering', 'meeting', 'wedding', 'engagement', 'other'])],
                    'package_specific_data.expected_attendees' => ['required', 'integer', 'min:10'],
                    'package_specific_data.event_duration' => ['required', Rule::in(['half_day', 'full_day', 'custom'])],
                    'package_specific_data.custom_duration' => ['required_if:package_specific_data.event_duration,custom', 'nullable', 'string', 'max:100'],
                    'package_specific_data.setup_preference' => ['required', Rule::in(['theater', 'u_shape', 'classroom', 'banquet'])],
                    'package_specific_data.catering_required' => ['required', 'boolean'],
                    'package_specific_data.decoration_required' => ['required', 'boolean'],
                    'package_specific_data.av_equipment' => ['nullable', 'array'],
                    'package_specific_data.av_equipment.*' => [Rule::in(['projector', 'sound_system', 'microphone', 'whiteboard'])],
                ];

            default:
                return [];
        }
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

    private function stripHtmlTags(string $value): string
    {
        if (preg_match('/^\s*<(script|style)\b[^>]*>.*?<\/\1>\s*$/is', $value)) {
            $value = preg_replace('/^\s*<(script|style)\b[^>]*>|<\/\1>\s*$/is', '', $value);
        } else {
            $value = preg_replace('/<(script|style)\b[^>]*>.*?<\/\1>/is', '', $value);
        }

        return strip_tags($value);
    }

    protected function prepareForValidation(): void
    {
        $sanitized = [];

        foreach (['nama_lengkap', 'email'] as $field) {
            if ($this->has($field)) {
                $sanitized[$field] = $this->sanitizeText($this->input($field));
            }
        }

        if ($this->has('no_hp')) {
            $sanitized['no_hp'] = $this->normalizePhoneNumber($this->input('no_hp'));
        }

        if ($this->has('catatan')) {
            $sanitized['catatan'] = htmlspecialchars(trim((string) $this->input('catatan')), ENT_QUOTES, 'UTF-8');
        }

        if ($this->has('tanggal_kunjungan')) {
            $sanitized['tanggal_kunjungan'] = trim((string) $this->input('tanggal_kunjungan'));
        }

        if ($this->has('package_specific_data')) {
            $sanitized['package_specific_data'] = $this->sanitizePackageSpecificData($this->input('package_specific_data'));
        }

        if ($sanitized !== []) {
            $this->merge($sanitized);
        }
    }

    private function sanitizeText(mixed $value): string
    {
        $value = mb_convert_encoding((string) $value, 'UTF-8', 'UTF-8');

        return $this->stripHtmlTags(trim($value));
    }

    private function normalizePhoneNumber(mixed $value): string
    {
        $phone = mb_convert_encoding((string) $value, 'UTF-8', 'UTF-8');
        $phone = trim($phone);
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        if (preg_match('/^0/', $phone)) {
            return '62' . substr($phone, 1);
        }

        if (preg_match('/^\+62/', $phone)) {
            return substr($phone, 1);
        }

        return $phone;
    }

    private function sanitizePackageSpecificData(array $packageData): array
    {
        $fieldsToSanitize = [
            'dietary_requirements',
            'table_preference',
            'time_slot',
            'fishing_type',
            'duration',
            'event_type',
            'custom_duration',
            'event_duration',
            'setup_preference',
        ];

        foreach ($fieldsToSanitize as $field) {
            if (isset($packageData[$field])) {
                $packageData[$field] = $this->sanitizeText($packageData[$field]);
            }
        }

        if (isset($packageData['av_equipment']) && is_array($packageData['av_equipment'])) {
            $packageData['av_equipment'] = array_map(fn ($item) => $this->sanitizeText($item), $packageData['av_equipment']);
        }

        return $packageData;
    }
}
