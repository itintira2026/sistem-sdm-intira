<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReportMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Gunakan DELETE (bukan TRUNCATE) agar tidak error foreign key constraint
        DB::table('report_fields')->delete();
        DB::table('report_categories')->delete();

        // =============================================
        // CATEGORIES
        // =============================================
        $categories = [
            [
                'id' => 1,
                'name' => 'Metrik Bisnis',
                'code' => 'metrik_bisnis',
                'description' => 'Data omset, revenue, dan jumlah akad harian (wajib diisi)',
                'order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Operasional',
                'code' => 'operasional',
                'description' => 'Checklist kegiatan operasional harian kantor',
                'order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Keuangan',
                'code' => 'keuangan',
                'description' => 'Checklist kegiatan keuangan harian',
                'order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'SDM',
                'code' => 'sdm',
                'description' => 'Checklist absensi SDM',
                'order' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'name' => 'Marketing',
                'code' => 'marketing',
                'description' => 'Laporan aktivitas marketing media sosial (angka + foto bukti)',
                'order' => 5,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('report_categories')->insert($categories);

        // =============================================
        // FIELDS — METRIK BISNIS (category_id: 1)
        // number, wajib diisi (boleh isi 0)
        // =============================================
        $metrikBisnisFields = [
            [
                'name' => 'Omset',
                'code' => 'mb_omset',
                'input_type' => 'number',
                'is_required' => true,
                'placeholder' => 'Masukkan nominal omset (0 jika tidak ada)',
                'help_text' => 'Total omset dalam rupiah. Isi 0 jika tidak ada.',
            ],
            [
                'name' => 'Revenue',
                'code' => 'mb_revenue',
                'input_type' => 'number',
                'is_required' => true,
                'placeholder' => 'Masukkan nominal revenue (0 jika tidak ada)',
                'help_text' => 'Total revenue dalam rupiah. Isi 0 jika tidak ada.',
            ],
            [
                'name' => 'Jumlah Akad',
                'code' => 'mb_jumlah_akad',
                'input_type' => 'number',
                'is_required' => true,
                'placeholder' => 'Masukkan jumlah akad (0 jika tidak ada)',
                'help_text' => 'Total jumlah akad hari ini. Isi 0 jika tidak ada.',
            ],
        ];

        // =============================================
        // FIELDS — OPERASIONAL (category_id: 2)
        // =============================================
        $operasionalFields = [
            ['name' => 'Bersih-Bersih Kantor',               'code' => 'ops_bersih_kantor'],
            ['name' => 'Tagihan Nasabah Jatuh Tempo',        'code' => 'ops_tagihan_nasabah'],
            ['name' => 'Briefing AM / Meeting HO',           'code' => 'ops_briefing_am'],
            ['name' => 'Laporan Keamanan Barang dan Gudang', 'code' => 'ops_laporan_keamanan'],
        ];

        // =============================================
        // FIELDS — KEUANGAN (category_id: 3)
        // =============================================
        $keuanganFields = [
            ['name' => 'Tarik Tunai',                   'code' => 'keu_tarik_tunai'],
            ['name' => 'Balance Kas',                   'code' => 'keu_balance_kas'],
            ['name' => 'Input Kas Awal',                'code' => 'keu_input_kas_awal'],
            ['name' => 'Serah Terima Pergantian Shift', 'code' => 'keu_serah_terima_shift'],
            ['name' => 'Closing Kas',                   'code' => 'keu_closing_kas'],
            ['name' => 'Laporan Keuangan',              'code' => 'keu_laporan_keuangan'],
        ];

        // =============================================
        // FIELDS — SDM (category_id: 4)
        // =============================================
        $sdmFields = [
            ['name' => 'Absen Pagi',               'code' => 'sdm_absen_pagi'],
            ['name' => 'Absen Sebelum Istirahat',   'code' => 'sdm_absen_sblm_istirahat'],
            ['name' => 'Absen Sesudah Istirahat',   'code' => 'sdm_absen_sdh_istirahat'],
            ['name' => 'Absen Pulang',              'code' => 'sdm_absen_pulang'],
        ];

        // =============================================
        // FIELDS — MARKETING (category_id: 5)
        // photo_number = angka + wajib upload foto bukti
        // =============================================
        $marketingFields = [
            // Facebook
            ['name' => 'Jumlah Like FB Akun Cabang',        'code' => 'mkt_like_fb_cabang'],
            ['name' => 'Jumlah Like FB Akun Pribadi',       'code' => 'mkt_like_fb_pribadi'],
            ['name' => 'Jumlah Komen FB Akun Cabang',       'code' => 'mkt_komen_fb_cabang'],
            ['name' => 'Jumlah Komen FB Akun Pribadi',      'code' => 'mkt_komen_fb_pribadi'],
            ['name' => 'Jumlah Story FB Akun Cabang',       'code' => 'mkt_story_fb_cabang'],
            ['name' => 'Jumlah Story FB Akun Pribadi',      'code' => 'mkt_story_fb_pribadi'],
            ['name' => 'Jumlah Postingan FB Akun Cabang',   'code' => 'mkt_post_fb_cabang'],
            ['name' => 'Jumlah Postingan FB Akun Pribadi',  'code' => 'mkt_post_fb_pribadi'],
            ['name' => 'Jumlah Inbox Akun Cabang',          'code' => 'mkt_inbox_cabang'],
            ['name' => 'Jumlah Inbox Akun Pribadi',         'code' => 'mkt_inbox_pribadi'],
            ['name' => 'Jumlah Live FB Akun Cabang',        'code' => 'mkt_live_fb_cabang'],
            ['name' => 'Jumlah Live FB Akun Pribadi',       'code' => 'mkt_live_fb_pribadi'],

            // Instagram
            ['name' => 'Jumlah Like IG Akun Cabang',        'code' => 'mkt_like_ig_cabang'],
            ['name' => 'Jumlah Like IG Akun Pribadi',       'code' => 'mkt_like_ig_pribadi'],
            ['name' => 'Jumlah Komen IG Akun Cabang',       'code' => 'mkt_komen_ig_cabang'],
            ['name' => 'Jumlah Komen IG Akun Pribadi',      'code' => 'mkt_komen_ig_pribadi'],
            ['name' => 'Jumlah Story IG Akun Cabang',       'code' => 'mkt_story_ig_cabang'],
            ['name' => 'Jumlah Story IG Akun Pribadi',      'code' => 'mkt_story_ig_pribadi'],
            ['name' => 'Jumlah Postingan IG Akun Cabang',   'code' => 'mkt_post_ig_cabang'],
            ['name' => 'Jumlah Postingan IG Akun Pribadi',  'code' => 'mkt_post_ig_pribadi'],
            ['name' => 'Jumlah DM Akun Cabang',             'code' => 'mkt_dm_cabang'],
            ['name' => 'Jumlah DM Akun Pribadi',            'code' => 'mkt_dm_pribadi'],
            ['name' => 'Jumlah Live IG Akun Cabang',        'code' => 'mkt_live_ig_cabang'],
            ['name' => 'Jumlah Live IG Akun Pribadi',       'code' => 'mkt_live_ig_pribadi'],

            // TikTok
            ['name' => 'Jumlah Like TikTok Akun Cabang',        'code' => 'mkt_like_tiktok_cabang'],
            ['name' => 'Jumlah Like TikTok Akun Pribadi',       'code' => 'mkt_like_tiktok_pribadi'],
            ['name' => 'Jumlah Komen TikTok Akun Cabang',       'code' => 'mkt_komen_tiktok_cabang'],
            ['name' => 'Jumlah Komen TikTok Akun Pribadi',      'code' => 'mkt_komen_tiktok_pribadi'],
            ['name' => 'Jumlah Story TikTok Akun Cabang',       'code' => 'mkt_story_tiktok_cabang'],
            ['name' => 'Jumlah Story TikTok Akun Pribadi',      'code' => 'mkt_story_tiktok_pribadi'],
            ['name' => 'Jumlah Postingan TikTok Akun Cabang',   'code' => 'mkt_post_tiktok_cabang'],
            ['name' => 'Jumlah Postingan TikTok Akun Pribadi',  'code' => 'mkt_post_tiktok_pribadi'],
            ['name' => 'Jumlah Chat TikTok Akun Cabang',        'code' => 'mkt_chat_tiktok_cabang'],
            ['name' => 'Jumlah Chat TikTok Akun Pribadi',       'code' => 'mkt_chat_tiktok_pribadi'],
            ['name' => 'Jumlah Live TikTok Akun Cabang',        'code' => 'mkt_live_tiktok_cabang'],
            ['name' => 'Jumlah Live TikTok Akun Pribadi',       'code' => 'mkt_live_tiktok_pribadi'],

            // WhatsApp
            ['name' => 'Jumlah Chat WA Akun Cabang', 'code' => 'mkt_chat_wa_cabang'],

            // Interaksi Cabang Lain & Pusat
            ['name' => 'Jumlah Like Postingan Cabang Lain',  'code' => 'mkt_like_cabang_lain'],
            ['name' => 'Jumlah Komen Postingan Cabang Lain', 'code' => 'mkt_komen_cabang_lain'],
            ['name' => 'Jumlah Share Postingan Cabang Lain', 'code' => 'mkt_share_cabang_lain'],
            ['name' => 'Jumlah Like Postingan Pusat',        'code' => 'mkt_like_pusat'],
            ['name' => 'Jumlah Komen Postingan Pusat',       'code' => 'mkt_komen_pusat'],
            ['name' => 'Jumlah Share Postingan Pusat',       'code' => 'mkt_share_pusat'],
        ];

        // =============================================
        // INSERT FIELDS — METRIK BISNIS
        // =============================================
        $order = 1;
        foreach ($metrikBisnisFields as $field) {
            DB::table('report_fields')->insert([
                'category_id' => 1,
                'name' => $field['name'],
                'code' => $field['code'],
                'input_type' => $field['input_type'],
                'is_required' => $field['is_required'],
                'is_active' => true,
                'order' => $order++,
                'validation_rules' => json_encode(['min' => 0]),
                'placeholder' => $field['placeholder'],
                'help_text' => $field['help_text'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // =============================================
        // INSERT FIELDS — OPERASIONAL
        // =============================================
        $order = 1;
        foreach ($operasionalFields as $field) {
            DB::table('report_fields')->insert([
                'category_id' => 2,
                'name' => $field['name'],
                'code' => $field['code'],
                'input_type' => 'checkbox',
                'is_required' => false,
                'is_active' => true,
                'order' => $order++,
                'validation_rules' => null,
                'placeholder' => null,
                'help_text' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // =============================================
        // INSERT FIELDS — KEUANGAN
        // =============================================
        $order = 1;
        foreach ($keuanganFields as $field) {
            DB::table('report_fields')->insert([
                'category_id' => 3,
                'name' => $field['name'],
                'code' => $field['code'],
                'input_type' => 'checkbox',
                'is_required' => false,
                'is_active' => true,
                'order' => $order++,
                'validation_rules' => null,
                'placeholder' => null,
                'help_text' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // =============================================
        // INSERT FIELDS — SDM
        // =============================================
        $order = 1;
        foreach ($sdmFields as $field) {
            DB::table('report_fields')->insert([
                'category_id' => 4,
                'name' => $field['name'],
                'code' => $field['code'],
                'input_type' => 'checkbox',
                'is_required' => false,
                'is_active' => true,
                'order' => $order++,
                'validation_rules' => null,
                'placeholder' => null,
                'help_text' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // =============================================
        // INSERT FIELDS — MARKETING
        // =============================================
        $order = 1;
        foreach ($marketingFields as $field) {
            DB::table('report_fields')->insert([
                'category_id' => 5,
                'name' => $field['name'],
                'code' => $field['code'],
                'input_type' => 'photo_number',
                'is_required' => false,
                'is_active' => true,
                'order' => $order++,
                'validation_rules' => null,
                'placeholder' => 'Masukkan jumlah...',
                'help_text' => 'Upload foto bukti dan masukkan jumlahnya',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ ReportMasterSeeder selesai!');
        $this->command->info('   → 5 categories');
        $this->command->info('   → '.count($metrikBisnisFields).' fields Metrik Bisnis (number, wajib)');
        $this->command->info('   → '.count($operasionalFields).' fields Operasional (checkbox)');
        $this->command->info('   → '.count($keuanganFields).' fields Keuangan (checkbox)');
        $this->command->info('   → '.count($sdmFields).' fields SDM (checkbox)');
        $this->command->info('   → '.count($marketingFields).' fields Marketing (photo_number)');
    }
}
