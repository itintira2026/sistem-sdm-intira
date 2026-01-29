<?php

namespace Database\Seeders;

use App\Models\Akad;
use App\Models\Cabang;
use App\Models\User;
use App\Models\UserCabang;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);
        // User Front Manager
        $frontOffice = User::create([
            'name' => 'FO',
            'email' => 'fo123@example.com',
            'username' => 'fo123',
            'password' => bcrypt('password'),
        ]);
        $frontOffice->assignRole('fo');

        // User Area Manager
        $areaManager = User::create([
            'name' => 'AM',
            'email' => 'am123@example.com',
            'username' => 'am123',
            'password' => bcrypt('password'),
        ]);
        $areaManager->assignRole('manager');

        // User Head Office
        $masterShifu = User::create([
            'name' => 'Master Shifu',
            'email' => 'ms123@example.com',
            'username' => 'ms123',
            'password' => bcrypt('password'),
        ]);
        $masterShifu->assignRole('superadmin');

        $finance = User::create([
            'name' => 'finance',
            'email' => 'finance@example.com',
            'username' => 'finance123',
            'password' => bcrypt('password'),
        ]);
        $finance->assignRole('finance');

        $hr = User::create([
            'name' => 'HR',
            'email' => 'hr@example.com',
            'username' => 'hr123',
            'password' => bcrypt('password'),
        ]);
        $hr->assignRole('hr');

        $marketing = User::create([
            'name' => 'Marketing',
            'email' => 'marketing@example.com',
            'username' => 'marketing123',
            'password' => bcrypt('password'),
        ]);
        $marketing->assignRole('marketing');

        // $headOffice = User::create([
        //     'name' => 'HO',
        //     'email' => 'ho123@example.com',
        //     'username' => 'ho123',
        //     'password' => bcrypt('123123123'),
        // ]);
        // $headOffice->assignRole('Head Office');
        // // Create Cabang
        // $cabang = Cabang::create([
        //     'nama' => 'KC Perdagangan',
        //     'wilayah' => 'KC Perdagangan',
        //     'is_active' => true,
        // ]);

        // // Create UserCabang
        // $userCabang = UserCabang::create([
        //     'cabang_id' => $cabang->id,
        //     'user_id' => $frontOffice->id,
        //     'role' => 'Front Office',
        //     'is_active' => true,
        //     'start_at' => now(),
        //     'end_at' => null,
        // ]);

        // Create Akad

    }
}
