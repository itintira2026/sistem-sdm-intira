<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache Spatie
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ===========================
        // 1. PERMISSIONS
        // ===========================
        $permissions = [
            'asign:permission',
            
            'manage:status_penempatan',
            'manage:cabang',
            'manage:akad',
            'manage:collateral',
            'manage:cost',
            'manage:lelang',
            'manage:revenue',
            'manage:user',
            'manage:user_cabang',


            'import:akad',
            'import:cabang',
            'import:collateral',
            'import:cost',
            'import:lelang',
            'import:revenue',
            'import:user',
            'import:user_cabang',

            'view:dashboard_ho',
            'view:dashboard_am',
            'view:dashboard_fo',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // ===========================
        // 2. ROLES
        // ===========================
        $frontOffice = Role::firstOrCreate(['name' => 'Front Office']);
        $areaManager  = Role::firstOrCreate(['name' => 'Area Manager']);
        $headOffice   = Role::firstOrCreate(['name' => 'Head Office']);

       $headOffice->givePermissionTo(Permission::all());


    }
}
