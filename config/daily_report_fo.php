<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Daily Report FO Configuration
    |--------------------------------------------------------------------------
    */

    /**
     * Timezone mapping untuk Indonesia
     */
    'timezones' => [
        'WIB' => 'Asia/Jakarta',   // UTC+7
        'WITA' => 'Asia/Makassar',  // UTC+8
        'WIT' => 'Asia/Jayapura',  // UTC+9
    ],

    /**
     * Shift schedules and their slots
     */
    'shifts' => [
        'pagi' => [
            'label' => 'Shift Pagi',
            'start_time' => '08:00',
            'end_time' => '16:00',
            'slots' => [
                1 => '10:00',
                2 => '12:00',
                3 => '14:00',
                4 => '16:00',
            ],
        ],
        'siang' => [
            'label' => 'Shift Siang',
            'start_time' => '14:00',
            'end_time' => '22:00',
            'slots' => [
                1 => '15:00',
                2 => '17:00',
                3 => '19:00',
                4 => '21:00',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Window Timing
    |--------------------------------------------------------------------------
    |
    | Timeline per slot (contoh slot 10:00):
    |   10:00 → 10:15  = Window FO (upload laporan)
    |   10:15 → 10:30  = Window Manager (validasi)
    |   10:30+         = Expired
    |
    */

    // Durasi window FO untuk upload laporan (menit)
    // DIUBAH dari 60 → 15 menit
    'upload_window_minutes' => 40,

    // Durasi window manager untuk validasi (menit, dihitung setelah window FO tutup)
    'validation_window_minutes' => 20,

    /*
    |--------------------------------------------------------------------------
    | Validation Status
    |--------------------------------------------------------------------------
    */
    'validation_statuses' => [
        'pending' => ['label' => 'Menunggu Validasi', 'color' => 'yellow'],
        'approved' => ['label' => 'Disetujui',         'color' => 'green'],
        'rejected' => ['label' => 'Ditolak',           'color' => 'red'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Input Types (master data)
    |--------------------------------------------------------------------------
    */
    'input_types' => [
        'checkbox' => 'Checkbox (Ya/Tidak)',
        'number' => 'Angka',
        'text' => 'Text',
        'photo' => 'Upload Foto',
        'photo_number' => 'Upload Foto + Angka',
        'time' => 'Waktu',
    ],

    /*
    |--------------------------------------------------------------------------
    | Photo Upload Limits
    |--------------------------------------------------------------------------
    */
    'photo_limits' => [
        'min_per_field' => 1,
        'max_per_field' => null,   // unlimited
        'max_file_size' => 5120,   // 5MB in KB
        'allowed_extensions' => ['jpg', 'jpeg', 'png'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Compression
    |--------------------------------------------------------------------------
    */
    'image_compression' => [
        'max_width' => 1920,
        'max_height' => 1080,
        'quality' => 80,
    ],

    /*
    |--------------------------------------------------------------------------
    | History & Other Settings
    |--------------------------------------------------------------------------
    */
    'history_days' => 30,

    'shift_selection' => [
        'storage_key' => 'daily_report_fo_shift',
        'allow_change_per_day' => true,
    ],
];
