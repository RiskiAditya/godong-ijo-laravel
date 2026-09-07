<?php

namespace App\Http\Requests;

use App\Support\FishingTypeCatalog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FishingBookingRequest extends FormRequest
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
        $rules = [
            // Common fields
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'no_hp' => [
                'required',
                'string',
                'regex:/^(08|62)[0-9]{8,13}$/',
                'min:10',
                'max:15'
            ],
            'tanggal_kunjungan' => [
                'required',
                'date',
                'after_or_equal:today',
                'before_or_equal:' . now()->addDays(365)->format('Y-m-d')
            ],
            'jam_kunjungan' => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    $hour = (int) substr($value, 0, 2);
                    if ($hour < 9 || $hour > 21) {
                        $fail('Jam kunjungan harus antara 09:00 - 21:00 WIB');
                    }
                },
            ],
            'jenis_pemancingan' => [
                'required',
                Rule::in(FishingTypeCatalog::DIRECT_TYPES)
            ],
            'jumlah_joran' => ['required', 'integer', 'min:1'],
            'perlu_sewa_alat' => ['nullable', 'boolean'],
            'setuju_aturan' => ['required', 'accepted'],
            
            // Optional fields
            'qty_komet' => ['nullable', 'integer', 'min:0'],
            'qty_umpan_jadi' => ['nullable', 'integer', 'min:0'],
            'estimasi_total' => ['nullable', 'numeric', 'min:0'],
        ];

        // Conditional rules based on jenis_pemancingan
        $jenis = $this->input('jenis_pemancingan');

        switch ($jenis) {
            case 'tarikan':
                $rules['durasi'] = ['required', Rule::in(['2', '4'])];
                $rules['tambahan_jam'] = ['nullable', 'integer', 'min:0'];
                break;

            case 'sewa_joran':
                $rules['ukuran_joran'] = ['required', Rule::in(['standar', 'besar'])];
                break;

            default:
                // For jackpot and kiloan, ukuran_joran is optional (only if perlu_sewa_alat)
                if ($this->input('perlu_sewa_alat')) {
                    $rules['ukuran_joran'] = ['nullable', Rule::in(['standar', 'besar'])];
                }
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
            'nama_lengkap.required' => 'Nama lengkap wajib diisi',
            'nama_lengkap.max' => 'Nama lengkap maksimal 255 karakter',

            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.max' => 'Email maksimal 255 karakter',

            'no_hp.required' => 'Nomor WhatsApp wajib diisi',
            'no_hp.regex' => 'Nomor WhatsApp harus dimulai dengan 08 atau 62 dan berisi 10-15 digit angka',
            'no_hp.min' => 'Nomor WhatsApp minimal 10 digit',
            'no_hp.max' => 'Nomor WhatsApp maksimal 15 digit',

            'tanggal_kunjungan.required' => 'Tanggal kunjungan wajib diisi',
            'tanggal_kunjungan.date' => 'Format tanggal tidak valid',
            'tanggal_kunjungan.after_or_equal' => 'Tanggal kunjungan harus hari ini atau setelahnya',
            'tanggal_kunjungan.before_or_equal' => 'Booking maksimal 1 tahun ke depan',

            'jam_kunjungan.required' => 'Jam kunjungan wajib diisi',
            'jam_kunjungan.date_format' => 'Format jam tidak valid',

            'jenis_pemancingan.required' => 'Jenis pemancingan wajib dipilih',
            'jenis_pemancingan.in' => 'Jenis pemancingan tidak valid',

            'jumlah_joran.required' => 'Jumlah joran wajib diisi',
            'jumlah_joran.integer' => 'Jumlah joran harus berupa angka',
            'jumlah_joran.min' => 'Jumlah joran minimal 1',

            'durasi.required' => 'Durasi wajib dipilih',
            'durasi.in' => 'Durasi tidak valid',

            'tambahan_jam.integer' => 'Tambahan jam harus berupa angka',
            'tambahan_jam.min' => 'Tambahan jam minimal 0',

            'ukuran_joran.required' => 'Ukuran joran wajib dipilih',
            'ukuran_joran.in' => 'Ukuran joran tidak valid',

            'setuju_aturan.required' => 'Anda harus menyetujui aturan pemancingan',
            'setuju_aturan.accepted' => 'Anda harus menyetujui aturan pemancingan',

            'qty_komet.integer' => 'Jumlah anak ikan komet harus berupa angka',
            'qty_komet.min' => 'Jumlah anak ikan komet minimal 0',

            'qty_umpan_jadi.integer' => 'Jumlah umpan jadi harus berupa angka',
            'qty_umpan_jadi.min' => 'Jumlah umpan jadi minimal 0',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $sanitized = [];

        // Sanitize nama_lengkap
        if ($this->has('nama_lengkap')) {
            $sanitized['nama_lengkap'] = trim(strip_tags($this->input('nama_lengkap')));
        }

        // Normalize phone number
        if ($this->has('no_hp')) {
            $phone = trim($this->input('no_hp'));
            $phone = preg_replace('/[^0-9+]/', '', $phone);
            
            // Convert to standard format (62xxx)
            if (preg_match('/^0/', $phone)) {
                $phone = '62' . substr($phone, 1);
            } elseif (preg_match('/^\+62/', $phone)) {
                $phone = substr($phone, 1);
            }
            
            $sanitized['no_hp'] = $phone;
        }

        $this->merge($sanitized);
    }
}
