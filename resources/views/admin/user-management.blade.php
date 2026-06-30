<x-app-layout>
    <x-slot name="header">
        <style>
            /* Prevent double scrollbars */
            html, body {
                overflow: hidden !important;
                height: 100%;
                margin: 0;
                padding: 0;
            }
            .app-layout-container {
                height: 100vh;
                overflow: hidden;
            }
            /* Animation for modals */
            @keyframes slideUp {
                from { opacity: 0; transform: translateY(30px) scale(0.95); }
                to { opacity: 1; transform: translateY(0) scale(1); }
            }
            .modal-content {
                animation: slideUp 0.25s ease-out;
            }
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            .modal-backdrop {
                animation: fadeIn 0.2s ease-out;
            }
            /* Card hover effects */
            .stat-card {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .stat-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.1);
            }
            .user-row {
                transition: background-color 0.2s ease;
            }
            .user-row:hover {
                background-color: #f8fafc;
            }
            /* Badge animations */
            .badge-pulse {
                animation: pulse 2s infinite;
            }
            @keyframes pulse {
                0%, 100% { opacity: 1; }
                50% { opacity: 0.6; }
            }
        </style>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    👥 User Management
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">Manage users, roles, and security settings</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.users.export') }}" 
                   class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Export CSV
                </a>
                <button onclick="openBlockIpModal()" 
                        class="inline-flex items-center px-4 py-2 bg-rose-600 text-white rounded-lg hover:bg-rose-700 transition-colors text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    Block IP
                </button>
            </div>
        </div>
    </x-slot>

    <div class="flex bg-gray-50 overflow-hidden app-layout-container" style="height: 100vh; max-height: 100vh;">
        <main class="flex-1 overflow-y-auto px-4 py-6 md:px-6 lg:px-8" style="height: 100vh;">
            <div class="max-w-7xl mx-auto">
                
                <!-- Stats Cards -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 stat-card">
                        <div class="text-2xl font-bold text-indigo-600">{{ number_format($stats['total']) }}</div>
                        <div class="text-xs text-gray-500">Total Users</div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 stat-card">
                        <div class="text-2xl font-bold text-emerald-600">{{ number_format($stats['active']) }}</div>
                        <div class="text-xs text-gray-500">Active</div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 stat-card">
                        <div class="text-2xl font-bold text-amber-600">{{ number_format($stats['inactive']) }}</div>
                        <div class="text-xs text-gray-500">Inactive</div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 stat-card">
                        <div class="text-2xl font-bold text-rose-600">{{ number_format($stats['locked']) }}</div>
                        <div class="text-xs text-gray-500">Locked</div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 stat-card">
                        <div class="text-2xl font-bold text-purple-600">{{ number_format($stats['admins']) }}</div>
                        <div class="text-xs text-gray-500">Admins</div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 stat-card">
                        <div class="text-2xl font-bold text-cyan-600">{{ number_format($stats['blocked_ips']) }}</div>
                        <div class="text-xs text-gray-500">Blocked IPs</div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
                    <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
                        <!-- Search -->
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-xs font-medium text-gray-700 mb-1">🔍 Search</label>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Search by name, email, ID..."
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        </div>

                        <!-- Role -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">👤 Role</label>
                            <select name="role" class="border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                <option value="">All Roles</option>
                                @foreach($roles as $key => $label)
                                    <option value="{{ $key }}" {{ request('role') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">📊 Status</label>
                            <select name="status" class="border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>✅ Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>⛔ Inactive</option>
                                <option value="locked" {{ request('status') == 'locked' ? 'selected' : '' }}>🔒 Locked</option>
                                <option value="admin" {{ request('status') == 'admin' ? 'selected' : '' }}>👑 Admin</option>
                            </select>
                        </div>

                        <!-- Per Page -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">📄 Per Page</label>
                            <select name="per_page" class="border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                                <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </div>

                        <!-- Buttons -->
                        <div class="flex gap-2">
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                                Apply Filters
                            </button>
                            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Bulk Actions Bar -->
                <div id="bulkActions" class="hidden bg-indigo-50 border border-indigo-200 rounded-lg p-3 mb-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-indigo-700">
                            <span id="selectedCount">0</span> users selected
                        </span>
                        <div class="h-4 w-px bg-indigo-300"></div>
                        <select id="bulkActionSelect" class="text-sm border-indigo-300 rounded-md">
                            <option value="">Bulk Action</option>
                            <option value="activate">✅ Activate</option>
                            <option value="deactivate">⛔ Deactivate</option>
                            <option value="lock">🔒 Lock</option>
                            <option value="unlock">🔓 Unlock</option>
                            <option value="delete">🗑️ Delete</option>
                        </select>
                        <button onclick="executeBulkAction()" class="px-3 py-1 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm">
                            Apply
                        </button>
                    </div>
                    <button onclick="clearSelection()" class="text-sm text-gray-500 hover:text-gray-700">
                        ✕ Clear
                    </button>
                </div>

                <!-- Users Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left">
                                        <input type="checkbox" id="selectAll" onchange="toggleAllUsers()" 
                                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse($users as $user)
                                    @php
                                        $isActive = $user->is_active && !$user->is_locked;
                                        $statusClass = $user->is_locked ? 'bg-rose-100 text-rose-800' : 
                                                      ($user->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800');
                                        $statusText = $user->is_locked ? '🔒 Locked' : 
                                                     ($user->is_active ? '✅ Active' : '⛔ Inactive');
                                    @endphp
                                    <tr class="user-row">
                                        <td class="px-4 py-3">
                                            <input type="checkbox" class="user-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" 
                                                   value="{{ $user->id }}" onchange="updateBulkActions()">
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                <img class="w-10 h-10 rounded-full object-cover border-2 border-gray-200" 
                                                     src="{{ $user->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=7F9CF5&background=EBF4FF&size=40' }}" 
                                                     alt="{{ $user->name }}">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                                    <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                                    <div class="text-[10px] text-gray-400">ID: #{{ $user->id }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                {{ $user->is_admin ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-700' }}">
                                                {{ $roles[$user->role] ?? $user->role }}
                                            </span>
                                            @if($user->is_admin)
                                                <span class="text-[10px] text-purple-600 block">👑 Admin</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                                {{ $statusText }}
                                            </span>
                                            @if($user->is_locked && $user->lock_reason)
                                                <div class="text-[10px] text-gray-400 mt-0.5" title="{{ $user->lock_reason }}">
                                                    {{ Str::limit($user->lock_reason, 30) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-xs text-gray-500">
                                            {{ $user->created_at->format('M d, Y') }}
                                            <div class="text-[10px] text-gray-400">{{ $user->created_at->diffForHumans() }}</div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-1">
                                                <button onclick="viewUser({{ $user->id }})" 
                                                        class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="View Details">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </button>
                                                
                                                @if(!$user->is_locked)
                                                    <button onclick="toggleLock({{ $user->id }})" 
                                                            class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Lock User">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                        </svg>
                                                    </button>
                                                @else
                                                    <button onclick="toggleLock({{ $user->id }})" 
                                                            class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Unlock User">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                                                        </svg>
                                                    </button>
                                                @endif

                                                @if($user->is_active)
                                                    <button onclick="toggleActive({{ $user->id }})" 
                                                            class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Disable User">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                        </svg>
                                                    </button>
                                                @else
                                                    <button onclick="toggleActive({{ $user->id }})" 
                                                            class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Enable User">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </button>
                                                @endif

                                                <button onclick="deleteUser({{ $user->id }})" 
                                                        class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Delete User">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-16 h-16 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                                <p class="text-lg font-medium text-gray-600">No users found</p>
                                                <p class="text-sm text-gray-400 mt-1">Try adjusting your filters</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($users->hasPages())
                        <div class="px-4 py-3 border-t border-gray-200">
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>

            </div>
        </main>
    </div>

    <!-- ============================================================
    MODALS
    ============================================================ -->

    <!-- View User Modal -->
    <div id="viewUserModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 hidden modal-backdrop">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden modal-content">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">👤 User Details</h3>
                        <p class="text-sm text-gray-500" id="viewUserInfo">Loading...</p>
                    </div>
                    <button onclick="closeModal('viewUserModal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div id="viewUserContent" class="px-6 py-4 overflow-y-auto max-h-[70vh]">
                    <div class="flex justify-center items-center py-12">
                        <div class="animate-spin rounded-full h-12 w-12 border-4 border-indigo-200 border-t-indigo-600"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Block IP Modal -->
    <div id="blockIpModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 hidden modal-backdrop">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full modal-content">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">🚫 Block IP Address</h3>
                    <button onclick="closeModal('blockIpModal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form id="blockIpForm" onsubmit="submitBlockIp(event)" class="px-6 py-4">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">IP Address <span class="text-rose-500">*</span></label>
                            <input type="text" id="ipAddress" name="ip_address" placeholder="e.g. 192.168.1.100"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Reason <span class="text-rose-500">*</span></label>
                            <textarea id="ipReason" name="reason" rows="3" placeholder="Why is this IP being blocked?"
                                      class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Unblock At (Optional)</label>
                            <input type="datetime-local" id="ipUnblockAt" name="unblocks_at"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="text-xs text-gray-400 mt-1">Leave empty for permanent block</p>
                        </div>
                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                            <button type="button" onclick="closeModal('blockIpModal')" 
                                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-rose-600 text-white rounded-lg hover:bg-rose-700 transition-colors">
                                Block IP
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="fixed bottom-4 right-4 z-50 transform transition-all duration-500 translate-y-16 opacity-0">
        <div class="bg-gray-900 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3">
            <span id="toastIcon"></span>
            <span id="toastMessage" class="text-sm"></span>
        </div>
    </div>

    <!-- ============================================================
    JAVASCRIPT
    ============================================================ -->
    <script>
        // ── Helper Functions ──
        function getCSRFToken() {
            return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        }

        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const icon = document.getElementById('toastIcon');
            const msg = document.getElementById('toastMessage');
            
            icon.textContent = type === 'success' ? '✅' : type === 'error' ? '❌' : '⚠️';
            msg.textContent = message;
            
            toast.classList.remove('translate-y-16', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
            
            clearTimeout(toast._timeout);
            toast._timeout = setTimeout(() => {
                toast.classList.add('translate-y-16', 'opacity-0');
                toast.classList.remove('translate-y-0', 'opacity-100');
            }, 4000);
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            document.body.style.overflow = '';
        }

        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        // ── View User ──
        function viewUser(userId) {
            openModal('viewUserModal');
            const content = document.getElementById('viewUserContent');
            const info = document.getElementById('viewUserInfo');
            info.textContent = 'Loading user data...';
            
            content.innerHTML = `
                <div class="flex justify-center items-center py-12">
                    <div class="animate-spin rounded-full h-12 w-12 border-4 border-indigo-200 border-t-indigo-600"></div>
                </div>
            `;

            fetch(`/admin/users/${userId}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(result => {
                if (!result.success) throw new Error(result.message || 'Failed to load user');
                
                const data = result.data;
                const user = data.user;
                info.textContent = `Showing details for ${user.name}`;
                
                let html = `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">👤 Profile</h4>
                            <div class="flex items-center gap-3 mb-3">
                                <img class="w-14 h-14 rounded-full object-cover border-2 border-gray-200" 
                                     src="${user.profile_photo_url || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name) + '&color=7F9CF5&background=EBF4FF&size=56'}" 
                                     alt="${user.name}">
                                <div>
                                    <div class="font-semibold text-gray-900">${user.name}</div>
                                    <div class="text-sm text-gray-500">${user.email}</div>
                                    <div class="text-xs text-gray-400">ID: #${user.id}</div>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <div><span class="text-gray-500">Role:</span> ${user.role || 'N/A'}</div>
                                <div><span class="text-gray-500">Admin:</span> ${user.is_admin ? '✅ Yes' : '❌ No'}</div>
                                <div><span class="text-gray-500">Status:</span> ${user.is_locked ? '🔒 Locked' : user.is_active ? '✅ Active' : '⛔ Inactive'}</div>
                                <div><span class="text-gray-500">Verified:</span> ${user.email_verified_at ? '✅ Yes' : '❌ No'}</div>
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">📊 Activity</h4>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div class="bg-white rounded p-2 text-center">
                                    <div class="text-xl font-bold text-indigo-600">${data.login_count || 0}</div>
                                    <div class="text-[10px] text-gray-500">Total Logins</div>
                                </div>
                                <div class="bg-white rounded p-2 text-center">
                                    <div class="text-xl font-bold text-rose-600">${data.failed_attempts || 0}</div>
                                    <div class="text-[10px] text-gray-500">Failed Attempts</div>
                                </div>
                                <div class="bg-white rounded p-2 text-center">
                                    <div class="text-xl font-bold text-amber-600">${data.suspicious_count || 0}</div>
                                    <div class="text-[10px] text-gray-500">Suspicious</div>
                                </div>
                                <div class="bg-white rounded p-2 text-center">
                                    <div class="text-xl font-bold text-emerald-600">${data.last_login ? new Date(data.last_login.attempted_at).toLocaleDateString() : 'N/A'}</div>
                                    <div class="text-[10px] text-gray-500">Last Login</div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // Recent login attempts
                if (data.history && data.history.length > 0) {
                    html += `
                        <div class="bg-gray-50 rounded-lg p-4 mt-4">
                            <h4 class="font-medium text-gray-900 mb-3">🕐 Recent Login History</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead class="text-xs text-gray-500">
                                        <tr>
                                            <th class="text-left py-1 px-2">Date</th>
                                            <th class="text-left py-1 px-2">IP</th>
                                            <th class="text-left py-1 px-2">Status</th>
                                            <th class="text-left py-1 px-2">Risk</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                    `;
                    data.history.slice(0, 5).forEach(attempt => {
                        const status = attempt.is_successful ? '✅ Success' : '❌ Failed';
                        const isSuspicious = attempt.is_suspicious ? '⚠️ Suspicious' : '✅ Normal';
                        html += `
                            <tr>
                                <td class="py-1 px-2 text-gray-500">${new Date(attempt.attempted_at).toLocaleString()}</td>
                                <td class="py-1 px-2 text-gray-500">${attempt.ip_address || 'N/A'}</td>
                                <td class="py-1 px-2">${status}</td>
                                <td class="py-1 px-2">${isSuspicious}</td>
                            </tr>
                        `;
                    });
                    html += `
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    `;
                }

                // Role update form
                html += `
                    <div class="bg-indigo-50 rounded-lg p-4 mt-4 border border-indigo-200">
                        <h4 class="font-medium text-gray-900 mb-2">🔄 Update Role</h4>
                        <form onsubmit="updateRole(event, ${user.id})" class="flex flex-wrap items-end gap-3">
                            <div class="flex-1 min-w-[150px]">
                                <label class="block text-xs font-medium text-gray-700 mb-1">Role</label>
                                <select name="role" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                    @foreach($roles as $key => $label)
                                        <option value="{{ $key }}" ${user.role === '{{ $key }}' ? 'selected' : ''}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Admin</label>
                                <input type="checkbox" name="is_admin" value="1" ${user.is_admin ? 'checked' : ''} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            </div>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">
                                Update Role
                            </button>
                        </form>
                    </div>
                `;

                content.innerHTML = html;
            })
            .catch(error => {
                content.innerHTML = `
                    <div class="text-center py-8">
                        <p class="text-rose-600">Error loading user details.</p>
                        <p class="text-sm text-gray-500 mt-2">${error.message}</p>
                    </div>
                `;
                showToast(error.message, 'error');
            });
        }

        // ── Toggle Active ──
        function toggleActive(userId) {
            if (!confirm('Are you sure you want to toggle this user\'s active status?')) return;
            
            fetch(`/admin/users/${userId}/toggle-active`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCSRFToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    showToast(result.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(result.message, 'error');
                }
            })
            .catch(() => showToast('An error occurred', 'error'));
        }

        // ── Toggle Lock ──
        function toggleLock(userId) {
            const reason = prompt('Enter lock reason (optional):');
            if (reason === null) return;
            
            fetch(`/admin/users/${userId}/toggle-lock`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCSRFToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ reason: reason || 'Locked by admin' })
            })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    showToast(result.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(result.message, 'error');
                }
            })
            .catch(() => showToast('An error occurred', 'error'));
        }

        // ── Delete User ──
        function deleteUser(userId) {
            if (!confirm('⚠️ Are you sure you want to delete this user? This action cannot be undone.')) return;
            
            if (!confirm('Really? This will permanently remove the user account.')) return;
            
            fetch(`/admin/users/${userId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCSRFToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    showToast(result.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(result.message, 'error');
                }
            })
            .catch(() => showToast('An error occurred', 'error'));
        }

        // ── Update Role ──
        function updateRole(event, userId) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            
            fetch(`/admin/users/${userId}/role`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCSRFToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    role: formData.get('role'),
                    is_admin: formData.has('is_admin') ? 1 : 0
                })
            })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    showToast(result.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(result.message, 'error');
                }
            })
            .catch(() => showToast('An error occurred', 'error'));
        }

        // ── Block IP ──
        function openBlockIpModal() {
            openModal('blockIpModal');
            document.getElementById('blockIpForm').reset();
        }

        function submitBlockIp(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            
            fetch(`/admin/ips/block`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCSRFToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    ip_address: formData.get('ip_address'),
                    reason: formData.get('reason'),
                    unblocks_at: formData.get('unblocks_at') || null
                })
            })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    showToast(result.message, 'success');
                    closeModal('blockIpModal');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(result.message || 'Failed to block IP', 'error');
                }
            })
            .catch(() => showToast('An error occurred', 'error'));
        }

        // ── Bulk Actions ──
        function toggleAllUsers() {
            const checked = document.getElementById('selectAll').checked;
            document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = checked);
            updateBulkActions();
        }

        function updateBulkActions() {
            const selected = document.querySelectorAll('.user-checkbox:checked');
            const count = selected.length;
            const bar = document.getElementById('bulkActions');
            
            if (count > 0) {
                bar.classList.remove('hidden');
                document.getElementById('selectedCount').textContent = count;
            } else {
                bar.classList.add('hidden');
            }
        }

        function clearSelection() {
            document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = false);
            document.getElementById('selectAll').checked = false;
            updateBulkActions();
        }

        function executeBulkAction() {
            const selected = document.querySelectorAll('.user-checkbox:checked');
            const action = document.getElementById('bulkActionSelect').value;
            
            if (!action) {
                showToast('Please select an action', 'error');
                return;
            }
            
            if (selected.length === 0) {
                showToast('No users selected', 'error');
                return;
            }
            
            if (!confirm(`Are you sure you want to ${action} ${selected.length} user(s)?`)) return;
            
            const userIds = Array.from(selected).map(cb => cb.value);
            
            fetch('/admin/users/bulk-action', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCSRFToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ user_ids: userIds, action: action })
            })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    showToast(result.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(result.message || 'Bulk action failed', 'error');
                }
            })
            .catch(() => showToast('An error occurred', 'error'));
        }

        // ── Close modals on backdrop click ──
        document.querySelectorAll('.modal-backdrop').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            });
        });

        // ── Close modals on Escape key ──
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-backdrop:not(.hidden)').forEach(modal => {
                    modal.classList.add('hidden');
                    document.body.style.overflow = '';
                });
            }
        });

        console.log('👥 User Management System loaded');
    </script>
</x-app-layout>