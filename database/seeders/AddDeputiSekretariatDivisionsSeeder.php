<?php

namespace Database\Seeders;

use App\Models\Division;
use Illuminate\Database\Seeder;

/**
 * Part A1 — Seeder 8 divisi tetap (sesuai dokumen konsep §2).
 * Aman dijalankan berkali-kali (firstOrCreate).
 */
class AddDeputiSekretariatDivisionsSeeder extends Seeder
{
    public function run(): void
    {
        $divisions = [
            ['name' => 'Deputi', 'code' => 'deputi'],
            ['name' => 'Sekretaris', 'code' => 'sekretaris'],
        ];

        foreach ($divisions as $division) {
            Division::firstOrCreate(['code' => $division['code']], $division);
        }

        $this->command->info('2 divisi berhasil di-seed.');
    }
}
