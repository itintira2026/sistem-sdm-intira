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
            'username' => 'fo123',
            'password' => bcrypt('123123123'),
        ]);
        $frontOffice->assignRole('Front Office');

        // User Area Manager
        $areaManager = User::create([
            'name' => 'AM',
            'username' => 'am123',
            'password' => bcrypt('123123123'),
        ]);
        $areaManager->assignRole('Area Manager');

        // User Head Office
        $masterShifu = User::create([
            'name' => 'Master Shifu',
            'username' => 'ms123',
            'password' => bcrypt('123123123'),
        ]);
        $masterShifu->assignRole('Head Office');

        $headOffice = User::create([
            'name' => 'HO',
            'username' => 'ho123',
            'password' => bcrypt('123123123'),
        ]);
        $headOffice->assignRole('Head Office');

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
