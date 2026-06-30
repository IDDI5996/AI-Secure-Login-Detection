<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\BlockedIp;
use App\Models\LoginAttempt;
use App\Models\SuspiciousActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    /**
     * Display user management dashboard
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search filter
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', $search)
                  ->orWhere('email', 'LIKE', $search)
                  ->orWhere('id', 'LIKE', $search);
            });
        }

        // Role filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true)->where('is_locked', false);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'locked') {
                $query->where('is_locked', true);
            } elseif ($request->status === 'admin') {
                $query->where('is_admin', true);
            }
        }

        // Sort
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $users = $query->paginate($request->get('per_page', 20))->withQueryString();

        // Statistics
        $stats = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->where('is_locked', false)->count(),
            'inactive' => User::where('is_active', false)->count(),
            'locked' => User::where('is_locked', true)->count(),
            'admins' => User::where('is_admin', true)->count(),
            'blocked_ips' => BlockedIp::where('is_active', true)->count(),
        ];

        $roles = [
            'super_admin' => 'Super Admin',
            'security_lead' => 'Security Lead',
            'security_analyst' => 'Security Analyst',
            'lecturer' => 'Lecturer',
            'user' => 'User',
        ];

        return view('admin.user-management', compact('users', 'stats', 'roles'));
    }

    /**
     * Get user details for modal
     */
    public function show($id)
    {
        $user = User::with(['loginAttempts' => function ($q) {
            $q->orderBy('attempted_at', 'desc')->limit(10);
        }])->findOrFail($id);

        // Get suspicious activities count
        $suspiciousCount = SuspiciousActivity::where('user_id', $id)->count();

        // Get last login
        $lastLogin = LoginAttempt::where('user_id', $id)
            ->where('is_successful', true)
            ->orderBy('attempted_at', 'desc')
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'suspicious_count' => $suspiciousCount,
                'last_login' => $lastLogin,
                'login_count' => LoginAttempt::where('user_id', $id)->count(),
                'failed_attempts' => LoginAttempt::where('user_id', $id)->where('is_successful', false)->count(),
            ]
        ]);
    }

    /**
     * Toggle user active status
     */
    public function toggleActive(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Prevent disabling your own account
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot disable your own account.'
            ], 403);
        }

        $user->update([
            'is_active' => !$user->is_active,
        ]);

        // Log the action
        $this->logAction($user, $user->is_active ? 'enabled' : 'disabled');

        return response()->json([
            'success' => true,
            'message' => "User {$user->name} has been " . ($user->is_active ? 'enabled' : 'disabled') . ".",
            'status' => $user->is_active ? 'active' : 'inactive'
        ]);
    }

    /**
     * Toggle user lock status
     */
    public function toggleLock(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Prevent locking your own account
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot lock your own account.'
            ], 403);
        }

        $user->update([
            'is_locked' => !$user->is_locked,
            'locked_at' => $user->is_locked ? null : now(),
            'locked_by' => $user->is_locked ? null : auth()->id(),
            'lock_reason' => $user->is_locked ? null : $request->reason ?? 'Locked by admin',
            'unlocks_at' => $user->is_locked ? null : null,
        ]);

        $action = $user->is_locked ? 'locked' : 'unlocked';

        return response()->json([
            'success' => true,
            'message' => "User {$user->name} has been " . $action . ".",
            'status' => $user->is_locked ? 'locked' : 'active'
        ]);
    }

    /**
     * Delete user (soft delete or permanent)
     */
    public function destroy(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting your own account
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account.'
            ], 403);
        }

        // If permanent delete
        if ($request->permanent) {
            $user->forceDelete();
            $message = "User {$user->name} has been permanently deleted.";
        } else {
            $user->delete();
            $message = "User {$user->name} has been soft-deleted.";
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Restore soft-deleted user
     */
    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return response()->json([
            'success' => true,
            'message' => "User {$user->name} has been restored."
        ]);
    }

    /**
     * Block IP address
     */
    public function blockIp(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|ip|unique:blocked_ips,ip_address',
            'reason' => 'required|string|max:500',
            'unblocks_at' => 'nullable|date|after:now',
        ]);

        $blocked = BlockedIp::create([
            'ip_address' => $request->ip_address,
            'blocked_by' => auth()->id(),
            'reason' => $request->reason,
            'blocked_at' => now(),
            'unblocks_at' => $request->unblocks_at,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => "IP {$request->ip_address} has been blocked.",
            'data' => $blocked
        ]);
    }

    /**
     * Unblock IP address
     */
    public function unblockIp($id)
    {
        $blocked = BlockedIp::findOrFail($id);
        $blocked->update([
            'is_active' => false,
            'unblocked_at' => now(),
            'unblocked_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "IP {$blocked->ip_address} has been unblocked."
        ]);
    }

    /**
     * Get blocked IPs
     */
    public function getBlockedIps(Request $request)
    {
        $query = BlockedIp::with(['blockedBy', 'unblockedBy'])
            ->where('is_active', true);

        if ($request->filled('search')) {
            $query->where('ip_address', 'LIKE', '%' . $request->search . '%');
        }

        $ips = $query->orderBy('blocked_at', 'desc')->paginate(20);

        return response()->json($ips);
    }

    /**
     * Get user login history
     */
    public function loginHistory($id)
    {
        $user = User::findOrFail($id);
        $history = LoginAttempt::where('user_id', $id)
            ->orderBy('attempted_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'history' => $history,
                'total' => LoginAttempt::where('user_id', $id)->count(),
                'failed' => LoginAttempt::where('user_id', $id)->where('is_successful', false)->count(),
                'suspicious' => LoginAttempt::where('user_id', $id)->where('is_suspicious', true)->count(),
            ]
        ]);
    }

    /**
     * Update user role
     */
    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'role' => ['required', Rule::in(['super_admin', 'security_lead', 'security_analyst', 'lecturer', 'user'])],
            'is_admin' => 'boolean',
        ]);

        $user = User::findOrFail($id);

        // Prevent modifying your own role
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot modify your own role.'
            ], 403);
        }

        $user->update([
            'role' => $request->role,
            'is_admin' => $request->is_admin ?? false,
        ]);

        return response()->json([
            'success' => true,
            'message' => "User role updated successfully.",
            'data' => $user
        ]);
    }

    /**
     * Send verification email
     */
    public function sendVerification($id)
    {
        $user = User::findOrFail($id);

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'message' => 'User already verified.'
            ]);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'success' => true,
            'message' => 'Verification email sent successfully.'
        ]);
    }

    /**
     * Bulk action on users
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'action' => 'required|in:activate,deactivate,lock,unlock,delete',
        ]);

        $userIds = $request->user_ids;

        // Prevent self action
        if (in_array(auth()->id(), $userIds)) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot perform this action on your own account.'
            ], 403);
        }

        $count = 0;
        foreach ($userIds as $id) {
            $user = User::find($id);
            if (!$user) continue;

            switch ($request->action) {
                case 'activate':
                    $user->update(['is_active' => true]);
                    break;
                case 'deactivate':
                    $user->update(['is_active' => false]);
                    break;
                case 'lock':
                    $user->update([
                        'is_locked' => true,
                        'locked_at' => now(),
                        'locked_by' => auth()->id(),
                        'lock_reason' => 'Bulk action by admin'
                    ]);
                    break;
                case 'unlock':
                    $user->update([
                        'is_locked' => false,
                        'locked_at' => null,
                        'locked_by' => null,
                        'lock_reason' => null
                    ]);
                    break;
                case 'delete':
                    $user->delete();
                    break;
            }
            $count++;
        }

        return response()->json([
            'success' => true,
            'message' => "Bulk action completed on {$count} user(s)."
        ]);
    }

    /**
     * Export users to CSV
     */
    public function export(Request $request)
    {
        $users = User::all();

        $csv = "ID,Name,Email,Role,Status,Verified,Created At\n";
        foreach ($users as $user) {
            $status = $user->is_locked ? 'Locked' : ($user->is_active ? 'Active' : 'Inactive');
            $csv .= "{$user->id},{$user->name},{$user->email},{$user->role},{$status}," .
                    ($user->hasVerifiedEmail() ? 'Yes' : 'No') . ",{$user->created_at}\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="users-export-' . date('Y-m-d') . '.csv"',
        ]);
    }

    /**
     * Log admin actions (helper method)
     */
    private function logAction($user, $action)
    {
        // You can implement audit logging here
        // Example: AuditLog::create([...]);
        \Log::info("Admin action: {$action} user {$user->id} - {$user->email} by " . auth()->user()->email);
    }
}