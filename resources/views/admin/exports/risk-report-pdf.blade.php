<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>AI-Powered Login Risk Assessment Report</title>
    <style>
        /* Modern Design System */
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --secondary: #7209b7;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            --dark: #1f2937;
            --light: #f9fafb;
            --gray: #6b7280;
            --border: #e5e7eb;
            --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', 'Segoe UI', Roboto, -apple-system, sans-serif;
            line-height: 1.6;
            color: var(--dark);
            background: white;
            margin: 0;
            padding: 0;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px;
        }
        
        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 50px;
            padding-bottom: 25px;
            border-bottom: 3px solid var(--primary);
        }
        
        .header-left {
            flex: 1;
        }
        
        .report-title {
            margin: 0 0 10px 0;
            font-size: 32px;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
        }
        
        .report-subtitle {
            color: var(--gray);
            font-size: 16px;
            margin: 0;
            font-weight: 400;
        }
        
        .header-right {
            text-align: right;
            padding-left: 30px;
        }
        
        .report-meta {
            background: var(--light);
            border-radius: 12px;
            padding: 20px;
            box-shadow: var(--shadow);
        }
        
        .meta-item {
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .meta-label {
            color: var(--gray);
            font-weight: 500;
        }
        
        .meta-value {
            color: var(--dark);
            font-weight: 600;
        }
        
        .report-id {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
            margin-top: 10px;
        }
        
        /* Summary Cards */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .summary-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .summary-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
        }
        
        .card-1::before { background: linear-gradient(90deg, #3b82f6, #1d4ed8); }
        .card-2::before { background: linear-gradient(90deg, #10b981, #059669); }
        .card-3::before { background: linear-gradient(90deg, #f59e0b, #d97706); }
        .card-4::before { background: linear-gradient(90deg, #ef4444, #dc2626); }
        
        .card-icon {
            font-size: 24px;
            margin-bottom: 15px;
        }
        
        .card-title {
            font-size: 14px;
            color: var(--gray);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        
        .card-value {
            font-size: 36px;
            font-weight: 700;
            margin: 0;
            color: var(--dark);
        }
        
        .card-subtext {
            font-size: 13px;
            color: var(--gray);
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        /* Section Headers */
        .section-header {
            margin: 40px 0 25px 0;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--border);
            position: relative;
        }
        
        .section-header::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 60px;
            height: 2px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
        }
        
        .section-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--dark);
            margin: 0;
        }
        
        .section-subtitle {
            font-size: 14px;
            color: var(--gray);
            margin: 5px 0 0 0;
        }
        
        /* Tables */
        .modern-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 20px 0;
            font-size: 14px;
        }
        
        .modern-table thead th {
            background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
            padding: 16px;
            text-align: left;
            font-weight: 600;
            color: var(--dark);
            border-bottom: 2px solid var(--border);
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .modern-table tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background-color 0.2s;
        }
        
        .modern-table tbody tr:nth-child(even) {
            background-color: #fafafa;
        }
        
        .modern-table tbody tr:hover {
            background-color: #f0f9ff;
        }
        
        .modern-table tbody td {
            padding: 14px 16px;
            color: var(--dark);
        }
        
        /* Risk Badges */
        .risk-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .risk-critical {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }
        
        .risk-high {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }
        
        .risk-medium {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            color: #1f2937;
        }
        
        .risk-low {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }
        
        /* Status Indicators */
        .status-indicator {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        
        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }
        
        .dot-success { background-color: var(--success); }
        .dot-warning { background-color: var(--warning); }
        .dot-danger { background-color: var(--danger); }
        .dot-info { background-color: var(--info); }
        
        /* IP Address Styling */
        .ip-address {
            font-family: 'Courier New', monospace;
            background: #f3f4f6;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 13px;
        }
        
        /* Recommendations */
        .recommendations {
            margin-top: 50px;
            background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
            border-radius: 16px;
            padding: 30px;
            border-left: 4px solid var(--primary);
        }
        
        .recommendations-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .recommendation-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 20px;
            padding: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
        }
        
        .priority-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            min-width: 60px;
            text-align: center;
        }
        
        .priority-high { background: var(--danger); color: white; }
        .priority-medium { background: var(--warning); color: white; }
        .priority-low { background: var(--success); color: white; }
        
        .recommendation-content h4 {
            margin: 0 0 8px 0;
            font-size: 16px;
            font-weight: 600;
            color: var(--dark);
        }
        
        .recommendation-content p {
            margin: 0;
            font-size: 14px;
            color: var(--gray);
            line-height: 1.5;
        }
        
        /* Footer */
        .footer {
            margin-top: 60px;
            padding-top: 20px;
            border-top: 2px solid var(--border);
            text-align: center;
            font-size: 12px;
            color: var(--gray);
        }
        
        .footer p {
            margin: 5px 0;
        }
        
        .watermark {
            position: fixed;
            bottom: 20px;
            right: 20px;
            opacity: 0.1;
            font-size: 48px;
            font-weight: 900;
            color: var(--primary);
            transform: rotate(-15deg);
            pointer-events: none;
            z-index: -1;
        }
        
        /* Print Optimizations */
        @media print {
            .container { padding: 20px; }
            .summary-grid { gap: 15px; }
            .summary-card { padding: 20px; }
            .modern-table { font-size: 12px; }
            .watermark { display: none; }
        }
        
        /* Utility Classes */
        .text-gradient {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .mb-4 { margin-bottom: 1rem; }
        .mt-4 { margin-top: 1rem; }
        .mb-8 { margin-bottom: 2rem; }
        .mt-8 { margin-top: 2rem; }
    </style>
</head>
<body>
    <div class="watermark">SECURE</div>
    
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <h1 class="report-title">AI-Powered Login Risk Assessment</h1>
                <p class="report-subtitle">Comprehensive Security Intelligence Report</p>
            </div>
            <div class="header-right">
                <div class="report-meta">
                    <div class="meta-item">
                        <span class="meta-label">Generated:</span>
                        <span class="meta-value">{{ \Carbon\Carbon::now()->format('F d, Y H:i:s') }}</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Period:</span>
                        <span class="meta-value">{{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Duration:</span>
                        <span class="meta-value">{{ \Carbon\Carbon::parse($endDate)->diffInDays(\Carbon\Carbon::parse($startDate)) + 1 }} days</span>
                    </div>
                    <div class="report-id">RISK-{{ \Carbon\Carbon::now()->format('Ymd-His') }}</div>
                </div>
            </div>
        </div>

        <!-- Executive Summary -->
        <div class="section-header">
            <h2 class="section-title">Executive Summary</h2>
            <p class="section-subtitle">Key metrics and risk indicators for the selected period</p>
        </div>
        
        <div class="summary-grid">
            <div class="summary-card card-1">
                <div class="card-icon">📊</div>
                <div class="card-title">Total Login Attempts</div>
                <div class="card-value">{{ number_format($totalAttempts) }}</div>
                <div class="card-subtext">
                    <span class="status-dot {{ $totalAttempts > 1000 ? 'dot-info' : 'dot-success' }}"></span>
                    {{ $totalAttempts > 1000 ? 'High Volume' : 'Normal Activity' }}
                </div>
            </div>
            
            <div class="summary-card card-2">
                <div class="card-icon">✅</div>
                <div class="card-title">Success Rate</div>
                <div class="card-value">{{ number_format($successRate, 1) }}%</div>
                <div class="card-subtext">
                    <span class="status-dot {{ $successRate > 80 ? 'dot-success' : ($successRate > 60 ? 'dot-warning' : 'dot-danger') }}"></span>
                    {{ $successRate > 80 ? 'Excellent' : ($successRate > 60 ? 'Good' : 'Needs Attention') }}
                </div>
            </div>
            
            <div class="summary-card card-3">
                <div class="card-icon">⚠️</div>
                <div class="card-title">Suspicious Rate</div>
                <div class="card-value">{{ number_format($suspiciousRate, 1) }}%</div>
                <div class="card-subtext">
                    <span class="status-dot {{ $suspiciousRate > 10 ? 'dot-danger' : ($suspiciousRate > 5 ? 'dot-warning' : 'dot-success') }}"></span>
                    {{ $suspiciousRate > 10 ? 'High Risk' : ($suspiciousRate > 5 ? 'Monitor' : 'Low Risk') }}
                </div>
            </div>
            
            <div class="summary-card card-4">
                <div class="card-icon">🚨</div>
                <div class="card-title">Suspicious Attempts</div>
                <div class="card-value">{{ number_format($suspiciousAttempts) }}</div>
                <div class="card-subtext">
                    <span class="status-dot {{ $suspiciousAttempts > 50 ? 'dot-danger' : ($suspiciousAttempts > 20 ? 'dot-warning' : 'dot-success') }}"></span>
                    {{ $suspiciousAttempts > 50 ? 'Critical' : ($suspiciousAttempts > 20 ? 'Elevated' : 'Normal') }}
                </div>
            </div>
        </div>

        <!-- Top Risky IPs -->
        <div class="section-header">
            <h2 class="section-title">Top Risky IP Addresses</h2>
            <p class="section-subtitle">IP addresses with highest average risk scores</p>
        </div>
        
        <table class="modern-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>IP Address</th>
                    <th>Attempts</th>
                    <th>Avg Risk Score</th>
                    <th>Risk Level</th>
                </tr>
            </thead>
            <tbody>
                @php $rank = 1; @endphp
                @foreach($riskyIPs as $ip)
                @php
                    $riskClass = $ip->avg_risk_score > 0.7 ? 'risk-critical' : 
                                ($ip->avg_risk_score > 0.5 ? 'risk-high' : 
                                ($ip->avg_risk_score > 0.3 ? 'risk-medium' : 'risk-low'));
                    $riskLevel = $ip->avg_risk_score > 0.7 ? 'CRITICAL' : 
                                ($ip->avg_risk_score > 0.5 ? 'HIGH' : 
                                ($ip->avg_risk_score > 0.3 ? 'MEDIUM' : 'LOW'));
                @endphp
                <tr>
                    <td><strong>#{{ $rank++ }}</strong></td>
                    <td><span class="ip-address">{{ $ip->ip_address }}</span></td>
                    <td>{{ $ip->attempt_count }}</td>
                    <td><strong>{{ number_format($ip->avg_risk_score * 100, 1) }}%</strong></td>
                    <td><span class="risk-badge {{ $riskClass }}">{{ $riskLevel }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Risk by Country -->
        <div class="section-header">
            <h2 class="section-title">Risk Analysis by Country</h2>
            <p class="section-subtitle">Geographic distribution of login attempts and risks</p>
        </div>
        
        <table class="modern-table">
            <thead>
                <tr>
                    <th>Country</th>
                    <th>Total Attempts</th>
                    <th>Suspicious</th>
                    <th>Suspicious %</th>
                    <th>Avg Risk Score</th>
                    <th>Threat Level</th>
                </tr>
            </thead>
            <tbody>
                @foreach($countryRisk as $country)
                @php
                    $suspiciousRate = $country->total_attempts > 0 ? 
                        ($country->suspicious_attempts / $country->total_attempts) * 100 : 0;
                    $riskClass = $country->avg_risk_score > 0.7 ? 'risk-critical' : 
                                ($country->avg_risk_score > 0.5 ? 'risk-high' : 
                                ($country->avg_risk_score > 0.3 ? 'risk-medium' : 'risk-low'));
                    $riskLevel = $country->avg_risk_score > 0.7 ? 'CRITICAL' : 
                                ($country->avg_risk_score > 0.5 ? 'HIGH' : 
                                ($country->avg_risk_score > 0.3 ? 'MEDIUM' : 'LOW'));
                @endphp
                <tr>
                    <td><strong>{{ $country->country ?? 'Unknown' }}</strong></td>
                    <td>{{ number_format($country->total_attempts) }}</td>
                    <td>{{ number_format($country->suspicious_attempts) }}</td>
                    <td>
                        <div class="status-indicator">
                            <span class="status-dot {{ $suspiciousRate > 10 ? 'dot-danger' : ($suspiciousRate > 5 ? 'dot-warning' : 'dot-success') }}"></span>
                            {{ number_format($suspiciousRate, 1) }}%
                        </div>
                    </td>
                    <td>{{ number_format($country->avg_risk_score * 100, 1) }}%</td>
                    <td><span class="risk-badge {{ $riskClass }}">{{ $riskLevel }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Daily Trend -->
        @if($dailyTrend->count() > 0)
        <div class="section-header">
            <h2 class="section-title">Daily Login Activity Trend</h2>
            <p class="section-subtitle">Pattern analysis over time</p>
        </div>
        
        <table class="modern-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Total Attempts</th>
                    <th>Suspicious</th>
                    <th>Suspicious %</th>
                    <th>Avg Risk Score</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dailyTrend as $day)
                @php
                    $suspiciousRate = $day->total_attempts > 0 ? 
                        ($day->suspicious_attempts / $day->total_attempts) * 100 : 0;
                @endphp
                <tr>
                    <td><strong>{{ \Carbon\Carbon::parse($day->date)->format('M d, Y') }}</strong></td>
                    <td>{{ $day->total_attempts }}</td>
                    <td>{{ $day->suspicious_attempts }}</td>
                    <td>
                        <div class="status-indicator">
                            <span class="status-dot {{ $suspiciousRate > 10 ? 'dot-danger' : ($suspiciousRate > 5 ? 'dot-warning' : 'dot-success') }}"></span>
                            {{ number_format($suspiciousRate, 1) }}%
                        </div>
                    </td>
                    <td>{{ number_format($day->avg_risk * 100, 1) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <!-- Security Recommendations -->
        <div class="recommendations">
            <h3 class="recommendations-title">🔒 Security Recommendations</h3>
            
            @if($suspiciousRate > 10)
            <div class="recommendation-item">
                <div class="priority-badge priority-high">HIGH</div>
                <div class="recommendation-content">
                    <h4>Implement Rate Limiting</h4>
                    <p>Configure rate limiting (e.g., 5 attempts per minute per IP) to prevent brute force attacks from suspicious IPs.</p>
                </div>
            </div>
            @endif
            
            @if($riskyIPs->count() > 0)
            <div class="recommendation-item">
                <div class="priority-badge priority-medium">MEDIUM</div>
                <div class="recommendation-content">
                    <h4>Review Top Risky IPs</h4>
                    <p>Investigate and consider blocking IP addresses with consistently high risk scores (>70%).</p>
                </div>
            </div>
            @endif
            
            @if($countryRisk->count() > 0 && $countryRisk->first()->avg_risk_score > 0.5)
            <div class="recommendation-item">
                <div class="priority-badge priority-medium">MEDIUM</div>
                <div class="recommendation-content">
                    <h4>Geo-Fencing Implementation</h4>
                    <p>Consider implementing geographic restrictions for high-risk countries showing abnormal activity patterns.</p>
                </div>
            </div>
            @endif
            
            <div class="recommendation-item">
                <div class="priority-badge priority-low">LOW</div>
                <div class="recommendation-content">
                    <h4>Regular Security Audit</h4>
                    <p>Schedule bi-weekly security reviews to analyze patterns and update threat detection rules.</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>AI-Powered Login Detection System v2.0</strong></p>
            <p>This report contains sensitive security information. Handle with appropriate confidentiality measures.</p>
            <p>Report generated automatically • Valid for 30 days from generation date</p>
            <p style="font-size: 10px; color: #9ca3af; margin-top: 10px;">
                Document ID: RISK-{{ \Carbon\Carbon::now()->format('Ymd-His') }} • Page 1 of 1
            </p>
        </div>
    </div>
</body>
</html>