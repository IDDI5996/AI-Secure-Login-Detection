<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>AI Login Risk Assessment Report</title>
    <style>
        /* Modern Design System */
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --secondary: #7209b7;
            --success: #2ecc71;
            --warning: #f39c12;
            --danger: #e74c3c;
            --info: #3498db;
            --dark: #2c3e50;
            --light: #f8f9fa;
            --gray: #95a5a6;
            --border: #e0e0e0;
        }
        
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.6;
            color: var(--dark);
            background: white;
            margin: 0;
            padding: 0;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px;
        }
        
        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid var(--primary);
            padding-bottom: 20px;
            margin-bottom: 40px;
        }
        
        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 20px;
        }
        
        .title h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            color: var(--dark);
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .title p {
            margin: 5px 0 0;
            color: var(--gray);
            font-size: 14px;
        }
        
        .report-info {
            text-align: right;
            font-size: 13px;
            color: var(--gray);
        }
        
        .report-info strong {
            color: var(--dark);
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
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--border);
            transition: transform 0.3s ease;
        }
        
        .summary-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.12);
        }
        
        .card-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .card-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 20px;
        }
        
        .icon-attempts { background: linear-gradient(135deg, #667eea, #764ba2); color: white; }
        .icon-success { background: linear-gradient(135deg, #2ecc71, #27ae60); color: white; }
        .icon-suspicious { background: linear-gradient(135deg, #e74c3c, #c0392b); color: white; }
        .icon-risk { background: linear-gradient(135deg, #f39c12, #e67e22); color: white; }
        
        .card-title {
            font-size: 14px;
            color: var(--gray);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .card-value {
            font-size: 36px;
            font-weight: 700;
            margin: 5px 0;
            color: var(--dark);
        }
        
        .card-trend {
            font-size: 13px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .trend-up { color: var(--success); }
        .trend-down { color: var(--danger); }
        
        /* Table Styles */
        .section {
            margin-bottom: 40px;
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--border);
        }
        
        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .section-title:before {
            content: '';
            width: 4px;
            height: 24px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 2px;
        }
        
        .modern-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 13px;
        }
        
        .modern-table thead th {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: var(--dark);
            border-bottom: 2px solid var(--border);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 12px;
        }
        
        .modern-table tbody tr {
            transition: background-color 0.2s ease;
        }
        
        .modern-table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .modern-table tbody td {
            padding: 15px;
            border-bottom: 1px solid var(--border);
            color: var(--dark);
        }
        
        .risk-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .risk-critical { background: linear-gradient(135deg, #ff6b6b, #ee5a52); color: white; }
        .risk-high { background: linear-gradient(135deg, #ff9f43, #ff9f1a); color: white; }
        .risk-medium { background: linear-gradient(135deg, #feca57, #ff9f43); color: white; }
        .risk-low { background: linear-gradient(135deg, #1dd1a1, #10ac84); color: white; }
        
        /* Chart Container */
        .chart-container {
            background: white;
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--border);
        }
        
        /* Recommendations */
        .recommendations {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 16px;
            padding: 30px;
            margin-top: 40px;
        }
        
        .recommendation-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 20px;
            padding: 20px;
            background: white;
            border-radius: 12px;
            border-left: 4px solid var(--primary);
        }
        
        .priority-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .priority-high { background: var(--danger); color: white; }
        .priority-medium { background: var(--warning); color: white; }
        .priority-low { background: var(--success); color: white; }
        
        /* Footer */
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid var(--border);
            text-align: center;
            font-size: 12px;
            color: var(--gray);
        }
        
        .footer p {
            margin: 5px 0;
        }
        
        .disclaimer {
            font-style: italic;
            font-size: 11px;
            color: #7f8c8d;
            margin-top: 15px;
        }
        
        /* Print Optimizations */
        @media print {
            body { font-size: 12px; }
            .container { padding: 20px; }
            .summary-grid { gap: 15px; }
            .section, .chart-container { break-inside: avoid; }
        }
        
        /* Utility Classes */
        .text-gradient {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .shadow-sm { box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .shadow-md { box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .shadow-lg { box-shadow: 0 8px 30px rgba(0,0,0,0.12); }
        
        /* Status Indicators */
        .status-indicator {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
        }
        
        .dot-success { background: var(--success); }
        .dot-warning { background: var(--warning); }
        .dot-danger { background: var(--danger); }
        .dot-info { background: var(--info); }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo-section">
                <div class="logo">🛡️</div>
                <div class="title">
                    <h1>AI-Powered Login Risk Assessment</h1>
                    <p>Comprehensive Security Intelligence Report</p>
                </div>
            </div>
            <div class="report-info">
                <div><strong>Report ID:</strong> RISK-{{ \Carbon\Carbon::now()->format('Ymd-His') }}</div>
                <div><strong>Generated:</strong> {{ \Carbon\Carbon::now()->format('F d, Y H:i:s') }}</div>
                <div><strong>Period:</strong> {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</div>
            </div>
        </div>

        <!-- Executive Summary -->
        <div class="summary-grid">
            <div class="summary-card">
                <div class="card-header">
                    <div class="card-icon icon-attempts">📊</div>
                    <div>
                        <div class="card-title">Total Attempts</div>
                        <div class="card-value">{{ number_format($totalAttempts) }}</div>
                        <div class="card-trend {{ $totalAttempts > 1000 ? 'trend-up' : 'trend-down' }}">
                            {{ $totalAttempts > 1000 ? '▲ High Volume' : '◄ Normal' }}
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="summary-card">
                <div class="card-header">
                    <div class="card-icon icon-success">✅</div>
                    <div>
                        <div class="card-title">Success Rate</div>
                        <div class="card-value">{{ number_format($successRate, 1) }}%</div>
                        <div class="card-trend {{ $successRate > 80 ? 'trend-up' : 'trend-down' }}">
                            {{ $successRate > 80 ? '▲ Excellent' : '▼ Needs Attention' }}
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="summary-card">
                <div class="card-header">
                    <div class="card-icon icon-suspicious">⚠️</div>
                    <div>
                        <div class="card-title">Suspicious Rate</div>
                        <div class="card-value">{{ number_format($suspiciousRate, 1) }}%</div>
                        <div class="card-trend {{ $suspiciousRate > 10 ? 'trend-up' : 'trend-down' }}">
                            {{ $suspiciousRate > 10 ? '▲ High Risk' : '▼ Low Risk' }}
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="summary-card">
                <div class="card-header">
                    <div class="card-icon icon-risk">📈</div>
                    <div>
                        <div class="card-title">Suspicious Attempts</div>
                        <div class="card-value">{{ number_format($suspiciousAttempts) }}</div>
                        <div class="card-trend {{ $suspiciousAttempts > 50 ? 'trend-up' : 'trend-down' }}">
                            {{ $suspiciousAttempts > 50 ? '▲ Monitor' : '▼ Stable' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Risky IPs -->
        <div class="section">
            <div class="section-title">Top Risky IP Addresses</div>
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
                    <tr>
                        <td><strong>#{{ $rank++ }}</strong></td>
                        <td><code>{{ $ip->ip_address }}</code></td>
                        <td>{{ $ip->attempt_count }}</td>
                        <td>{{ number_format($ip->avg_risk_score * 100, 1) }}%</td>
                        <td>
                            @php
                                $riskClass = $ip->avg_risk_score > 0.7 ? 'risk-critical' : 
                                            ($ip->avg_risk_score > 0.5 ? 'risk-high' : 
                                            ($ip->avg_risk_score > 0.3 ? 'risk-medium' : 'risk-low'));
                            @endphp
                            <span class="risk-badge {{ $riskClass }}">
                                {{ $ip->avg_risk_score > 0.7 ? 'CRITICAL' : 
                                   ($ip->avg_risk_score > 0.5 ? 'HIGH' : 
                                   ($ip->avg_risk_score > 0.3 ? 'MEDIUM' : 'LOW')) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Risk by Country -->
        <div class="section">
            <div class="section-title">Risk Analysis by Country</div>
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
                    @endphp
                    <tr>
                        <td><strong>{{ $country->country ?? 'Unknown' }}</strong></td>
                        <td>{{ $country->total_attempts }}</td>
                        <td>{{ $country->suspicious_attempts }}</td>
                        <td>{{ number_format($suspiciousRate, 1) }}%</td>
                        <td>{{ number_format($country->avg_risk_score * 100, 1) }}%</td>
                        <td>
                            <span class="risk-badge {{ $riskClass }}">
                                {{ $country->avg_risk_score > 0.7 ? 'CRITICAL' : 
                                   ($country->avg_risk_score > 0.5 ? 'HIGH' : 
                                   ($country->avg_risk_score > 0.3 ? 'MEDIUM' : 'LOW')) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Daily Trend -->
        <div class="section">
            <div class="section-title">Daily Login Activity Trend</div>
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
                            <span class="status-indicator">
                                <span class="status-dot {{ $suspiciousRate > 10 ? 'dot-danger' : ($suspiciousRate > 5 ? 'dot-warning' : 'dot-success') }}"></span>
                                {{ number_format($suspiciousRate, 1) }}%
                            </span>
                        </td>
                        <td>{{ number_format($day->avg_risk * 100, 1) }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Security Recommendations -->
        <div class="recommendations">
            <div class="section-title">Security Recommendations</div>
            
            <div class="recommendation-item">
                <div class="priority-badge priority-high">HIGH</div>
                <div>
                    <strong>Implement Rate Limiting</strong>
                    <p>Configure rate limiting for IP addresses with multiple failed attempts to prevent brute force attacks.</p>
                </div>
            </div>
            
            <div class="recommendation-item">
                <div class="priority-badge priority-medium">MEDIUM</div>
                <div>
                    <strong>Geo-Blocking for High-Risk Regions</strong>
                    <p>Consider blocking or challenging logins from countries with unusually high suspicious activity rates.</p>
                </div>
            </div>
            
            <div class="recommendation-item">
                <div class="priority-badge priority-low">LOW</div>
                <div>
                    <strong>Regular Security Audit</strong>
                    <p>Schedule monthly security audits to review patterns and update detection rules.</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>AI-Powered Login Detection System</strong></p>
            <p>This report contains sensitive security information. Handle with appropriate confidentiality measures.</p>
            <p class="disclaimer">
                Generated automatically by AI Security System v2.0 • Report validity: 30 days from generation date
            </p>
        </div>
    </div>
</body>
</html>