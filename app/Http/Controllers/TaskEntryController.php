<?php

namespace App\Http\Controllers;

use App\Models\TaskAttachment;
use App\Models\TaskEntry;
use App\Models\TaskStatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Part D1 + D4 — Form input entry harian, berlaku untuk Tugas KPI
 * maupun Tugas Tambahan (self-record).
 *
 * D4 (alur ditolak -> window edit otomatis terbuka) sudah menyatu di
 * update(): begitu entry rejected di-resubmit, otomatis balik ke
 * 'pending' dan riwayat cek/approve lama di-reset.
 *
 * DIUBAH: authorizeEdit() sekarang menolak edit kalau assignee sudah
 * finalized_at (freeze mutasi jabatan ATAU dinonaktifkan manual dari
 * tugas — lihat TaskAssignee::isFinalized()). Tanpa ini, entry orang
 * yang sudah "beku" masih bisa diubah, padahal persentasenya sudah
 * dikunci dan gak akan dihitung ulang.
 */
class TaskEntryController extends Controller
{
    public function edit(TaskEntry $entry)
    {
        $this->authorizeEdit($entry);

        $entry->load(['taskAssignee.task', 'taskAssignee.user', 'attachments']);

        return view('task-entries.edit', compact('entry'));
    }

    public function update(Request $request, TaskEntry $entry)
    {
        $this->authorizeEdit($entry);

        $task = $entry->taskAssignee->task;

        $validated = $request->validate([
            'value_number' => $task->tracking_mode === 'numeric' ? 'required|numeric|min:0' : 'nullable|numeric',
            'value_checklist' => 'nullable|boolean',
            // Batas atas 100MB cuma berlaku buat video; gambar/dokumen
            // dicek lebih ketat (5MB) di bawah, setelah lolos validasi dasar.
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,xlsx,xls,csv,mp4,mov,avi|max:102400',
        ]);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $isVideo = str_starts_with((string) $file->getMimeType(), 'video/');

            if (! $isVideo && $file->getSize() > 5 * 1024 * 1024) {
                // ValidationException (bukan back()->withErrors) biar otomatis
                // jadi JSON 422 buat request dari modal, dan redirect-back
                // biasa buat form lama — satu cara untuk dua jalur.
                throw ValidationException::withMessages([
                    'attachment' => 'Ukuran file maksimal 5MB untuk gambar/dokumen. Video boleh sampai 100MB.',
                ]);
            }
        }

        $wasRejected = $entry->validation_status === 'rejected';

        DB::transaction(function () use ($request, $validated, $task, $entry, $wasRejected) {
            $updateData = [
                'value_number' => $task->tracking_mode === 'numeric' ? $validated['value_number'] : null,
                'value_checklist' => $task->tracking_mode === 'checklist' ? $request->boolean('value_checklist') : null,
            ];

            // D4: resubmit dari rejected -> balik ke pending, reset riwayat validasi lama
            if ($wasRejected) {
                $updateData = array_merge($updateData, [
                    'validation_status' => 'pending',
                    'rejected_reason' => null,
                    'checked_by' => null,
                    'checked_at' => null,
                    'approved_by' => null,
                    'approved_at' => null,
                ]);
            }

            $entry->update($updateData);

            if ($wasRejected) {
                TaskStatusLog::record(null, $entry->id, 'resubmitted', 'rejected', 'pending', Auth::id());
            }

            if ($request->hasFile('attachment')) {
                $path = $request->file('attachment')->store('task-attachments', 'public');

                TaskAttachment::create([
                    'task_entry_id' => $entry->id,
                    'file_path' => $path,
                    'file_name' => $request->file('attachment')->getClientOriginalName(),
                    'file_size' => $request->file('attachment')->getSize(),
                    'uploaded_by' => Auth::id(),
                ]);
            }
        });

        $message = $wasRejected ? 'Entry berhasil diperbaiki, dikirim ulang untuk dicek.' : 'Entry berhasil disimpan.';

        if ($request->wantsJson()) {
            return response()->json(['message' => $message]);
        }

        return redirect()
            ->route('tasks.show', $task->id)
            ->with('success', $message);
    }

    private function authorizeEdit(TaskEntry $entry): void
    {
        $user = Auth::user();

        if ($user->hasRole('superadmin')) {
            return;
        }

        if ($entry->taskAssignee->user_id !== $user->id) {
            abort(403, 'Ini bukan entry Anda.');
        }

        // BARU: assignee yang sudah dibekukan (mutasi jabatan ATAU
        // dinonaktifkan manual dari tugas ini) gak boleh diedit lagi.
        if ($entry->taskAssignee->isFinalized()) {
            abort(403, 'Tugas ini sudah non-aktif/dibekukan — entry tidak bisa diubah lagi.');
        }

        // Self-record (validation_status null) -> selalu bisa diedit oleh pemiliknya
        if ($entry->validation_status === null) {
            return;
        }

        // Tugas KPI -> cuma bisa diedit kalau pending atau rejected (D4)
        if (! in_array($entry->validation_status, ['pending', 'rejected'])) {
            abort(403, 'Entry ini sudah dicek/disetujui, tidak bisa diedit lagi.');
        }

        // Entry yang MASIH 'pending' (belum pernah diisi/ditolak) cuma
        // boleh diisi TEPAT di tanggalnya (hari ini). Kalau statusnya
        // 'rejected', TIDAK dibatasi tanggal — itu D4, window edit
        // reopened kapan saja.
        if (
            $entry->validation_status === 'pending'
            && $entry->entry_date !== null
            && ! $entry->entry_date->isToday()
        ) {
            abort(403, 'Entry ini cuma bisa diisi tepat di tanggalnya (hari ini). Tanggal yang sudah lewat tanpa diisi otomatis dianggap gagal.');
        }
    }
}
