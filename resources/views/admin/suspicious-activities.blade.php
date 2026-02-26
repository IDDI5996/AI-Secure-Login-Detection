<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Suspicious Activities Review') }}
            </h2>
            <div class="flex space-x-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    Total: {{ $stats['total'] ?? 0 }}
                </span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                    Pending: {{ $stats['pending'] ?? 0 }}
                </span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                    High Risk: {{ $stats['high_risk'] ?? 0 }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form action="{{ route('admin.suspicious-activities') }}" method="GET" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Status Filter -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="status" id="status" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Statuses</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="reviewed" {{ request('status') == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                    <option value="false_positive" {{ request('status') == 'false_positive' ? 'selected' : '' }}>False Positive</option>
                                </select>
                            </div>

                            <!-- Activity Type Filter -->
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Activity Type</label>
                                <select name="type" id="type" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Types</option>
                                    <option value="login_attempt" {{ request('type') == 'login_attempt' ? 'selected' : '' }}>Login Attempt</option>
                                    <option value="unusual_location" {{ request('type') == 'unusual_location' ? 'selected' : '' }}>Unusual Location</option>
                                    <option value="multiple_failures" {{ request('type') == 'multiple_failures' ? 'selected' : '' }}>Multiple Failures</option>
                                </select>
                            </div>

                            <!-- Search -->
                            <div class="md:col-span-2">
                                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                                <div class="flex">
                                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                           placeholder="Search by user name, email, or IP..."
                                           class="flex-1 border-gray-300 rounded-l-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-r-md hover:bg-blue-700">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Date Range -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" 
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" 
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div class="flex items-end">
                                <a href="{{ route('admin.suspicious-activities') }}" 
                                   class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Reset Filters
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Activities Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activity Type</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Risk Score</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detected At</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($activities as $activity)
                                @php
                                    $riskScore = $activity->risk_score * 100;
                                    $riskColor = $riskScore >= 80 ? 'bg-red-100 text-red-800' : 
                                                ($riskScore >= 60 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800');
                                    
                                    $statusColor = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'reviewed' => 'bg-blue-100 text-blue-800',
                                        'resolved' => 'bg-green-100 text-green-800',
                                        'false_positive' => 'bg-gray-100 text-gray-800',
                                    ][$activity->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img class="h-10 w-10 rounded-full" src="{{ $activity->user->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($activity->user->name ?? 'Unknown') . '&color=7F9CF5&background=EBF4FF' }}" alt="">
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $activity->user->name ?? 'Unknown User' }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $activity->user->email ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $activity->activity_type)) }}</div>
                                        <div class="text-sm text-gray-500">
                                            {{ $activity->activity_data['device'] ?? 'Unknown' }} • {{ $activity->activity_data['browser'] ?? 'Unknown' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $riskColor }}">
                                            {{ number_format($riskScore, 1) }}%
                                        </span>
                                        <div class="text-xs text-gray-500 mt-1">
                                            @if(is_array($activity->detection_reasons))
                                                {{ implode(', ', array_slice($activity->detection_reasons, 0, 2)) }}
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                                            {{ ucfirst(str_replace('_', ' ', $activity->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $activity->activity_data['location'] ?? 'Unknown' }}</div>
                                        <div class="text-sm text-gray-500">
                                            IP: {{ $activity->activity_data['ip_address'] ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $activity->created_at->format('M d, Y H:i') }}
                                        <div class="text-xs text-gray-400">
                                            {{ $activity->created_at->diffForHumans() }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <!-- View Button - Modal Trigger -->
                                        <button onclick="showActivityDetails({{ $activity->id }})" 
                                                class="text-blue-600 hover:text-blue-900 mr-3">
                                            View
                                        </button>
                                        
                                        <!-- Review Form (only for pending activities) -->
                                        @if($activity->status === 'pending')
                                            <form action="{{ route('admin.update-activity', $activity->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="reviewed">
                                                <button type="submit" 
                                                        onclick="return confirm('Mark this activity as reviewed?')"
                                                        class="text-green-600 hover:text-green-900">
                                                    Review
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-400">Reviewed</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No suspicious activities found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($activities->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $activities->links() }}
                    </div>
                @endif
            </div>

            <!-- Statistics Cards -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5">
                            <p class="text-sm font-medium text-gray-500">Total Activities</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5">
                            <p class="text-sm font-medium text-gray-500">Pending Review</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['pending'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <div class="ml-5">
                            <p class="text-sm font-medium text-gray-500">High Risk</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['high_risk'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5">
                            <p class="text-sm font-medium text-gray-500">Avg Risk Score</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['avg_risk_score'] ?? 0, 1) }}%</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for View Details -->
    <div id="activityModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto overflow-x-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Activity Details</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="modalContent" class="space-y-4">
                <!-- Content will be loaded here via JavaScript -->
            </div>
        </div>
    </div>

<!-- JavaScript for Modal -->
<script>
    function showActivityDetails(activityId) {
        // Show loading
        document.getElementById('modalContent').innerHTML = `
            <div class="text-center py-8">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
                <p class="mt-4 text-gray-500">Loading activity details...</p>
                <p class="text-xs text-gray-400 mt-1">ID: ${activityId}</p>
            </div>
        `;
        
        // Show modal
        document.getElementById('activityModal').classList.remove('hidden');
        
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Fetch activity details
        fetch(`/admin/suspicious-activity/${activityId}/details`, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.error || `HTTP error! status: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(result => {
            console.log('API Result:', result);
            
            if (!result.success) {
                throw new Error(result.error || 'Unknown error occurred');
            }
            
            const activity = result.data;
            const riskScore = activity.risk_score * 100;
            
            // Format activity data safely
            const activityData = activity.activity_data || {};
            const user = activity.user || {};
            const reviewer = activity.reviewer || {};
            const detectionReasons = Array.isArray(activity.detection_reasons) ? activity.detection_reasons : [];
            
            let modalHtml = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- User Info -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-medium text-gray-900 mb-2">User Information</h4>
                        <p class="text-sm text-gray-600"><strong>Name:</strong> ${user.name || 'Unknown'}</p>
                        <p class="text-sm text-gray-600"><strong>Email:</strong> ${user.email || 'N/A'}</p>
                        <p class="text-sm text-gray-600"><strong>User ID:</strong> ${activity.user_id}</p>
                    </div>
                    
                    <!-- Activity Info -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-medium text-gray-900 mb-2">Activity Information</h4>
                        <p class="text-sm text-gray-600"><strong>Type:</strong> ${(activity.activity_type || '').replace(/_/g, ' ')}</p>
                        <p class="text-sm text-gray-600"><strong>Status:</strong> <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ${getStatusColorClass(activity.status)}">${(activity.status || '').replace(/_/g, ' ')}</span></p>
                        <p class="text-sm text-gray-600"><strong>Risk Score:</strong> <span class="font-semibold ${getRiskColorClass(riskScore)}">${riskScore.toFixed(1)}%</span></p>
                    </div>
                    
                    <!-- Location Info -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-medium text-gray-900 mb-2">Location Information</h4>
                        <p class="text-sm text-gray-600"><strong>IP Address:</strong> ${activityData.ip_address || 'N/A'}</p>
                        <p class="text-sm text-gray-600"><strong>Location:</strong> ${activityData.location || 'Unknown'}</p>
                        <p class="text-sm text-gray-600"><strong>Device:</strong> ${activityData.device || 'Unknown'} (${activityData.browser || 'Unknown'})</p>
                    </div>
                    
                    <!-- Timestamps -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-medium text-gray-900 mb-2">Timestamps</h4>
                        <p class="text-sm text-gray-600"><strong>Detected:</strong> ${formatDate(activity.created_at)}</p>
                        ${activity.reviewed_at ? `<p class="text-sm text-gray-600"><strong>Reviewed:</strong> ${formatDate(activity.reviewed_at)}</p>` : ''}
                        ${reviewer.name ? `<p class="text-sm text-gray-600"><strong>Reviewed By:</strong> ${reviewer.name}</p>` : ''}
                    </div>
                </div>
                
                <!-- Detection Reasons -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-medium text-gray-900 mb-2">Detection Reasons</h4>
                    <div class="flex flex-wrap gap-2">
            `;
            
            if (detectionReasons.length > 0) {
                detectionReasons.forEach(reason => {
                    modalHtml += `<span class="inline-block bg-white px-3 py-1 rounded-full text-xs text-gray-700 border">${reason}</span>`;
                });
            } else {
                modalHtml += `<p class="text-sm text-gray-500">No specific reasons provided.</p>`;
            }
            
            modalHtml += `
                    </div>
                </div>
            `;
            
            // Add review notes if available
            if (activity.review_notes) {
                modalHtml += `
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-medium text-gray-900 mb-2">Review Notes</h4>
                        <p class="text-sm text-gray-600">${activity.review_notes}</p>
                    </div>
                `;
            }
            
            // Add quick actions for pending activities
            if (activity.status === 'pending') {
                modalHtml += `
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h4 class="font-medium text-blue-900 mb-2">Quick Actions</h4>
                        <div class="flex space-x-2">
                            <form action="/admin/suspicious-activities/${activityId}" method="POST" class="inline">
                                <input type="hidden" name="_token" value="${csrfToken}">
                                <input type="hidden" name="_method" value="PUT">
                                <input type="hidden" name="status" value="reviewed">
                                <button type="submit" onclick="return confirm('Mark this activity as reviewed?')" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                                    Mark as Reviewed
                                </button>
                            </form>
                            <form action="/admin/suspicious-activities/${activityId}" method="POST" class="inline">
                                <input type="hidden" name="_token" value="${csrfToken}">
                                <input type="hidden" name="_method" value="PUT">
                                <input type="hidden" name="status" value="false_positive">
                                <button type="submit" onclick="return confirm('Mark this activity as false positive?')" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm">
                                    Mark as False Positive
                                </button>
                            </form>
                        </div>
                    </div>
                `;
            }
            
            document.getElementById('modalContent').innerHTML = modalHtml;
        })
        .catch(error => {
            console.error('Error details:', error);
            document.getElementById('modalContent').innerHTML = `
                <div class="text-center py-8">
                    <p class="text-red-500">Error loading activity details.</p>
                    <p class="text-sm text-gray-500 mt-2">${error.message}</p>
                    <p class="text-xs text-gray-400 mt-1">Check browser console for more details.</p>
                    <button onclick="retryLoad(${activityId})" class="mt-4 px-4 py-2 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200">
                        Retry
                    </button>
                </div>
            `;
        });
    }
    
    // Helper functions
    function getStatusColorClass(status) {
        const colors = {
            'pending': 'bg-yellow-100 text-yellow-800',
            'reviewed': 'bg-blue-100 text-blue-800',
            'resolved': 'bg-green-100 text-green-800',
            'false_positive': 'bg-gray-100 text-gray-800'
        };
        return colors[status] || 'bg-gray-100 text-gray-800';
    }
    
    function getRiskColorClass(score) {
        if (score >= 80) return 'text-red-600';
        if (score >= 60) return 'text-yellow-600';
        return 'text-green-600';
    }
    
    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        try {
            const date = new Date(dateString);
            return date.toLocaleString();
        } catch (e) {
            return dateString;
        }
    }
    
    function retryLoad(activityId) {
        showActivityDetails(activityId);
    }
    
    function closeModal() {
        document.getElementById('activityModal').classList.add('hidden');
    }
    
    // Close modal when clicking outside
    document.getElementById('activityModal').addEventListener('click', function(e) {
        if (e.target.id === 'activityModal') {
            closeModal();
        }
    });
</script>
</x-app-layout>