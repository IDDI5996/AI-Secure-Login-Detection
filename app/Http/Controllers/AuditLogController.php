<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LoginAttempt;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuditLogController extends Controller
{
    /**
     * Display audit log for admin
     */
    public function index(Request $request)
    {
        // Check if user is admin
        if (!Auth::check() || (!Auth::user()->is_admin && !Auth::user()->is_super_admin)) {
            abort(403, 'Unauthorized access.');
        }

        // Get filter parameters
        $status = $request->input('status');
        $search = $request->input('search');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        
        // Start query
        $query = LoginAttempt::with(['user'])
            ->orderBy('attempted_at', 'desc');
        
        // Apply filters
        if ($status) {
            $query->where('is_successful', $status === 'success');
        }
        
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhere('ip_address', 'like', "%{$search}%")
                ->orWhere('user_agent', 'like', "%{$search}%");
            });
        }
        
        if ($dateFrom) {
            $query->whereDate('attempted_at', '>=', $dateFrom);
        }
        
        if ($dateTo) {
            $query->whereDate('attempted_at', '<=', $dateTo);
        }
        
        // Get paginated results
        $loginAttempts = $query->paginate(25);
        
        // Statistics
        $stats = [
            'total' => LoginAttempt::count(),
            'successful' => LoginAttempt::where('is_successful', true)->count(),
            'failed' => LoginAttempt::where('is_successful', false)->count(),
            'suspicious' => LoginAttempt::where('is_suspicious', true)->count(),
            'today' => LoginAttempt::whereDate('attempted_at', today())->count(),
            'unique_users' => LoginAttempt::distinct('user_id')->count('user_id'),
        ];
        
        return view('admin.audit-log', compact('loginAttempts', 'stats'));
    }

    /**
     * Display specific login attempt details
     */
    public function show($id)
    {
        // Check if user is admin
        if (!Auth::check() || (!Auth::user()->is_admin && !Auth::user()->is_super_admin)) {
            abort(403, 'Unauthorized access.');
        }

        $loginAttempt = LoginAttempt::with(['user', 'verificationAttempt'])->findOrFail($id);
        
        return view('admin.login-attempt-detail', compact('loginAttempt'));
    }

    /**
 * Export audit log as CSV
 */
public function export(Request $request)
{
    // Check if user is admin
    if (!Auth::check() || (!Auth::user()->is_admin && !Auth::user()->is_super_admin)) {
        abort(403, 'Unauthorized access.');
    }

    // Get filter parameters (same as index method)
    $status = $request->input('status');
    $search = $request->input('search');
    $dateFrom = $request->input('date_from');
    $dateTo = $request->input('date_to');
    $format = $request->input('format', 'csv');
    
    // Start query
    $query = LoginAttempt::with(['user', 'verificationAttempt'])
        ->orderBy('attempted_at', 'desc');
    
    // Apply filters
    if ($status) {
        $query->where('is_successful', $status === 'success');
    }
    
    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->whereHas('user', function ($userQuery) use ($search) {
                $userQuery->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
            })
            ->orWhere('ip_address', 'like', "%{$search}%")
            ->orWhere('user_agent', 'like', "%{$search}%");
        });
    }
    
    if ($dateFrom) {
        $query->whereDate('attempted_at', '>=', $dateFrom);
    }
    
    if ($dateTo) {
        $query->whereDate('attempted_at', '<=', $dateTo);
    }
    
    // Get all results (no pagination for export)
    $loginAttempts = $query->get();
    
    if ($format === 'pdf') {
        return $this->exportPdf($loginAttempts, $dateFrom, $dateTo);
    } else {
        return $this->exportCsv($loginAttempts);
    }
}

/**
 * Export data as CSV
 */
private function exportCsv($loginAttempts)
{
    $filename = 'audit-log-' . date('Y-m-d-H-i-s') . '.csv';
    
    $headers = [
        'Content-Type' => 'text/csv; charset=utf-8',
        'Content-Disposition' => "attachment; filename=\"$filename\"",
        'Pragma' => 'no-cache',
        'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
        'Expires' => '0'
    ];
    
    $callback = function() use ($loginAttempts) {
        $file = fopen('php://output', 'w');
        
        // Add UTF-8 BOM for Excel compatibility
        fwrite($file, "\xEF\xBB\xBF");
        
        // CSV headers
        $headers = [
            'ID',
            'User Name',
            'User Email',
            'IP Address',
            'City',
            'Country',
            'Device Type',
            'Browser',
            'Status',
            'Suspicious',
            'Risk Score (%)',
            'Verification Method',
            'Verification Status',
            'Attempted At',
            'User Agent'
        ];
        
        fputcsv($file, $headers);
        
        // CSV rows
        foreach ($loginAttempts as $attempt) {
            $row = [
                $attempt->id,
                $attempt->user->name ?? 'Unknown',
                $attempt->user->email ?? 'N/A',
                $attempt->ip_address,
                $attempt->city ?? 'N/A',
                $attempt->country ?? 'N/A',
                $attempt->device_type ?? 'Unknown',
                $attempt->browser ?? 'Unknown',
                $attempt->is_successful ? 'Successful' : 'Failed',
                $attempt->is_suspicious ? 'Yes' : 'No',
                number_format($attempt->risk_score * 100, 2),
                $attempt->verificationAttempt->verification_method ?? 'N/A',
                $attempt->verificationAttempt ? ($attempt->verificationAttempt->is_successful ? 'Verified' : 'Failed') : 'N/A',
                $attempt->attempted_at->format('Y-m-d H:i:s'),
                $attempt->user_agent
            ];
            
            fputcsv($file, $row);
        }
        
        fclose($file);
    };
    
    return response()->stream($callback, 200, $headers);
}

/**
 * Export data as PDF
 */
private function exportPdf($loginAttempts, $dateFrom, $dateTo)
{
    $filename = 'audit-log-' . date('Y-m-d-H-i-s') . '.pdf';
    
    $html = view('admin.exports.audit-log-pdf', [
        'loginAttempts' => $loginAttempts,
        'dateFrom' => $dateFrom,
        'dateTo' => $dateTo,
        'generatedAt' => now()->format('F d, Y H:i:s')
    ])->render();
    
    // Simple PDF generation using DomPDF (if installed) or return HTML
    if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
        return $pdf->download($filename);
    } else {
        // If DomPDF is not installed, offer to download as HTML
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', "attachment; filename=\"audit-log-report.html\"");
    }
}

    /**
     * Get user login history for profile page
     */
    public function userHistory(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $loginAttempts = LoginAttempt::where('user_id', Auth::id())
            ->orderBy('attempted_at', 'desc')
            ->paginate(20);
            
        return view('profile.login-history', compact('loginAttempts'));
    }

    /**
     * Get user login history for API
     */
    public function getUserLoginHistory(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $loginAttempts = LoginAttempt::where('user_id', Auth::id())
            ->orderBy('attempted_at', 'desc')
            ->paginate($request->get('per_page', 20));
            
        return response()->json($loginAttempts);
    }
}