<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Validation\Rule;


class UsersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{

use SkipsFailures;

    private $successCount = 0;
    private $rows = [];

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    // public function model(array $row)
    // {
    //     return new User([
    //         //
    //     ]);
    // }
    public function model(array $row)
    {
     
        // Create new branch
        $user = new User([
            'code' => $row['kode_cabang'] ?? null,
            'name' => $row['nama_cabang'] ?? null,
            'phone' => $row['telepon'] ?? null,
            'address' => $row['alamat'] ?? null,
            'is_active' => true,
        ]);

        $this->successCount++;

        return $user;
    }

    /**
     * Validasi data
     */
    public function rules(): array
    {
        return [
            'kode_cabang' => [
                'required',
                'string',
                'max:50',
                Rule::unique('branches', 'code')->whereNull('deleted_at')
            ],
            'nama_cabang' => ['required', 'string', 'max:255'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages(): array
    {
        return [
            'kode_cabang.required' => 'Kode cabang wajib diisi',
            'kode_cabang.unique' => 'Kode cabang :input sudah ada di database',
            'nama_cabang.required' => 'Nama cabang wajib diisi',
        ];
    }

    /**
     * Custom validation attributes
     */
    public function customValidationAttributes(): array
    {
        return [
            'kode_cabang' => 'Kode Cabang',
            'nama_cabang' => 'Nama Cabang',
            'telepon' => 'Telepon',
            'alamat' => 'Alamat',
        ];
    }

    /**
     * Get success count
     */
    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    /**
     * Heading row (baris pertama di Excel)
     */
    public function headingRow(): int
    {
        return 1;
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    
}
