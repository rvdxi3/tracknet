<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\AccountApprovedNotification;
use App\Notifications\AccountRejectedNotification;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PendingUserController extends Controller
{
    public function index()
    {
        $pending = User::where('is_active', false)
            ->whereNotNull('mfa_verified_at')
            ->whereNull('rejected_at')
            ->latest('mfa_verified_at')
            ->paginate(20);

        return view('admin.pending-users.index', compact('pending'));
    }

    public function approve(Request $request, User $user)
    {
        if ($user->is_active || $user->rejected_at) {
            return back()->with('error', 'This account cannot be approved.');
        }

        $user->update([
            'is_active'   => true,
            'approved_at' => now(),
            'approved_by' => Auth::id(),
        ]);

        $user->notify(new AccountApprovedNotification());

        ActivityLogService::accountApproved(Auth::id(), $user->id, $user->email, $request);

        return back()->with('success', "Account for {$user->name} approved successfully.");
    }

    public function reject(Request $request, User $user)
    {
        if ($user->is_active || $user->rejected_at) {
            return back()->with('error', 'This account cannot be rejected.');
        }

        $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $user->update([
            'rejected_at'      => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        $user->notify(new AccountRejectedNotification($request->rejection_reason));

        ActivityLogService::accountRejected(Auth::id(), $user->id, $user->email, $request->rejection_reason, $request);

        return back()->with('success', "Account for {$user->name} has been rejected.");
    }
}
