<?php

namespace App\Http\Controllers\Superadmin;

use App\Models\LogPresensiPhotoCleanup;
use App\Services\PresensiPhotoCleanupService;
use App\Http\Controllers\Controller;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class PresensiPhotoCleanupController extends Controller
{
      public function __construct(
        private PresensiPhotoCleanupService $service
    ) {}
    
    public function index(Request $request)
    {
        $preview      = $this->service->preview();
        $storageStats = $this->service->storageStats();

        // Filter history
        $historyDateFrom = $request->input('history_date_from');
        $historyDateTo   = $request->input('history_date_to');
        $historyType     = $request->input('history_type');

        $logsQuery = LogPresensiPhotoCleanup::with('executor')
            ->orderBy('executed_at', 'desc');

        if ($historyDateFrom) {
            $logsQuery->whereDate('executed_at', '>=', $historyDateFrom);
        }

        if ($historyDateTo) {
            $logsQuery->whereDate('executed_at', '<=', $historyDateTo);
        }

        if ($historyType) {
            $logsQuery->where('execution_type', $historyType);
        }

        $logs = $logsQuery->paginate(10)->withQueryString();

        return view('superadmin.absensi.photo-cleanup.index', [
            'preview'         => $preview,
            'storageStats'    => $storageStats,
            'logs'            => $logs,
            'threshold'       => $this->service->getThresholdDate(),
            // filter history untuk mempertahankan state form
            'historyDateFrom' => $historyDateFrom,
            'historyDateTo'   => $historyDateTo,
            'historyType'     => $historyType,
        ]);
    }

    /**
     * Eksekusi penghapusan foto
     * Perlu konfirmasi ketik "HAPUS" dari form
     */
    public function execute(Request $request)
    {
        $request->validate([
            'confirm' => 'required|in:HAPUS',
        ], [
            'confirm.required' => 'Konfirmasi wajib diisi.',
            'confirm.in'       => 'Ketik HAPUS untuk konfirmasi penghapusan.',
        ]);

        $log = $this->service->cleanup(
            executedBy: Auth::id(),
            executionType: 'manual',
        );

        return redirect()
            ->route('superadmin.absensi.photo-cleanup.index')
            ->with('success', "Berhasil menghapus {$log->total_photos_deleted} foto, membebaskan {$log->size_freed_human}.");
    }

    /**
     * Preview by range — dipanggil saat superadmin submit form filter
     * Menampilkan halaman yang sama dengan tambahan data previewRange
     */
    public function previewRange(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to'   => 'required|date',
        ], [
            'date_from.required' => 'Tanggal Dari wajib diisi.',
            'date_from.date'     => 'Tanggal Dari tidak valid.',
            'date_to.required'   => 'Tanggal Sampai wajib diisi.',
            'date_to.date'       => 'Tanggal Sampai tidak valid.',
        ]);

        $dateFrom = Carbon::parse($request->input('date_from'))->startOfDay();
        $dateTo   = Carbon::parse($request->input('date_to'))->endOfDay();

        // Validasi logika dasar (date_from > date_to)
        $errors = $this->service->validateDateRange($dateFrom, $dateTo);

        if (!empty($errors)) {
            return redirect()
                ->route('superadmin.absensi.photo-cleanup.index')
                ->withErrors($errors)
                ->withInput();
        }

        $previewRange = $this->service->previewByRange($dateFrom, $dateTo);
        $preview      = $this->service->preview();
        $storageStats = $this->service->storageStats();
        $logs         = LogPresensiPhotoCleanup::with('executor')
            ->orderBy('executed_at', 'desc')
            ->paginate(10);

        return view('superadmin.absensi.photo-cleanup.index', [
            'preview'      => $preview,
            'storageStats' => $storageStats,
            'logs'         => $logs,
            'threshold'    => $this->service->getThresholdDate(),
            // data range untuk ditampilkan di section Opsi 2
            'previewRange' => $previewRange,
            'dateFrom'     => $request->input('date_from'),
            'dateTo'       => $request->input('date_to'),
            // filter history default
            'historyDateFrom' => null,
            'historyDateTo'   => null,
            'historyType'     => null,
        ]);
    }

    /**
     * Eksekusi hapus by range tanggal
     */
    public function executeRange(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to'   => 'required|date',
            'confirm'   => 'required|in:HAPUS',
        ], [
            'date_from.required' => 'Tanggal Dari wajib diisi.',
            'date_to.required'   => 'Tanggal Sampai wajib diisi.',
            'confirm.required'   => 'Konfirmasi wajib diisi.',
            'confirm.in'         => 'Ketik HAPUS untuk konfirmasi penghapusan.',
        ]);

        $dateFrom = Carbon::parse($request->input('date_from'))->startOfDay();
        $dateTo   = Carbon::parse($request->input('date_to'))->endOfDay();

        // Validasi logika dasar
        $errors = $this->service->validateDateRange($dateFrom, $dateTo);

        if (!empty($errors)) {
            return redirect()
                ->route('superadmin.absensi.photo-cleanup.index')
                ->withErrors($errors)
                ->withInput();
        }

        $log = $this->service->cleanupByRange(
            dateFrom: $dateFrom,
            dateTo: $dateTo,
            executedBy: Auth::id(),
            executionType: 'manual',
        );

        return redirect()
            ->route('superadmin.absensi.photo-cleanup.index')
            ->with('success', "Berhasil menghapus {$log->total_photos_deleted} foto dari range {$dateFrom->translatedFormat('d F Y')} – {$dateTo->translatedFormat('d F Y')}, membebaskan {$log->size_freed_human}.");
    }
}
