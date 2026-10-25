<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

/**
 * Part A2 — Seeder role baru.
 *
 * Role existing (superadmin, manager, fo, marketing, hr) TIDAK disentuh
 * sama sekali di sini — cuma nambah role baru yang belum ada.
 *
 * Cara jalanin:
 *   php artisan db:seed --class=NewRolesSeeder
 *
 * Aman dijalankan berkali-kali (pakai firstOrCreate, gak akan duplikat/error
 * kalau sebagian role udah pernah dibuat sebelumnya).
 */
class NewRolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'owner',
            'ketua',
            'direktur_utama',
            'direktur_bisnis',
            'direktur_operasional',
            'kadiv',
            'staff',
            'korwil',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'web',
            ]);
        }

        $this->command->info('8 role baru berhasil di-seed: ' . implode(', ', $roles));
    }
}
