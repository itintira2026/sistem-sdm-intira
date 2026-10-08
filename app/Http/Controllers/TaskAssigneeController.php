<?php

namespace App\Http\Controllers;

use App\Models\TaskAssignee;
use App\Models\TaskStatusLog;
use App\Services\TaskAssignmentPermissionService;
use App\Services\TaskProgressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * BARU — Nonaktifkan SATU assignee dari SATU tugas, tanpa menghapus
 * tugasnya secara keseluruhan (beda dari TaskDetailController::destroy()
 * yang soft-delete tugas untuk SEMUA assignee-nya).
 *
 * Dipakai dari halaman Atur Bobot (weight-reinput), karena di situlah
 * atasan melihat portofolio tugas seseorang dan paling masuk akal buat
 * menghentikan partisipasinya di satu tugas tertentu.
 *
 * SEMANTIK: nonaktif BUKAN "dianggap tidak pernah ada". Persentase
 * dikunci di angka terakhir (persis seperti freeze mutasi jabatan,
 * lihat TaskMutationFreezeService), lalu dikeluarkan dari perhitungan
 * bobot & antrian cek/approve ke depannya. Riwayat entry & validasi
 * yang sudah terjadi tetap ada, gak dihapus.
 */
class TaskAssigneeController extends Controller
{
    public function remove(
        Request $request,
        TaskAssignee $taskAssignee,
        TaskAssignmentPermissionService $permissionService,
        TaskProgressService $progressService
    ) {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $currentUser = Auth::user();
        $pengerja = $taskAssignee->user;
        $task = $taskAssignee->task;

        $authorized = $currentUser->hasRole('superadmin')
            || $task->created_by === $currentUser->id
            || $permissionService->canAssign($currentUser, $pengerja);

        if (! $authorized) {
            return response()->json([
                'message' => 'Anda tidak berwenang menonaktifkan assignee ini.',
            ], 403);
        }

        if ($taskAssignee->finalized_at !== null) {
            return response()->json([
                'message' => 'Assignee ini sudah non-aktif atau dibekukan sebelumnya.',
            ], 422);
        }

        $percentage = $progressService->currentPercentage($taskAssignee);

        $taskAssignee->update([
            'final_percentage' => $percentage,
            'finalized_at' => now(),
            'removed_reason' => $validated['reason'],
            'removed_by' => $currentUser->id,
        ]);

        TaskStatusLog::record(
            $task->id,
            null,
            'removed_by_supervisor',
            null,
            (string) $percentage,
            $currentUser->id,
            $validated['reason'],
            $taskAssignee->id
        );

        return response()->json([
            'message' => "{$pengerja->name} dinonaktifkan dari \"{$task->title}\". Progress dikunci di {$percentage}%.",
            'percentage' => $percentage,
        ]);
    }
}
