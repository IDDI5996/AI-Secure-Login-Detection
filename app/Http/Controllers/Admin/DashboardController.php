<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LoginAttempt;
use App\Models\SuspiciousActivity;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Barryvdh\DomPDF\Facade\Pdf;
use League\Csv\Writer;
use SplTempFileObject;

class DashboardController extends Controller
{
    
    public function dashboard(Request $request)
    {
        $timeRange = $request->get('range', 'today');
        
        return response()->json([
            'stats' => $this->getDashboardStats($timeRange),
            'recent_suspicious_activities' => $this->getRecentSuspiciousActivities(),
            'login_trend' => $this->getLoginTrend($timeRange),
            'top_risky_users' => $this->getTopRiskyUsers(),
            'geographic_data' => $this->getGeographicData($timeRange)
        ]);
    }
    
    public function suspiciousActivities(Request $request)
{
    $query = SuspiciousActivity::with(['user', 'reviewer'])
        ->latest();
        
    if ($request->has('status')) {
        $query->where('status', $request->status);
    }
    
    if ($request->has('type')) {
        $query->where('activity_type', $request->type);
    }
    
    if ($request->has('date_from')) {
        $query->where('created_at', '>=', $request->date_from);
    }
    
    if ($request->has('date_to')) {
        $query->where('created_at', '<=', $request->date_to);
    }
    
    $activities = $query->paginate($request->get('per_page', 20));
    
    // Add statistics calculation
    $stats = [
        'total' => SuspiciousActivity::count(),
        'pending' => SuspiciousActivity::where('status', 'pending')->count(),
        'reviewed' => SuspiciousActivity::where('status', 'reviewed')->count(),
        'resolved' => SuspiciousActivity::where('status', 'resolved')->count(),
        'false_positive' => SuspiciousActivity::where('status', 'false_positive')->count(),
        'high_risk' => SuspiciousActivity::where('risk_score', '>=', 0.8)->count(),
        'avg_risk_score' => SuspiciousActivity::avg('risk_score') * 100,
    ];
    
    // Pass both activities and stats to the view
    return view('admin.suspicious-activities', compact('activities', 'stats'));
}
    
    /**
    * Update suspicious activity status (for web routes)
    */
    public function updateActivity(Request $request, $id)
    {
        // Check if user is admin
        if (!auth()->user()->is_admin) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }
    
        $request->validate([
            'status' => 'required|in:pending,reviewed,resolved,false_positive',
            'review_notes' => 'nullable|string|max:1000'
        ]);
    
        $activity = \App\Models\SuspiciousActivity::find($id);
    
        if (!$activity) {
            return redirect()->back()->with('error', 'Activity not found.');
        }
    
        $activity->update([
            'status' => $request->status,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_notes' => $request->review_notes
        ]);
    
        // If marked as false positive, you can adjust AI model here
        if ($request->status === 'false_positive') {
            // Call method to adjust AI model
            // $this->adjustAiModel($activity);
        }
    
        return redirect()->back()->with('success', 'Activity status updated successfully!');
    }
    
    public function userLoginHistory(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        
        $logins = LoginAttempt::where('user_id', $userId)
            ->with('verificationAttempt')
            ->orderBy('attempted_at', 'desc')
            ->paginate($request->get('per_page', 20));
            
        return response()->json([
            'user' => $user,
            'behavior_profile' => $user->behaviorProfile,
            'login_history' => $logins
        ]);
    }
    
    public function getRiskReport(Request $request)
    {
        $reportData = [
            'overall_risk_score' => $this->calculateOverallRiskScore(),
            'risk_by_country' => $this->getRiskByCountry(),
            'risk_by_device' => $this->getRiskByDevice(),
            'risk_by_time' => $this->getRiskByTime(),
            'false_positive_rate' => $this->calculateFalsePositiveRate(),
            'detection_efficiency' => $this->calculateDetectionEfficiency()
        ];
        
        return response()->json($reportData);
    }
    
    private function getDashboardStats($range): array
    {
        $dateRange = $this->getDateRange($range);
        
        return [
            'total_logins' => LoginAttempt::whereBetween('attempted_at', $dateRange)->count(),
            'suspicious_logins' => LoginAttempt::whereBetween('attempted_at', $dateRange)
                ->where('is_suspicious', true)
                ->count(),
            'successful_logins' => LoginAttempt::whereBetween('attempted_at', $dateRange)
                ->where('is_successful', true)
                ->count(),
            'failed_logins' => LoginAttempt::whereBetween('attempted_at', $dateRange)
                ->where('is_successful', false)
                ->count(),
            'pending_reviews' => SuspiciousActivity::where('status', 'pending')->count(),
            'unique_users' => LoginAttempt::whereBetween('attempted_at', $dateRange)
                ->distinct('user_id')
                ->count('user_id'),
            'avg_risk_score' => LoginAttempt::whereBetween('attempted_at', $dateRange)
                ->avg('risk_score') ?? 0
        ];
    }
    
    private function getRecentSuspiciousActivities($limit = 10)
    {
        return SuspiciousActivity::with('user')
            ->where('status', 'pending')
            ->orderBy('risk_score', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'user' => $activity->user->name,
                    'type' => $activity->activity_type,
                    'risk_score' => $activity->risk_score,
                    'reasons' => $activity->detection_reasons,
                    'created_at' => $activity->created_at->diffForHumans()
                ];
            });
    }
    
    private function getLoginTrend($range)
    {
        $dateRange = $this->getDateRange($range);
        $interval = $range === 'today' ? 'hour' : 'day';
        
        return LoginAttempt::whereBetween('attempted_at', $dateRange)
            ->select(
                DB::raw("DATE_FORMAT(attempted_at, '%Y-%m-%d %H:00:00') as time"),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN is_suspicious = 1 THEN 1 ELSE 0 END) as suspicious')
            )
            ->groupBy('time')
            ->orderBy('time')
            ->get();
    }
    
    /**
    * Get activity details for modal
    */
    public function getActivityDetails($id)
    {
        try {
            $activity = \App\Models\SuspiciousActivity::with(['user', 'reviewer'])
                ->find($id);
        
            if (!$activity) {
                return response()->json([
                    'success' => false,
                    'error' => 'Activity not found'
                ],  404);
            }
        
            return response()->json([
                'success' => true,
                'data' => $activity
            ]);
        
    } catch (\Exception $e) {
        \Log::error('Error fetching activity details: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'error' => 'Server error: ' . $e->getMessage()
        ], 500);
      }
    }
    
    public function riskReport(Request $request)
{
    // Check if export is requested
        if ($request->has('export')) {
            return $this->handleExport($request);
        }
        
        // Get date range
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        
        // Get the data
        $data = $this->getRiskReportData($startDate, $endDate);
        
        return view('admin.risk-report', array_merge([
            'startDate' => $startDate,
            'endDate' => $endDate,
        ], $data));
}

protected function handleExport(Request $request)
    {
        $format = $request->input('export', 'pdf');
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        
        $data = $this->getRiskReportData($startDate, $endDate);
        $data['startDate'] = $startDate;
        $data['endDate'] = $endDate;
        $data['generatedAt'] = Carbon::now();
        
        if ($format === 'csv') {
            return $this->exportToModernCSV($data);
        }
        
        return $this->exportToModernPDF($data);
    }
    
     protected function exportToModernPDF($data)
    {
        $pdf = Pdf::loadView('admin.exports.modern-pdf-report', $data);
        
        // Set paper size and orientation
        $pdf->setPaper('A4', 'landscape');
        
        // Set options for better rendering
        $pdf->setOptions([
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'isPhpEnabled' => true,
        ]);
        
        $filename = 'AI-Login-Risk-Report-' . Carbon::now()->format('Y-m-d-H-i-s') . '.pdf';
        
        return $pdf->download($filename);
    }
    
     protected function exportToModernCSV($data)
    {
        // Create CSV writer
        $csv = Writer::createFromFileObject(new SplTempFileObject());
        
        // Add BOM for UTF-8 encoding
        $csv->setOutputBOM(Writer::BOM_UTF8);
        
        // Set delimiter (comma) and enclosure
        $csv->setDelimiter(',');
        $csv->setEnclosure('"');
        
        // Header Section
        $csv->insertOne(['AI-POWERED LOGIN DETECTION SYSTEM - RISK ASSESSMENT REPORT']);
        $csv->insertOne([]);
        $csv->insertOne(['Report Period:', Carbon::parse($data['startDate'])->format('F d, Y') . ' to ' . Carbon::parse($data['endDate'])->format('F d, Y')]);
        $csv->insertOne(['Generated:', Carbon::now()->format('F d, Y H:i:s')]);
        $csv->insertOne(['Report ID:', 'RISK-' . Carbon::now()->format('Ymd-His')]);
        $csv->insertOne([]);
        
        // Executive Summary
        $csv->insertOne(['EXECUTIVE SUMMARY']);
        $csv->insertOne(['Metric', 'Value', 'Status']);
        $csv->insertOne([
            'Total Login Attempts', 
            $data['totalAttempts'],
            $data['totalAttempts'] > 100 ? 'High Volume' : 'Normal'
        ]);
        $csv->insertOne([
            'Successful Attempts', 
            $data['successfulAttempts'],
            $data['successRate'] > 80 ? 'Excellent' : ($data['successRate'] > 60 ? 'Good' : 'Needs Attention')
        ]);
        $csv->insertOne([
            'Suspicious Attempts', 
            $data['suspiciousAttempts'],
            $data['suspiciousRate'] > 10 ? '⚠️ High Risk' : ($data['suspiciousRate'] > 5 ? '⚠️ Moderate' : '✅ Low')
        ]);
        $csv->insertOne([
            'Success Rate', 
            number_format($data['successRate'], 2) . '%',
            $this->getSuccessRateStatus($data['successRate'])
        ]);
        $csv->insertOne([
            'Suspicious Rate', 
            number_format($data['suspiciousRate'], 2) . '%',
            $this->getRiskStatus($data['suspiciousRate'])
        ]);
        $csv->insertOne([]);
        
        // Top Risky IPs
        $csv->insertOne(['TOP RISKY IP ADDRESSES']);
        $csv->insertOne(['Rank', 'IP Address', 'Attempts', 'Avg Risk Score', 'Risk Level']);
        $rank = 1;
        foreach ($data['riskyIPs'] as $ip) {
            $csv->insertOne([
                $rank++,
                $ip->ip_address,
                $ip->attempt_count,
                number_format($ip->avg_risk_score * 100, 2) . '%',
                $this->getRiskLevel($ip->avg_risk_score)
            ]);
        }
        $csv->insertOne([]);
        
        // Risk by Country
        $csv->insertOne(['RISK ANALYSIS BY COUNTRY']);
        $csv->insertOne(['Country', 'Total Attempts', 'Suspicious', 'Suspicious %', 'Avg Risk Score', 'Threat Level']);
        foreach ($data['countryRisk'] as $country) {
            $suspiciousRate = $country->total_attempts > 0 ? 
                ($country->suspicious_attempts / $country->total_attempts) * 100 : 0;
            
            $csv->insertOne([
                $country->country ?? 'Unknown',
                $country->total_attempts,
                $country->suspicious_attempts,
                number_format($suspiciousRate, 2) . '%',
                number_format($country->avg_risk_score * 100, 2) . '%',
                $this->getRiskLevel($country->avg_risk_score)
            ]);
        }
        $csv->insertOne([]);
        
        // Daily Trend Data
        $csv->insertOne(['DAILY LOGIN TREND']);
        $csv->insertOne(['Date', 'Total Attempts', 'Suspicious Attempts', 'Suspicious %', 'Avg Risk Score']);
        foreach ($data['dailyTrend'] as $day) {
            $suspiciousRate = $day->total_attempts > 0 ? 
                ($day->suspicious_attempts / $day->total_attempts) * 100 : 0;
            
            $csv->insertOne([
                Carbon::parse($day->date)->format('M d, Y'),
                $day->total_attempts,
                $day->suspicious_attempts,
                number_format($suspiciousRate, 2) . '%',
                number_format($day->avg_risk * 100, 2) . '%'
            ]);
        }
        $csv->insertOne([]);
        
        // Recommendations
        $csv->insertOne(['SECURITY RECOMMENDATIONS']);
        $csv->insertOne(['Priority', 'Recommendation', 'Impact']);
        
        $recommendations = $this->generateRecommendations($data);
        foreach ($recommendations as $rec) {
            $csv->insertOne($rec);
        }
        
        $filename = 'AI-Login-Risk-Report-' . Carbon::now()->format('Y-m-d-H-i-s') . '.csv';
        
        return response((string) $csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ]);
    }
    
    // Helper methods for status indicators
    protected function getRiskLevel($score)
    {
        if ($score > 0.7) return '🔴 CRITICAL';
        if ($score > 0.5) return '🟠 HIGH';
        if ($score > 0.3) return '🟡 MEDIUM';
        return '🟢 LOW';
    }
    
     protected function getSuccessRateStatus($rate)
    {
        if ($rate > 85) return '✅ Excellent';
        if ($rate > 70) return '👍 Good';
        if ($rate > 50) return '⚠️ Fair';
        return '🔴 Poor';
    }
    
    protected function getRiskStatus($rate)
    {
        if ($rate > 15) return '🔴 Critical';
        if ($rate > 10) return '🟠 High';
        if ($rate > 5) return '🟡 Medium';
        return '🟢 Low';
    }
    
    protected function generateRecommendations($data)
    {
        $recommendations = [];
        
        if ($data['suspiciousRate'] > 10) {
            $recommendations[] = ['HIGH', 'Implement rate limiting for suspicious IPs', 'High'];
        }
        
        if ($data['successRate'] < 60) {
            $recommendations[] = ['HIGH', 'Review authentication flow and error messages', 'Medium'];
        }
        
        if ($data['riskyIPs']->count() > 5) {
            $recommendations[] = ['MEDIUM', 'Consider IP blocking for repeated offenders', 'High'];
        }
        
        if ($data['totalAttempts'] > 500) {
            $recommendations[] = ['MEDIUM', 'Implement CAPTCHA for high-traffic periods', 'Medium'];
        }
        
        // Add default recommendation
        $recommendations[] = ['LOW', 'Regular security audit and monitoring', 'Low'];
        
        return $recommendations;
    }

protected function getRiskReportData($startDate, $endDate)
{
    // Get login attempts within date range
    $attempts = LoginAttempt::whereBetween('created_at', [
        Carbon::parse($startDate)->startOfDay(),
        Carbon::parse($endDate)->endOfDay()
    ])->get();
    
    // Calculate statistics
    $totalAttempts = $attempts->count();
    $successfulAttempts = $attempts->where('is_successful', true)->count();
    $suspiciousAttempts = $attempts->where('is_suspicious', true)->count();
    $successRate = $totalAttempts > 0 ? ($successfulAttempts / $totalAttempts) * 100 : 0;
    $suspiciousRate = $totalAttempts > 0 ? ($suspiciousAttempts / $totalAttempts) * 100 : 0;
    
    // Top risky IPs
    $riskyIPs = LoginAttempt::select('ip_address')
        ->selectRaw('COUNT(*) as attempt_count')
        ->selectRaw('AVG(risk_score) as avg_risk_score')
        ->whereBetween('created_at', [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay()
        ])
        ->groupBy('ip_address')
        ->havingRaw('AVG(risk_score) > 0.3')
        ->orderBy('avg_risk_score', 'desc')
        ->limit(10)
        ->get();
    
    // Daily trend
    $dailyTrend = LoginAttempt::selectRaw('DATE(created_at) as date')
        ->selectRaw('COUNT(*) as total_attempts')
        ->selectRaw('SUM(CASE WHEN is_suspicious = 1 THEN 1 ELSE 0 END) as suspicious_attempts')
        ->selectRaw('AVG(risk_score) as avg_risk')
        ->whereBetween('created_at', [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay()
        ])
        ->groupBy('date')
        ->orderBy('date')
        ->get();
    
    // Top countries with suspicious attempts
    $countryRisk = LoginAttempt::select('country')
        ->selectRaw('COUNT(*) as total_attempts')
        ->selectRaw('SUM(CASE WHEN is_suspicious = 1 THEN 1 ELSE 0 END) as suspicious_attempts')
        ->selectRaw('AVG(risk_score) as avg_risk_score')
        ->whereBetween('created_at', [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay()
        ])
        ->whereNotNull('country')
        ->groupBy('country')
        ->orderBy('avg_risk_score', 'desc')
        ->limit(10)
        ->get();
    
    return [
        'totalAttempts' => $totalAttempts,
        'successfulAttempts' => $successfulAttempts,
        'suspiciousAttempts' => $suspiciousAttempts,
        'successRate' => $successRate,
        'suspiciousRate' => $suspiciousRate,
        'riskyIPs' => $riskyIPs,
        'dailyTrend' => $dailyTrend,
        'countryRisk' => $countryRisk,
    ];
}

protected function exportRiskReport(Request $request)
{
    // First, install required packages:
    // composer require barryvdh/laravel-dompdf
    // composer require league/csv
    
    $format = $request->input('export', 'pdf');
    $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
    $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
    
    $data = $this->getRiskReportData($startDate, $endDate);
    $data['startDate'] = $startDate;
    $data['endDate'] = $endDate;
    
    if ($format === 'csv') {
        return $this->exportRiskReportToCsv($data);
    }
    
    return $this->exportRiskReportToPdf($data);
}

protected function exportRiskReportToPdf($data)
{
    // Load PDF facade (make sure it's installed)
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.exports.risk-report-pdf', $data);
    
    $filename = 'risk-report-' . date('Y-m-d-H-i-s') . '.pdf';
    
    return $pdf->download($filename);
}

protected function exportRiskReportToCsv($data)
{
    // Create CSV content
    $csvContent = "Risk Assessment Report\n";
    $csvContent .= "Generated on: " . date('F d, Y H:i:s') . "\n";
    $csvContent .= "Report Period: " . \Carbon\Carbon::parse($data['startDate'])->format('M d, Y') . 
                   " - " . \Carbon\Carbon::parse($data['endDate'])->format('M d, Y') . "\n\n";
    
    $csvContent .= "SUMMARY\n";
    $csvContent .= "Total Login Attempts," . $data['totalAttempts'] . "\n";
    $csvContent .= "Successful Attempts," . $data['successfulAttempts'] . "\n";
    $csvContent .= "Suspicious Attempts," . $data['suspiciousAttempts'] . "\n";
    $csvContent .= "Success Rate," . number_format($data['successRate'], 2) . "%\n";
    $csvContent .= "Suspicious Rate," . number_format($data['suspiciousRate'], 2) . "%\n\n";
    
    $csvContent .= "TOP RISKY IP ADDRESSES\n";
    $csvContent .= "IP Address,Attempt Count,Average Risk Score\n";
    foreach ($data['riskyIPs'] as $ip) {
        $csvContent .= $ip->ip_address . "," . $ip->attempt_count . "," . 
                      number_format($ip->avg_risk_score * 100, 2) . "%\n";
    }
    $csvContent .= "\n";
    
    $csvContent .= "RISK BY COUNTRY\n";
    $csvContent .= "Country,Total Attempts,Suspicious Attempts,Average Risk Score\n";
    foreach ($data['countryRisk'] as $country) {
        $csvContent .= ($country->country ?? 'Unknown') . "," . 
                      $country->total_attempts . "," . 
                      $country->suspicious_attempts . "," . 
                      number_format($country->avg_risk_score * 100, 2) . "%\n";
    }
    $csvContent .= "\n";
    
    $csvContent .= "DAILY LOGIN TREND\n";
    $csvContent .= "Date,Total Attempts,Suspicious Attempts,Average Risk\n";
    foreach ($data['dailyTrend'] as $day) {
        $csvContent .= $day->date . "," . 
                      $day->total_attempts . "," . 
                      $day->suspicious_attempts . "," . 
                      number_format($day->avg_risk * 100, 2) . "%\n";
    }
    
    $filename = 'risk-report-' . date('Y-m-d-H-i-s') . '.csv';
    
    return response($csvContent, 200, [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ]);
}
}
