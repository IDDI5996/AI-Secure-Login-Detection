<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    🔐 Security Logs
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">Real-time monitoring of all authentication events</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    📊 Total: {{ number_format($stats['total'] ?? 0) }}
                </span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    ✅ Today: {{ number_format($stats['today'] ?? 0) }}
                </span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                    🚨 Suspicious: {{ number_format($stats['suspicious'] ?? 0) }}
                </span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                    ❌ Failed: {{ number_format($stats['failed'] ?? 0) }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Stats Cards Row -->
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow">
                    <div class="text-2xl font-bold text-indigo-600">{{ number_format($stats['total'] ?? 0) }}</div>
                    <div class="text-xs text-gray-500">Total Events</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow">
                    <div class="text-2xl font-bold text-emerald-600">{{ number_format($stats['successful'] ?? 0) }}</div>
                    <div class="text-xs text-gray-500">Successful</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow">
                    <div class="text-2xl font-bold text-rose-600">{{ number_format($stats['suspicious'] ?? 0) }}</div>
                    <div class="text-xs text-gray-500">Suspicious</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow">
                    <div class="text-2xl font-bold text-amber-600">{{ number_format($stats['failed'] ?? 0) }}</div>
                    <div class="text-xs text-gray-500">Failed</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow">
                    <div class="text-2xl font-bold text-purple-600">{{ number_format($stats['avg_risk'] ?? 0, 1) }}%</div>
                    <div class="text-xs text-gray-500">Avg Risk Score</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow">
                    <div class="text-2xl font-bold text-cyan-600">{{ number_format($stats['today'] ?? 0) }}</div>
                    <div class="text-xs text-gray-500">Today's Events</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow">
                    <div class="text-2xl font-bold text-gray-700">{{ number_format($stats['unique_ips'] ?? 0) }}</div>
                    <div class="text-xs text-gray-500">Unique IPs</div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form action="{{ route('admin.security-logs') }}" method="GET" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                            <!-- Search -->
                            <div class="lg:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">🔍 Search</label>
                                <input type="text" name="search" value="{{ request('search') }}"
                                       placeholder="Search by user, email, IP, country..."
                                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <!-- Event Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">📌 Event Type</label>
                                <select name="event_type" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">All Events</option>
                                    <option value="suspicious" {{ request('event_type') == 'suspicious' ? 'selected' : '' }}>🚨 Suspicious</option>
                                    <option value="failed" {{ request('event_type') == 'failed' ? 'selected' : '' }}>❌ Failed</option>
                                    <option value="successful" {{ request('event_type') == 'successful' ? 'selected' : '' }}>✅ Successful</option>
                                </select>
                            </div>

                            <!-- Date From -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">📅 From</label>
                                <input type="date" name="date_from" value="{{ request('date_from') }}"
                                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <!-- Date To + Submit -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">📅 To</label>
                                <div class="flex">
                                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                                           class="w-full border-gray-300 rounded-l-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <button type="submit"
                                            class="px-4 py-2 bg-indigo-600 text-white rounded-r-lg hover:bg-indigo-700 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <a href="{{ route('admin.security-logs') }}" class="text-sm text-gray-600 hover:text-gray-900 hover:underline">
                                ↺ Reset Filters
                            </a>
                            <span class="text-sm text-gray-500">
                                Showing {{ $logs->firstItem() ?? 0 }} – {{ $logs->lastItem() ?? 0 }} of {{ number_format($logs->total()) }}
                            </span>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Main Logs Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP / Location</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Risk</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($logs as $log)
                                @php
                                    $isSuspicious = $log->is_suspicious ?? false;
                                    $isSuccessful = $log->is_successful ?? false;
                                    $riskScore = ($log->risk_score ?? 0) * 100;
                                    $riskColor = $riskScore >= 80 ? 'bg-rose-100 text-rose-800' :
                                                ($riskScore >= 60 ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800');

                                    // Safely parse detection factors
                                    $factors = [];
                                    if ($log->detection_factors) {
                                        $decoded = is_string($log->detection_factors)
                                            ? json_decode($log->detection_factors, true)
                                            : $log->detection_factors;
                                        if (is_array($decoded)) {
                                            $factors = $decoded;
                                        }
                                    }
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $log->user_name ?? 'Unknown' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $log->user_email ?? $log->attempted_email ?? 'N/A' }}
                                        </div>
                                        @if($log->user_id)
                                            <button onclick="showCorrelated('user', {{ $log->user_id }})"
                                                    class="text-xs text-indigo-600 hover:text-indigo-800 hover:underline">
                                                🔗 View all from this user
                                            </button>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="flex items-center gap-1.5">
                                            @if($isSuspicious)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800">
                                                    🚨 Suspicious
                                                </span>
                                            @elseif($isSuccessful)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                                    ✅ Success
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                                    ❌ Failed
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-400 mt-0.5">
                                            {{ $log->device_type ?? 'Unknown Device' }}
                                            @if($log->browser)
                                                • {{ $log->browser }}
                                            @endif
                                            @if($log->platform)
                                                • {{ $log->platform }}
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $log->ip_address ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500">
                                            {{ $log->country ?? 'Unknown' }}
                                            @if($log->city)
                                                • {{ $log->city }}
                                            @endif
                                        </div>
                                        <button onclick="showCorrelated('ip', '{{ $log->ip_address }}')"
                                                class="text-xs text-indigo-600 hover:text-indigo-800 hover:underline">
                                            🔗 View all from this IP
                                        </button>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if($isSuspicious)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $riskColor }}">
                                                {{ number_format($riskScore, 1) }}%
                                            </span>
                                            @if(!empty($factors))
                                                @php
                                                    // Extract readable factor names from complex structure
                                                    $factorMessages = [];
                                                    if (is_array($factors)) {
                                                        foreach ($factors as $factor) {
                                                            if (is_string($factor)) {
                                                                $factorMessages[] = $factor;
                                                            } elseif (is_array($factor)) {
                                                                if (isset($factor['factor']) && is_string($factor['factor'])) {
                                                                    $factorMessages[] = $factor['factor'];
                                                                } elseif (isset($factor['reason']) && is_string($factor['reason'])) {
                                                                    $factorMessages[] = $factor['reason'];
                                                                } else {
                                                                    // Try to find any string value
                                                                    $flat = array_filter($factor, 'is_string');
                                                                    if (!empty($flat)) {
                                                                        $factorMessages[] = reset($flat);
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                    $factorMessages = array_slice($factorMessages, 0, 2);
                                                    $factorString = implode(', ', $factorMessages);
                                                @endphp
                                                <div class="text-[10px] text-gray-400 mt-0.5 max-w-[120px] truncate" title="{{ json_encode($factors) }}">
                                                    {{ $factorString }}
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-xs text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        <div>{{ \Carbon\Carbon::parse($log->occurred_at)->format('M d, Y H:i') }}</div>
                                        <div class="text-xs text-gray-400">
                                            {{ \Carbon\Carbon::parse($log->occurred_at)->diffForHumans() }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                                        <button onclick="showCorrelated('ip', '{{ $log->ip_address }}')"
                                                class="text-indigo-600 hover:text-indigo-800 font-medium text-xs mr-2">
                                            🔍 Correlate
                                        </button>
                                        @if($isSuspicious)
                                            <a href="{{ route('admin.suspicious-activities') }}?search={{ urlencode($log->ip_address) }}"
                                               class="text-amber-600 hover:text-amber-800 text-xs font-medium">
                                                🚨 View in Activities
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-16 h-16 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                            </svg>
                                            <p class="text-lg font-medium text-gray-600">No security logs found</p>
                                            <p class="text-sm text-gray-400 mt-1">Try adjusting your filters or check back later</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($logs->hasPages())
                    <div class="px-4 py-3 border-t border-gray-200">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>

            <!-- Correlated Events Section -->
            @if($correlatedEvents->isNotEmpty())
                <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">🔗 Correlated Events (Last 24 Hours)</h3>
                        <span class="text-xs text-gray-500">Grouped by IP address with multiple attempts</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($correlatedEvents as $correlated)
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100 hover:border-indigo-200 transition-colors cursor-pointer"
                                 onclick="showCorrelated('ip', '{{ $correlated->ip_address }}')">
                                <div class="flex items-center justify-between">
                                    <div class="text-sm font-medium text-gray-900">{{ $correlated->ip_address }}</div>
                                    <span class="text-xs font-semibold {{ $correlated->suspicious_count > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                        {{ $correlated->suspicious_count > 0 ? '⚠️ ' . $correlated->suspicious_count . ' suspicious' : '✅ Clean' }}
                                    </span>
                                </div>
                                <div class="flex gap-4 mt-2 text-xs text-gray-500">
                                    <span>🔄 {{ $correlated->total_attempts }} attempts</span>
                                    <span>✅ {{ $correlated->successful_count }} success</span>
                                    <span>⏱ {{ \Carbon\Carbon::parse($correlated->first_attempt)->diffForHumans(\Carbon\Carbon::parse($correlated->last_attempt), true) }}</span>
                                </div>
                                <div class="text-[10px] text-gray-400 mt-1">
                                    First: {{ \Carbon\Carbon::parse($correlated->first_attempt)->diffForHumans() }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>

    <!-- ============================================================
    CORRELATION MODAL
    ============================================================ -->
    <div id="correlationModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
                <!-- Modal Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">🔗 Correlated Events</h3>
                        <p class="text-sm text-gray-500" id="correlationInfo">Loading...</p>
                    </div>
                    <button onclick="closeCorrelationModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <!-- Modal Body -->
                <div class="px-6 py-4 overflow-y-auto max-h-[70vh]">
                    <div id="correlationContent">
                        <div class="flex justify-center items-center py-12">
                            <div class="animate-spin rounded-full h-12 w-12 border-4 border-indigo-200 border-t-indigo-600"></div>
                        </div>
                    </div>
                </div>
                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end">
                    <button onclick="closeCorrelationModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
    JAVASCRIPT
    ============================================================ -->
    <script>
        function showCorrelated(type, value) {
            const modal = document.getElementById('correlationModal');
            const content = document.getElementById('correlationContent');
            const info = document.getElementById('correlationInfo');

            // Show loading
            content.innerHTML = `
                <div class="flex justify-center items-center py-12">
                    <div class="animate-spin rounded-full h-12 w-12 border-4 border-indigo-200 border-t-indigo-600"></div>
                </div>
            `;

            const labels = {
                'ip': 'IP Address',
                'user': 'User ID',
                'email': 'Email'
            };
            info.textContent = `Showing events for ${labels[type] || type}: ${value}`;

            // Show modal
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            // Fetch correlated events
            fetch(`{{ route('admin.security-logs.correlated') }}?type=${type}&value=${encodeURIComponent(value)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(result => {
                if (!result.success || !result.data || result.data.length === 0) {
                    content.innerHTML = `
                        <div class="text-center py-12">
                            <p class="text-gray-500">No correlated events found for this ${type}.</p>
                        </div>
                    `;
                    return;
                }

                let html = `
                    <div class="space-y-3">
                        <div class="text-sm text-gray-500 mb-4">
                            Found ${result.data.length} event${result.data.length > 1 ? 's' : ''}
                        </div>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">IP</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Risk</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                `;

                result.data.forEach(event => {
                    const isSuspicious = event.is_suspicious;
                    const isSuccessful = event.is_successful;
                    const risk = (event.risk_score || 0) * 100;

                    html += `
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-500">
                                ${new Date(event.attempted_at).toLocaleString()}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900">
                                ${event.user_name || 'Unknown'}
                                <div class="text-[10px] text-gray-400">${event.user_email || ''}</div>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap">
                                ${isSuspicious ? '<span class="text-xs font-medium text-rose-600">🚨 Suspicious</span>' :
                                  isSuccessful ? '<span class="text-xs font-medium text-emerald-600">✅ Success</span>' :
                                  '<span class="text-xs font-medium text-amber-600">❌ Failed</span>'}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-500">
                                ${event.ip_address || 'N/A'}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap">
                                ${isSuspicious ? `<span class="text-xs font-medium ${risk >= 80 ? 'text-rose-600' : risk >= 60 ? 'text-amber-600' : 'text-emerald-600'}">${risk.toFixed(1)}%</span>` : '—'}
                            </td>
                        </tr>
                    `;
                });

                html += `
                            </tbody>
                        </table>
                    </div>
                `;

                content.innerHTML = html;
            })
            .catch(error => {
                content.innerHTML = `
                    <div class="text-center py-12">
                        <p class="text-rose-600">Error loading correlated events.</p>
                        <p class="text-xs text-gray-500 mt-2">${error.message}</p>
                    </div>
                `;
                console.error('Error:', error);
            });
        }

        function closeCorrelationModal() {
            document.getElementById('correlationModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Close on backdrop click
        document.getElementById('correlationModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCorrelationModal();
            }
        });

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeCorrelationModal();
            }
        });
    </script>

    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        #correlationModal .bg-white {
            animation: fadeIn 0.25s ease-out;
        }
        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px -8px rgba(0,0,0,0.1);
        }
    </style>
</x-app-layout>