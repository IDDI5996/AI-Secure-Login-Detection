<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Risk Assessment Report') }}
            </h2>
            <div class="flex space-x-2">
                <form method="GET" action="{{ route('admin.risk-report') }}" class="flex space-x-2">
                    <input type="date" name="start_date" value="{{ $startDate }}" 
                           class="border-gray-300 rounded-md shadow-sm">
                    <input type="date" name="end_date" value="{{ $endDate }}" 
                           class="border-gray-300 rounded-md shadow-sm">
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition duration-150">
                        Filter
                    </button>
                </form>
                <a href="{{ route('admin.risk-report', ['export' => 'pdf', 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
                   class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition duration-150">
                    Export PDF
                </a>
                <a href="{{ route('admin.risk-report', ['export' => 'csv', 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
                   class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition duration-150">
                    Export CSV
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">Total Attempts</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalAttempts }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">Success Rate</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($successRate, 1) }}%</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">Suspicious Rate</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($suspiciousRate, 1) }}%</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">Period</p>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - 
                        {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
                    </p>
                </div>
            </div>

            <!-- Risky IPs -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Top Risky IP Addresses</h3>
                </div>
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP Address</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Attempts</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Avg Risk Score</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($riskyIPs as $ip)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $ip->ip_address }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $ip->attempt_count }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $ip->avg_risk_score > 0.7 ? 'bg-red-100 text-red-800' : 
                                           ($ip->avg_risk_score > 0.4 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                        {{ number_format($ip->avg_risk_score * 100, 1) }}%
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Daily Trend Chart -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Daily Login Trend</h3>
                </div>
                <div class="p-6">
                    <canvas id="dailyTrendChart" height="100"></canvas>
                </div>
            </div>

            <!-- Country Risk -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Risk by Country</h3>
                </div>
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Country</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Attempts</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Suspicious</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Risk Level</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($countryRisk as $country)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $country->country ?? 'Unknown' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $country->total_attempts }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $country->suspicious_attempts }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $country->avg_risk_score > 0.7 ? 'bg-red-100 text-red-800' : 
                                           ($country->avg_risk_score > 0.4 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                        {{ number_format($country->avg_risk_score * 100, 1) }}%
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Empty State -->
            @if($totalAttempts == 0)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-8 text-center mt-8">
                <svg class="h-12 w-12 text-yellow-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
                <h3 class="text-lg font-medium text-yellow-800 mb-2">No Data Available</h3>
                <p class="text-yellow-700">No login attempts were recorded for the selected date range. Try selecting a different period.</p>
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('dailyTrendChart').getContext('2d');
        const dailyTrendChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($dailyTrend->pluck('date')),
                datasets: [{
                    label: 'Total Attempts',
                    data: @json($dailyTrend->pluck('total_attempts')),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.1,
                    borderWidth: 2
                }, {
                    label: 'Suspicious Attempts',
                    data: @json($dailyTrend->pluck('suspicious_attempts')),
                    borderColor: 'rgb(239, 68, 68)',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.1,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>