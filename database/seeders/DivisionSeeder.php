<?php

namespace Database\Seeders;

use App\Models\Division;
use Illuminate\Database\Seeder;

/**
 * Part A1 — Seeder 8 divisi tetap (sesuai dokumen konsep §2).
 * Aman dijalankan berkali-kali (firstOrCreate).
 */
class DivisionSeeder extends Seeder
{
    public function run(): void
    {
        $divisions = [
            ['name' => 'Operasional', 'code' => 'operasional'],
            ['name' => 'Bisnis', 'code' => 'bisnis'],
            ['name' => 'Penjualan', 'code' => 'penjualan'],
            ['name' => 'IT', 'code' => 'it'],
            ['name' => 'SPI', 'code' => 'spi'],
            ['name' => 'Finance', 'code' => 'finance'],
            ['name' => 'HR', 'code' => 'hr'],
            ['name' => 'Umum', 'code' => 'umum'],
        ];

        foreach ($divisions as $division) {
            Division::firstOrCreate(['code' => $division['code']], $division);
        }

        $this->command->info('8 divisi berhasil di-seed.');

        $this->call(AddDeputiSekretariatDivisionsSeeder::class);
    }
}
