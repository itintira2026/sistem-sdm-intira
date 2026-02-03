<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Daily Report FO Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Daily Report FO feature including shift times,
    | slot schedules, and upload windows.
    |
    */

    /**
     * Timezone mapping untuk Indonesia
     * WIB = UTC+7, WITA = UTC+8, WIT = UTC+9
     */
    'timezones' => [
        'WIB' => 'Asia/Jakarta',      // UTC+7
        'WITA' => 'Asia/Makassar',    // UTC+8
        'WIT' => 'Asia/Jayapura',     // UTC+9
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
                1 => '10:00',  // Window: 10:00 - 11:00
                2 => '12:00',  // Window: 12:00 - 13:00
                3 => '14:00',  // Window: 14:00 - 15:00
                4 => '16:00',  // Window: 16:00 - 17:00
            ],
        ],
        'siang' => [
            'label' => 'Shift Siang',
            'start_time' => '14:00',
            'end_time' => '22:00',
            'slots' => [
                1 => '15:00',  // Window: 15:00 - 16:00
                2 => '17:00',  // Window: 17:00 - 18:00
                3 => '19:00',  // Window: 19:00 - 20:00
                4 => '21:00',  // Window: 21:00 - 22:00
            ],
        ],
    ],

    /**
     * Upload window duration (in minutes)
     * Default: 60 minutes (1 hour after slot time)
     */
    'upload_window_minutes' => 60,

    /**
     * Photo categories yang wajib diisi
     */
    'photo_categories' => [
        'like_fb' => 'Like Facebook',
        'comment_fb' => 'Comment Facebook',
        'like_ig' => 'Like Instagram',
        'comment_ig' => 'Comment Instagram',
        'like_tiktok' => 'Like TikTok',
        'comment_tiktok' => 'Comment TikTok',
    ],

    /**
     * Photo upload limits
     */
    'photo_limits' => [
        'min_per_category' => 1,  // Minimal 1 foto per kategori
        'max_per_category' => null, // Unlimited
        'max_file_size' => 5120, // 5MB in KB
        'allowed_extensions' => ['jpg', 'jpeg', 'png'],
    ],

    /**
     * Image compression settings
     */
    'image_compression' => [
        'max_width' => 1920,
        'max_height' => 1080,
        'quality' => 80,
    ],

    /**
     * History view settings
     */
    'history_days' => 30, // Berapa hari history yang bisa dilihat FO

    /**
     * Shift selection settings
     */
    'shift_selection' => [
        'storage_key' => 'daily_report_fo_shift', // Session key
        'allow_change_per_day' => true, // Bisa beda shift tiap hari
    ],
];
