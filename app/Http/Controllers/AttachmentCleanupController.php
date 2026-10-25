<?php

namespace App\Http\Controllers;

use App\Models\TaskAttachment;
use App\Models\TaskStatusLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * BARU — Pembersihan lampiran (khusus Superadmin).
 *
 * index(): daftar lampiran + filter rentang tanggal upload + total ukuran.
 * destroyRange(): hapus SEMUA lampiran di rentang tanggal yang dipilih
 * (hard delete baris + file fisik, lewat event deleting di TaskAttachment).
 *
 * Default filter index: lampiran yang diupload SEBELUM 6 bulan lalu —
 * kandidat paling aman buat dibersihkan.
 */
class AttachmentCleanupController extends Controller
{
    private const PER_PAGE = 20;

    // Rentang yang menyentuh N bulan terakhir dianggap "masih baru":
    // butuh centang konfirmasi tambahan sebelum boleh dihapus.
    private const RECENT_MONTHS = 6;

    public function index(Request $request)
    {
        $this->authorizeSuperadmin();

        $validated = $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
        ]);

        $from = $validated['from'] ?? null;
        $to = $validated['to'] ?? null;

        // Tanpa param sama sekali -> pakai default. Kalau form udah
        // disubmit (param ada, walau kosong), hormati pilihan user.
        if (! $request->has('to') && ! $request->has('from')) {
            $to = $this->recentCutoff()->toDateString();
        }

        $base = fn() => TaskAttachment::query()
            ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to));

        $stats = $base()
            ->selectRaw('COUNT(*) as total, COALESCE(SUM(file_size), 0) as total_size, COALESCE(SUM(CASE WHEN file_size IS NULL THEN 1 ELSE 0 END), 0) as unknown_size')
            ->first();

        $attachments = $base()
            ->with(['uploader', 'taskEntry.taskAssignee.task', 'taskEntry.taskAssignee.user'])
            ->latest('created_at')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return view('superadmin.lampiran.index', [
            'attachments' => $attachments,
            'stats' => $stats,
            'from' => $from,
            'to' => $to,
            'isRecentRange' => $this->touchesRecent($to),
            'recentMonths' => self::RECENT_MONTHS,
        ]);
    }

    public function destroyRange(Request $request)
    {
        $this->authorizeSuperadmin();

        $validated = $request->validate([
            'from' => 'nullable|date',
            // 'to' WAJIB: tanpa batas akhir = bisa nyapu bersih semua lampiran.
            'to' => 'required|date|after_or_equal:from',
            'reason' => 'required|string|max:500',
            'confirm_recent' => 'nullable|boolean',
        ], [
            'to.required' => 'Tanggal "sampai" wajib diisi sebelum menghapus.',
            'reason.required' => 'Alasan penghapusan wajib diisi.',
        ]);

        $from = $validated['from'] ?? null;
        $to = $validated['to'];

        if ($this->touchesRecent($to) && ! $request->boolean('confirm_recent')) {
            return back()
                ->withInput()
                ->with('error', 'Rentang ini mencakup lampiran dalam ' . self::RECENT_MONTHS . ' bulan terakhir. Centang konfirmasi dulu kalau memang yakin.');
        }

        $query = TaskAttachment::query()
            ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
            ->whereDate('created_at', '<=', $to);

        if (! (clone $query)->exists()) {
            return back()->with('error', 'Tidak ada lampiran di rentang itu.');
        }

        $count = 0;
        $bytes = 0;

        // Per baris lewat ->delete() (bukan mass delete) supaya event
        // deleting di TaskAttachment ikut jalan dan file fisiknya terhapus.
        $query->chunkById(100, function ($chunk) use (&$count, &$bytes) {
            foreach ($chunk as $attachment) {
                $bytes += (int) $attachment->file_size;
                $attachment->delete();
                $count++;
            }
        });

        $rangeLabel = ($from ?: 'awal') . ' s/d ' . $to;

        // Satu catatan ringkas per aksi (bukan per file).
        TaskStatusLog::record(
            null,
            null,
            'attachments_purged',
            "{$count} file, " . TaskAttachment::formatBytes($bytes),
            $rangeLabel,
            Auth::id(),
            $validated['reason']
        );

        return redirect()
            ->route('superadmin.lampiran.index', array_filter(['from' => $from, 'to' => $to]))
            ->with('success', "{$count} lampiran dihapus (" . TaskAttachment::formatBytes($bytes) . " dibebaskan, belum termasuk file lama tanpa data ukuran).");
    }

    private function recentCutoff(): Carbon
    {
        return now()->subMonths(self::RECENT_MONTHS)->startOfDay();
    }

    /**
     * Rentang dianggap menyentuh data "baru" kalau tanggal akhirnya
     * lewat dari batas N bulan lalu, atau gak ada tanggal akhir sama sekali.
     */
    private function touchesRecent(?string $to): bool
    {
        if (! $to) {
            return true;
        }

        return Carbon::parse($to)->startOfDay()->gt($this->recentCutoff());
    }

    private function authorizeSuperadmin(): void
    {
        abort_unless(Auth::user()->hasRole('superadmin'), 403, 'Hanya Superadmin yang bisa mengelola lampiran.');
    }
}
