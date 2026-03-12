<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ActivityLogService
{
    /**
     * Write an activity entry to the database and to the Laravel log file.
     */
    public static function log(
        string  $action,
        string  $description,
        ?int    $userId   = null,
        ?array  $metadata = null,
        ?Request $request = null
    ): void {
        $ip        = $request?->ip();
        $userAgent = $request?->userAgent();

        // Database record (for admin panel) — wrapped so a missing table never crashes the request
        try {
            ActivityLog::create([
                'user_id'    => $userId,
                'action'     => $action,
                'description'=> $description,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
                'metadata'   => $metadata,
            ]);
        } catch (\Throwable $e) {
            // Never let an activity log failure crash the request
        }

        // Flat log record (uses default channel — stderr on Railway, daily locally)
        try {
            Log::info("[ACTIVITY] {$action}", array_filter([
                'user_id'     => $userId,
                'description' => $description,
                'ip'          => $ip,
                'metadata'    => $metadata,
            ]));
        } catch (\Throwable $e) {
            // Never let a logging failure crash the request
        }
    }

    // ── Convenience wrappers ─────────────────────────────────────────────────

    public static function loginSuccess(int $userId, string $email, Request $request): void
    {
        self::log('login_success', "Login successful for {$email}", $userId, ['email' => $email], $request);
    }

    public static function loginFailed(string $email, Request $request): void
    {
        self::log('login_failed', "Failed login attempt for email: {$email}", null, ['email' => $email], $request);
    }

    public static function logout(int $userId, string $email, Request $request): void
    {
        self::log('logout', "User {$email} logged out", $userId, ['email' => $email], $request);
    }

    public static function registered(int $userId, string $email, Request $request): void
    {
        self::log('registered', "New account registered for {$email}", $userId, ['email' => $email], $request);
    }

    public static function mfaVerified(int $userId, string $method, Request $request): void
    {
        self::log('mfa_verified', "MFA verified via {$method}", $userId, ['method' => $method], $request);
    }

    public static function accountApproved(int $adminId, int $targetUserId, string $targetEmail, Request $request): void
    {
        self::log('account_approved', "Admin approved account for {$targetEmail}", $adminId, [
            'target_user_id' => $targetUserId,
            'target_email'   => $targetEmail,
        ], $request);
    }

    public static function accountRejected(int $adminId, int $targetUserId, string $targetEmail, ?string $reason, Request $request): void
    {
        self::log('account_rejected', "Admin rejected account for {$targetEmail}", $adminId, [
            'target_user_id' => $targetUserId,
            'target_email'   => $targetEmail,
            'reason'         => $reason,
        ], $request);
    }

    public static function orderPlaced(int $userId, string $orderNumber, float $total, Request $request): void
    {
        self::log('order_placed', "Order {$orderNumber} placed (₱" . number_format($total, 2) . ")", $userId, [
            'order_number' => $orderNumber,
            'total'        => $total,
        ], $request);
    }

    public static function orderCancelled(int $userId, string $orderNumber, Request $request): void
    {
        self::log('order_cancelled', "Order {$orderNumber} cancelled", $userId, [
            'order_number' => $orderNumber,
        ], $request);
    }

    public static function passwordChanged(int $userId, string $email, Request $request): void
    {
        self::log('password_changed', "Password changed for {$email}", $userId, ['email' => $email], $request);
    }
}
