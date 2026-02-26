<!DOCTYPE html>
<html>
<head>
    <title>Audit Log Report</title>
    <style>
        @page { margin: 50px 25px; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 15px; }
        .header h1 { margin: 0; font-size: 24px; color: #333; }
        .header .subtitle { margin-top: 5px; color: #666; font-size: 14px; }
        .info { margin-bottom: 25px; background: #f9f9f9; padding: 15px; border-radius: 5px; }
        .info p { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 11px; }
        th { background-color: #2c3e50; color: white; text-align: left; padding: 10px; border: 1px solid #34495e; font-weight: bold; }
        td { padding: 8px; border: 1px solid #ddd; }
        tr:nth-child(even) { background-color: #f8f9fa; }
        .status-success { color: #27ae60; font-weight: bold; }
        .status-failed { color: #e74c3c; font-weight: bold; }
        .suspicious-yes { color: #e74c3c; font-weight: bold; }
        .suspicious-no { color: #27ae60; font-weight: bold; }
        .footer { margin-top: 40px; text-align: center; color: #666; font-size: 10px; border-top: 1px solid #ddd; padding-top: 10px; }
        .page-number:after { content: "Page " counter(page); }
    </style>
</head>
<body>
    <div class="header">
        <h1>AI-Powered Login Detection System</h1>
        <div class="subtitle">Audit Log Report</div>
        <div class="subtitle">Generated on: {{ $generatedAt }}</div>
    </div>
    
    <div class="info">
        <p><strong>Report Summary:</strong></p>
        <p><strong>Total Records:</strong> {{ number_format(count($loginAttempts)) }}</p>
        <p><strong>Date Range:</strong> 
            @if($dateFrom && $dateTo)
                {{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}
            @elseif($dateFrom)
                From {{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }}
            @elseif($dateTo)
                Until {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}
            @else
                All dates
            @endif
        </p>
        @php
            $successful = $loginAttempts->where('is_successful', true)->count();
            $suspicious = $loginAttempts->where('is_suspicious', true)->count();
        @endphp
        <p><strong>Successful Logins:</strong> {{ $successful }} ({{ $loginAttempts->count() > 0 ? number_format(($successful/$loginAttempts->count())*100, 1) : 0 }}%)</p>
        <p><strong>Suspicious Attempts:</strong> {{ $suspicious }} ({{ $loginAttempts->count() > 0 ? number_format(($suspicious/$loginAttempts->count())*100, 1) : 0 }}%)</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>IP Address</th>
                <th>Location</th>
                <th>Device</th>
                <th>Status</th>
                <th>Suspicious</th>
                <th>Risk Score</th>
                <th>Verification</th>
                <th>Attempted At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($loginAttempts as $attempt)
            @php
                $verificationAttempt = $attempt->verificationAttempt;
            @endphp
            <tr>
                <td>{{ $attempt->id }}</td>
                <td>
                    {{ $attempt->user->name ?? 'Unknown' }}<br>
                    <small>{{ $attempt->user->email ?? 'N/A' }}</small>
                </td>
                <td>{{ $attempt->ip_address }}</td>
                <td>@if($attempt->country || $attempt->city)
                    <div class="text-sm text-gray-900">
                        {{ $attempt->city }}{{ $attempt->city && $attempt->country ? ', ' : '' }}{{ $attempt->country }}
                    </div>
                    @else
                    <div class="text-sm text-gray-900">Unknown</div>
                    @endif
                </td>
                <td>
                    {{ $attempt->device_type ?? 'Unknown' }}<br>
                    <small>{{ $attempt->browser ?? 'Unknown' }}</small>
                </td>
                <td class="{{ $attempt->is_successful ? 'status-success' : 'status-failed' }}">
                    {{ $attempt->is_successful ? '✓ Successful' : '✗ Failed' }}
                </td>
                <td class="{{ $attempt->is_suspicious ? 'suspicious-yes' : 'suspicious-no' }}">
                    {{ $attempt->is_suspicious ? 'Yes' : 'No' }}
                </td>
                <td>
                    <strong>{{ number_format($attempt->risk_score * 100, 1) }}%</strong><br>
                    @if($attempt->risk_score >= 0.8)
                        <small style="color: #e74c3c;">High Risk</small>
                    @elseif($attempt->risk_score >= 0.6)
                        <small style="color: #f39c12;">Medium Risk</small>
                    @else
                        <small style="color: #27ae60;">Low Risk</small>
                    @endif
                </td>
                <td>
                    {{ $verificationAttempt->verification_method ?? 'N/A' }}<br>
                    @if($verificationAttempt)
                        <small class="{{ $verificationAttempt->is_successful ? 'status-success' : 'status-failed' }}">
                            {{ $verificationAttempt->is_successful ? 'Verified' : 'Failed' }}
                        </small>
                    @endif
                </td>
                <td>{{ $attempt->attempted_at->format('Y-m-d H:i:s') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <p>Generated by AI-Powered Login Detection System • Confidential Report</p>
        <p>Page <span class="page-number"></span></p>
    </div>
    
    <script type="text/php">
        if (isset($pdf)) {
            $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
            $size = 10;
            $font = $fontMetrics->getFont("Helvetica");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 35;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
    </script>
</body>
</html>