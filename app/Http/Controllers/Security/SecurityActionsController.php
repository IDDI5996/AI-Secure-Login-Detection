<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LoginAttempt;
use App\Models\SuspiciousActivity;
use App\Models\BlockedIp;
use App\Models\SystemLock;
use App\Models\ThreatDatabase;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use League\Csv\Writer;
use SplTempFileObject;

class SecurityActionsController extends Controller
{
    /**
     * Search users for security actions
     */
    public function searchUsers(Request $request)
    {
        // Start output buffering to catch any stray output
    ob_start();
    
    try {
        // Validate input
        $validated = $request->validate([
            'q' => 'required|string|min:2'
        ]);
        
        $query = $validated['q'];
        
        // Simple query without any complex transformations
        $users = User::where('name', 'like', '%'.$query.'%')
            ->orWhere('email', 'like', '%'.$query.'%')
            ->select('id', 'name', 'email', 'is_locked')
            ->orderBy('name')
            ->limit(10)
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_locked' => (bool) $user->is_locked
                ];
            })
            ->values() // Reset array keys
            ->all(); // Convert to plain array
        
        // Clear any output that might have been generated
        ob_end_clean();
        
        // Return clean JSON response
        return response()->json($users, 200, [
            'Content-Type' => 'application/json; charset=utf-8'
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        ob_end_clean();
        return response()->json([
            'error' => 'Validation error',
            'messages' => $e->errors()
        ], 422);
        
    } catch (\Exception $e) {
        // Get any output that was generated before the error
        $output = ob_get_contents();
        ob_end_clean();
        
        \Log::error('Search users error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'output' => $output,
            'query' => $request->q ?? 'none'
        ]);
        
        return response()->json([
            'error' => 'Server error',
            'message' => $e->getMessage()
        ], 500);
      }
    }
    
    /**
     * Lock the entire system
     */
    public function lockSystem(Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
            'duration_minutes' => 'required|integer|min:1|max:1440' // max 24 hours
        ]);
        
        // Check if system is already locked
        $existingLock = SystemLock::where('is_active', true)->first();
        if ($existingLock) {
            return response()->json([
                'success' => false,
                'message' => 'System is already locked.'
            ]);
        }
        
        // Create system lock
        $lock = SystemLock::create([
            'locked_by' => auth()->id(),
            'reason' => $request->reason,
            'locked_at' => now(),
            'unlocks_at' => now()->addMinutes($request->duration_minutes),
            'is_active' => true
        ]);
        
        // Log the action
        SuspiciousActivity::create([
            'user_id' => auth()->id(),
            'activity_type' => 'system_lock',
            'description' => 'System locked by security: ' . $request->reason,
            'risk_score' => 0.1,
            'status' => 'resolved',
            'detection_reasons' => ['manual_lock']
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'System locked successfully.',
            'lock' => $lock
        ]);
    }
    
    /**
     * Unlock the system
     */
    public function unlockSystem(Request $request)
    {
        $lock = SystemLock::where('is_active', true)->first();
        
        if (!$lock) {
            return response()->json([
                'success' => false,
                'message' => 'System is not locked.'
            ]);
        }
        
        $lock->update([
            'is_active' => false,
            'unlocked_at' => now(),
            'unlocked_by' => auth()->id()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'System unlocked successfully.'
        ]);
    }
    
    /**
     * Block an IP address
     */
    public function blockIpAddress(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|ip',
            'reason' => 'required|string|max:500',
            'duration_hours' => 'required|integer|min:1|max:720' // max 30 days
        ]);
        
        // Check if IP is already blocked
        $existingBlock = BlockedIp::where('ip_address', $request->ip_address)
            ->where('is_active', true)
            ->first();
            
        if ($existingBlock) {
            return response()->json([
                'success' => false,
                'message' => 'IP address is already blocked.'
            ]);
        }
        
        // Block the IP
        $block = BlockedIp::create([
            'ip_address' => $request->ip_address,
            'blocked_by' => auth()->id(),
            'reason' => $request->reason,
            'blocked_at' => now(),
            'unblocks_at' => now()->addHours($request->duration_hours),
            'is_active' => true
        ]);
        
        // Get recent login attempts from this IP
        $loginAttempts = LoginAttempt::where('ip_address', $request->ip_address)
            ->where('created_at', '>=', now()->subDays(7))
            ->get();
            
        // Log suspicious activity for each affected user
        foreach ($loginAttempts as $attempt) {
            if ($attempt->user_id) {
                SuspiciousActivity::create([
                    'user_id' => $attempt->user_id,
                    'activity_type' => 'ip_blocked',
                    'description' => "IP address {$request->ip_address} blocked: {$request->reason}",
                    'risk_score' => 0.9,
                    'status' => 'resolved',
                    'detection_reasons' => ['ip_block_manual']
                ]);
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'IP address blocked successfully.',
            'block' => $block,
            'affected_attempts' => $loginAttempts->count()
        ]);
    }
    
    /**
     * Unblock an IP address
     */
    public function unblockIpAddress($ipId)
    {
        $block = BlockedIp::find($ipId);
        
        if (!$block) {
            return response()->json([
                'success' => false,
                'message' => 'IP block not found.'
            ]);
        }
        
        $block->update([
            'is_active' => false,
            'unblocked_at' => now(),
            'unblocked_by' => auth()->id()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'IP address unblocked successfully.'
        ]);
    }
    
    /**
     * Require 2FA for a user
     */
    public function require2FA(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'reason' => 'required|string|max:500'
        ]);
        
        $user = User::find($request->user_id);
        
        // Check if Google2FA is available
        if (!class_exists('PragmaRX\Google2FA\Google2FA')) {
            return response()->json([
                'success' => false,
                'message' => 'Google2FA package is not installed. Run: composer require pragmarx/google2fa'
            ], 500);
        }
        
        // Enable 2FA if not already enabled
        if (!$user->two_factor_enabled) {
            $google2fa = app('pragmarx.google2fa');
            
            $user->update([
                'two_factor_enabled' => true,
                'two_factor_secret' => encrypt($google2fa->generateSecretKey()),
                'two_factor_recovery_codes' => encrypt(json_encode(
                    \Illuminate\Support\Collection::times(8, function () {
                        return \Illuminate\Support\Str::random(10);
                    })->all()
                ))
            ]);
        }
        
        // Force 2FA confirmation
        $user->update([
            'two_factor_confirmed_at' => null // Force re-confirmation
        ]);
        
        // Log the action
        SuspiciousActivity::create([
            'user_id' => $user->id,
            'activity_type' => '2fa_required',
            'description' => "2FA required by security: {$request->reason}",
            'risk_score' => 0.3,
            'status' => 'resolved',
            'detection_reasons' => ['security_requirement']
        ]);
        
        return response()->json([
            'success' => true,
            'message' => '2FA required for user successfully.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'two_factor_enabled' => true,
                'two_factor_confirmed' => false
            ]
        ]);
    }
    
    /**
     * Lock a user account
     */
    public function lockUserAccount(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'reason' => 'required|string|max:500',
            'duration_hours' => 'required|integer|min:1|max:720'
        ]);
        
        $user = User::find($request->user_id);
        
        // Check if user is already locked
        if ($user->is_locked) {
            return response()->json([
                'success' => false,
                'message' => 'User account is already locked.'
            ]);
        }
        
        // Lock the user account
        $user->update([
            'is_locked' => true,
            'locked_at' => now(),
            'locked_by' => auth()->id(),
            'lock_reason' => $request->reason,
            'unlocks_at' => now()->addHours($request->duration_hours)
        ]);
        
        // Log the action
        SuspiciousActivity::create([
            'user_id' => $user->id,
            'activity_type' => 'account_locked',
            'description' => "Account locked by security: {$request->reason}",
            'risk_score' => 0.8,
            'status' => 'resolved',
            'detection_reasons' => ['manual_lock']
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'User account locked successfully.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_locked' => true,
                'unlocks_at' => $user->unlocks_at
            ]
        ]);
    }
    
    /**
     * Unlock a user account
     */
    public function unlockUserAccount($userId)
    {
        $user = User::find($userId);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ]);
        }
        
        if (!$user->is_locked) {
            return response()->json([
                'success' => false,
                'message' => 'User account is not locked.'
            ]);
        }
        
        $user->update([
            'is_locked' => false,
            'unlocked_at' => now(),
            'unlocked_by' => auth()->id(),
            'lock_reason' => null,
            'unlocks_at' => null
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'User account unlocked successfully.'
        ]);
    }
    
    /**
     * Generate a security report
     */
    public function generateReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:security,threats,users,comprehensive',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:pdf,csv,json'
        ]);
        
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        
        $data = $this->prepareReportData($startDate, $endDate, $request->report_type);
        
        if ($request->format === 'json') {
            return response()->json([
                'success' => true,
                'report' => $data
            ]);
        }
        
        // For file downloads, we need to return proper response
        if ($request->format === 'csv') {
            return $this->exportReportToCSV($data, $request->report_type, $startDate, $endDate);
        }
        
        // PDF download
        return $this->exportReportToPDF($data, $request->report_type, $startDate, $endDate);
    }
    
    /**
     * Export report to CSV
     */
    private function exportReportToCSV($data, $reportType, $startDate, $endDate)
    {
        // Simple CSV generation
        $filename = "security-report-{$reportType}-" . Carbon::now()->format('Y-m-d-H-i-s') . '.csv';
        
        // Create CSV content
        $csvContent = "Security Report - " . ucfirst($reportType) . "\n";
        $csvContent .= "Generated on: " . Carbon::now()->format('Y-m-d H:i:s') . "\n";
        $csvContent .= "Period: {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}\n";
        $csvContent .= "Generated by: " . auth()->user()->name . "\n\n";
        
        if ($reportType === 'security' || $reportType === 'comprehensive') {
            $csvContent .= "Security Metrics\n";
            $csvContent .= "Metric,Value\n";
            foreach ($data['security_metrics'] as $key => $value) {
                $csvContent .= ucfirst(str_replace('_', ' ', $key)) . "," . $value . "\n";
            }
            $csvContent .= "\n";
        }
        
        if ($reportType === 'threats' || $reportType === 'comprehensive') {
            $csvContent .= "Threat Analysis\n";
            $csvContent .= "Category,Count\n";
            $csvContent .= "Suspicious Attempts," . ($data['threat_analysis']['suspicious_attempts'] ?? 0) . "\n";
            $csvContent .= "Failed Logins," . ($data['threat_analysis']['failed_logins'] ?? 0) . "\n";
            $csvContent .= "High Risk Activities," . ($data['threat_analysis']['high_risk_activities'] ?? 0) . "\n";
            $csvContent .= "\n";
        }
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        return response($csvContent, 200, $headers);
    }
    
    /**
     * Export report to PDF
     */
    private function exportReportToPDF($data, $reportType, $startDate, $endDate)
    {
        // Check if DomPDF is installed
        if (!class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
            return response()->json([
                'success' => false,
                'message' => 'PDF generation requires DomPDF package. Run: composer require barryvdh/laravel-dompdf'
            ], 500);
        }
        
        $filename = "security-report-{$reportType}-" . Carbon::now()->format('Y-m-d-H-i-s') . '.pdf';
        
        // Create HTML for PDF
        $html = $this->generatePDFHtml($data, $reportType, $startDate, $endDate);
        
        try {
            $pdf = Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'portrait');
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating PDF: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Generate HTML for PDF
     */
    private function generatePDFHtml($data, $reportType, $startDate, $endDate)
    {
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Security Report - ' . ucfirst($reportType) . '</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; }
                h1 { color: #333; border-bottom: 2px solid #333; padding-bottom: 10px; }
                h2 { color: #555; margin-top: 30px; }
                .header { margin-bottom: 30px; }
                .header p { margin: 5px 0; color: #666; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
                th { background-color: #f5f5f5; font-weight: bold; }
                .footer { margin-top: 50px; color: #888; font-size: 12px; }
                .metric-value { font-weight: bold; color: #333; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Security Report - ' . ucfirst($reportType) . '</h1>
                <p><strong>Generated:</strong> ' . Carbon::now()->format('Y-m-d H:i:s') . '</p>
                <p><strong>Period:</strong> ' . $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d') . '</p>
                <p><strong>Generated by:</strong> ' . auth()->user()->name . '</p>
            </div>';
        
        if ($reportType === 'security' || $reportType === 'comprehensive') {
            if (isset($data['security_metrics'])) {
                $html .= '<h2>Security Metrics</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Metric</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>';
                
                foreach ($data['security_metrics'] as $key => $value) {
                    $html .= '<tr>
                        <td>' . ucfirst(str_replace('_', ' ', $key)) . '</td>
                        <td class="metric-value">' . $value . '</td>
                    </tr>';
                }
                
                $html .= '</tbody>
                </table>';
            }
        }
        
        if ($reportType === 'threats' || $reportType === 'comprehensive') {
            if (isset($data['threat_analysis'])) {
                $html .= '<h2>Threat Analysis</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Count</th>
                        </tr>
                    </thead>
                    <tbody>';
                
                $threatData = [
                    'Suspicious Attempts' => $data['threat_analysis']['suspicious_attempts'] ?? 0,
                    'Failed Logins' => $data['threat_analysis']['failed_logins'] ?? 0,
                    'High Risk Activities' => $data['threat_analysis']['high_risk_activities'] ?? 0,
                ];
                
                foreach ($threatData as $category => $count) {
                    $html .= '<tr>
                        <td>' . $category . '</td>
                        <td class="metric-value">' . $count . '</td>
                    </tr>';
                }
                
                $html .= '</tbody>
                </table>';
            }
        }
        
        $html .= '<div class="footer">
                <p>This is an automated security report generated by the Security Operations Center.</p>
                <p>Report ID: ' . Carbon::now()->format('YmdHis') . '</p>
                <p>Confidential - For authorized personnel only</p>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    /**
     * Update threat database
     */
    public function updateThreatDatabase(Request $request)
    {
        $request->validate([
            'threat_type' => 'required|in:ip,pattern,behavior,malware',
            'data' => 'required|array',
            'action' => 'required|in:add,remove,update'
        ]);
        
        $threatType = $request->threat_type;
        $data = $request->data;
        $action = $request->action;
        
        // Store in threat database
        $threat = ThreatDatabase::create([
            'threat_type' => $threatType,
            'threat_data' => $data,
            'action' => $action,
            'added_by' => auth()->id(),
            'is_active' => true
        ]);
        
        // Apply the threat update
        $this->applyThreatUpdate($threatType, $data, $action);
        
        return response()->json([
            'success' => true,
            'message' => 'Threat database updated successfully.',
            'threat' => $threat
        ]);
    }
    
    /**
     * Get blocked IPs
     */
    public function getBlockedIps()
    {
        $blockedIps = BlockedIp::with('blocker')
            ->where('is_active', true)
            ->orderBy('blocked_at', 'desc')
            ->get()
            ->map(function($ip) {
                return [
                    'id' => $ip->id,
                    'ip_address' => $ip->ip_address,
                    'reason' => $ip->reason,
                    'blocked_at' => $ip->blocked_at->format('Y-m-d H:i:s'),
                    'unblocks_at' => $ip->unblocks_at->format('Y-m-d H:i:s'),
                    'blocked_by' => $ip->blocker ? $ip->blocker->name : 'System'
                ];
            });
            
        return response()->json([
            'success' => true,
            'blocked_ips' => $blockedIps
        ]);
    }
    
    /**
     * Get locked users
     */
    public function getLockedUsers()
    {
        $lockedUsers = User::where('is_locked', true)
            ->orderBy('locked_at', 'desc')
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'locked_at' => $user->locked_at ? $user->locked_at->format('Y-m-d H:i:s') : null,
                    'lock_reason' => $user->lock_reason,
                    'unlocks_at' => $user->unlocks_at ? $user->unlocks_at->format('Y-m-d H:i:s') : null
                ];
            });
            
        return response()->json([
            'success' => true,
            'locked_users' => $lockedUsers
        ]);
    }
    
    /**
     * Get system lock status
     */
    public function getSystemLockStatus()
    {
        $systemLock = SystemLock::where('is_active', true)->first();
        
        return response()->json([
            'success' => true,
            'is_locked' => !empty($systemLock),
            'lock' => $systemLock ? [
                'id' => $systemLock->id,
                'reason' => $systemLock->reason,
                'locked_at' => $systemLock->locked_at->format('Y-m-d H:i:s'),
                'unlocks_at' => $systemLock->unlocks_at->format('Y-m-d H:i:s'),
                'locked_by' => $systemLock->locked_by
            ] : null
        ]);
    }
    
    private function prepareReportData($startDate, $endDate, $reportType)
    {
        $data = [
            'period' => [
                'start' => $startDate->format('Y-m-d H:i:s'),
                'end' => $endDate->format('Y-m-d H:i:s')
            ],
            'generated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'generated_by' => auth()->user()->name
        ];
        
        switch ($reportType) {
            case 'security':
                $data['security_metrics'] = $this->getSecurityMetrics($startDate, $endDate);
                break;
                
            case 'threats':
                $data['threat_analysis'] = $this->getThreatAnalysis($startDate, $endDate);
                break;
                
            case 'users':
                $data['user_activity'] = $this->getUserActivityReport($startDate, $endDate);
                break;
                
            case 'comprehensive':
                $data['security_metrics'] = $this->getSecurityMetrics($startDate, $endDate);
                $data['threat_analysis'] = $this->getThreatAnalysis($startDate, $endDate);
                $data['user_activity'] = $this->getUserActivityReport($startDate, $endDate);
                break;
        }
        
        return $data;
    }
    
    private function getSecurityMetrics($startDate, $endDate)
    {
        return [
            'total_logins' => LoginAttempt::whereBetween('created_at', [$startDate, $endDate])->count(),
            'successful_logins' => LoginAttempt::whereBetween('created_at', [$startDate, $endDate])
                ->where('is_successful', true)->count(),
            'failed_logins' => LoginAttempt::whereBetween('created_at', [$startDate, $endDate])
                ->where('is_successful', false)->count(),
            'suspicious_logins' => LoginAttempt::whereBetween('created_at', [$startDate, $endDate])
                ->where('is_suspicious', true)->count(),
            'blocked_ips' => BlockedIp::whereBetween('blocked_at', [$startDate, $endDate])->count(),
            'locked_accounts' => User::whereBetween('locked_at', [$startDate, $endDate])->where('is_locked', true)->count(),
            'pending_reviews' => SuspiciousActivity::where('status', 'pending')->count()
        ];
    }
    
    private function getThreatAnalysis($startDate, $endDate)
    {
        return [
            'suspicious_attempts' => LoginAttempt::whereBetween('created_at', [$startDate, $endDate])
                ->where('is_suspicious', true)->count(),
            'failed_logins' => LoginAttempt::whereBetween('created_at', [$startDate, $endDate])
                ->where('is_successful', false)->count(),
            'high_risk_activities' => SuspiciousActivity::whereBetween('created_at', [$startDate, $endDate])
                ->where('risk_score', '>=', 0.8)->count()
        ];
    }
    
    private function getUserActivityReport($startDate, $endDate)
    {
        return [
            'total_users' => User::count(),
            'active_users' => LoginAttempt::whereBetween('created_at', [$startDate, $endDate])
                ->distinct('user_id')->count('user_id'),
            'locked_users' => User::where('is_locked', true)->count(),
            'new_users' => User::whereBetween('created_at', [$startDate, $endDate])->count()
        ];
    }
    
    private function applyThreatUpdate($threatType, $data, $action)
    {
        // Implement threat database updates based on type
        // This is where you'd integrate with your AI/ML models
        // For now, just log the update
        \Log::info("Threat database updated", [
            'type' => $threatType,
            'action' => $action,
            'data' => $data,
            'user_id' => auth()->id()
        ]);
    }
}