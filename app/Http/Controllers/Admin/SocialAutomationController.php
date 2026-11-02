<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\PublishScheduledPostJob;
use App\Models\ScheduledPost;
use App\Support\AdminPermissions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SocialAutomationController extends Controller
{
    public function retry(Request $request, ScheduledPost $scheduledPost): RedirectResponse
    {
        $user = $request->user();
        if (! $user?->is_admin || ! $user->canAccessAdminModule(AdminPermissions::SETTINGS)) {
            abort(403);
        }

        $scheduledPost->forceFill([
            'status' => ScheduledPost::STATUS_PENDING,
            'error_log' => null,
        ])->save();

        PublishScheduledPostJob::dispatch($scheduledPost);

        return back()->with('status', 'Post re-queued for publishing.');
    }
}
