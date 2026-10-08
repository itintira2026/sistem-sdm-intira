<?php

namespace App\Http\Controllers;

use App\Exports\ReportOfficialDataTemplateExport;
use App\Imports\ReportOfficialDataImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ReportOfficialDataController extends Controller
{
    public function index()
    {
        return view('report-official-data.index');
    }

    public function template()
    {
        return Excel::download(new ReportOfficialDataTemplateExport, 'template_data_resmi.xlsx');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'file.required' => 'File wajib diupload.',
            'file.mimes' => 'File harus berformat Excel (.xlsx/.xls) atau CSV.',
            'file.max' => 'Ukuran file maksimal 5MB.',
        ]);

        $import = new ReportOfficialDataImport(Auth::id());

        Excel::import($import, $request->file('file'));

        return back()
            ->with('success', "{$import->successCount} baris berhasil diproses, {$import->failCount} baris gagal.")
            ->with('importResults', $import->results);
    }
}
