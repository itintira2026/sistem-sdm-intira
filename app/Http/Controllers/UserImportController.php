<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class UserImportController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $import = new UsersImport();
        Excel::import($import, $request->file('file'));

        return response()->json([
            'success' => count($import->errors) === 0,
            'inserted' => $import->successCount,
            'errors' => $import->errors,
        ]);
    }
}
