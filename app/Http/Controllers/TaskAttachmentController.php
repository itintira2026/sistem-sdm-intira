<?php

namespace App\Http\Controllers;

use App\Models\TaskAttachment;
use App\Services\TaskValidationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskAttachmentController extends Controller
{
    public function show(TaskAttachment $attachment, TaskValidationService $validation)
    {
        $user = Auth::user();
        $entry = $attachment->taskEntry()->with('taskAssignee.task', 'taskAssignee.user')->firstOrFail();

        abort_unless($validation->canViewEntry($user, $entry), 403);

        $disk = Storage::disk('local')->exists($attachment->file_path) ? 'local' : 'public';

        return Storage::disk($disk)->response($attachment->file_path, $attachment->file_name);
    }
}
