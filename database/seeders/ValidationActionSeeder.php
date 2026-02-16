<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ValidationActionSeeder extends Seeder
{
    public function run(): void
    {
        // DELETE aman (tidak ada FK yang mengarah ke sini dari data existing)
        DB::table('validation_actions')->delete();

        $actions = [
            [
                'name' => 'Mengarahkan',
                'code' => 'mengarahkan',
                'is_active' => true,
                'order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Memberikan Solusi',
                'code' => 'memberikan_solusi',
                'is_active' => true,
                'order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lainnya',
                'code' => 'lainnya',
                'is_active' => true,
                'order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('validation_actions')->insert($actions);

        $this->command->info('✅ ValidationActionSeeder selesai!');
        $this->command->info('   → '.count($actions).' validation actions');
    }
}
