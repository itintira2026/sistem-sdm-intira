<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Branch;
use App\Models\BranchUser;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class UsersImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    public $errors = [];
    public $successCount = 0;
    private $currentRow = 1;

    protected $allowedRoles = [
        'fo',
        'manager',
        'superadmin',
        'finance',
        'hr',
    ];

    public function model(array $row)
    {
        $this->currentRow++;

        try {
            // ===============================
            // NORMALISASI
            // ===============================
            $role = strtolower(trim($row['role'] ?? ''));
            $isManager = (int) ($row['is_manager'] ?? 0);

            $phone = isset($row['telepon'])
                ? (string) preg_replace('/[^0-9]/', '', $row['telepon'])
                : null;

            // ===============================
            // VALIDASI WAJIB
            // ===============================
            if (empty($row['kode_cabang'])) {
                throw new \Exception('Kode cabang wajib diisi');
            }

            if (empty($row['nama_pengguna'])) {
                throw new \Exception('Nama pengguna wajib diisi');
            }

            if (empty($row['username'])) {
                throw new \Exception('Username wajib diisi');
            }

            if (empty($row['email'])) {
                throw new \Exception('Email wajib diisi');
            }

            if (empty($role)) {
                throw new \Exception('Role wajib diisi');
            }

            if (!in_array($role, $this->allowedRoles)) {
                throw new \Exception("Role {$role} tidak valid");
            }

            // ===============================
            // CARI CABANG
            // ===============================
            $branch = Branch::where('code', $row['kode_cabang'])->first();

            if (!$branch) {
                throw new \Exception("Kode cabang {$row['kode_cabang']} tidak ditemukan");
            }

            // ===============================
            // CARI / BUAT USER
            // ===============================
            $user = User::firstOrCreate(
                [
                    'username' => $row['username'],
                ],
                [
                    'name'      => $row['nama_pengguna'],
                    'email'     => $row['email'],
                    'phone'     => $phone,
                    'password'  => Hash::make('password123'),
                    'is_active' => 1,
                ]
            );

            // ===============================
            // UPDATE TELEPON JIKA KOSONG
            // ===============================
            if (empty($user->phone) && $phone) {
                $user->update(['phone' => $phone]);
            }

            // ===============================
            // ASSIGN ROLE (SPATIE)
            // ===============================
            if (!$user->hasRole($role)) {
                $user->assignRole($role);
            }

            // ===============================
            // RELASI USER - CABANG
            // ===============================
            $exists = BranchUser::where('branch_id', $branch->id)
                ->where('user_id', $user->id)
                ->exists();

            if (!$exists) {
                BranchUser::create([
                    'branch_id' => $branch->id,
                    'user_id'   => $user->id,
                    'is_manager' => $isManager,
                ]);
            }

            $this->successCount++;
        } catch (\Exception $e) {
            $this->errors[] = [
                'row'     => $this->currentRow,
                'message' => $e->getMessage()
            ];
        }

        return null;
    }
}
