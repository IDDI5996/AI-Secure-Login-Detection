<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Uploaded Notes') }}
            </h2>
            <div class="flex items-center gap-3">
                <!-- Export Button -->
                <button onclick="exportNotes()" 
                        class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export CSV
                </button>
                <a href="{{ route('lecturer.notes.create') }}" 
                   class="inline-flex items-center justify-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200 transform hover:-translate-y-0.5">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Upload New Note
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Success Message -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-400 text-green-700 rounded-r-lg shadow-sm" role="alert">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Notes</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $notes->total() }}</p>
                        </div>
                        <div class="bg-primary-100 rounded-full p-3">
                            <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Active Notes</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $notes->where('is_active', true)->count() }}</p>
                        </div>
                        <div class="bg-green-100 rounded-full p-3">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Downloads</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $notes->sum('downloads_count') }}</p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-3">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Courses Covered</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $notes->pluck('course_id')->unique()->count() }}</p>
                        </div>
                        <div class="bg-purple-100 rounded-full p-3">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filter Bar -->
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="relative flex-1 max-w-md">
                    <label for="note-search" class="sr-only">Search notes</label>
                    <input type="text" id="note-search" placeholder="Search by title, course, or description..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                           aria-label="Search notes">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <select id="status-filter" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 text-sm">
                        <option value="all">All Status</option>
                        <option value="active">Active Only</option>
                        <option value="inactive">Inactive Only</option>
                    </select>
                    <div class="text-sm text-gray-500">
                        Showing <span id="visible-count">{{ $notes->count() }}</span> of {{ $notes->total() }} notes
                    </div>
                </div>
            </div>

            <!-- Notes Table -->
            @if($notes->count())
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Downloads</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="notes-table-body">
                                @foreach($notes as $note)
                                <tr class="note-row hover:bg-gray-50 transition duration-150"
                                    data-title="{{ strtolower($note->title) }}"
                                    data-course="{{ strtolower($note->course->code . ' ' . $note->course->name) }}"
                                    data-description="{{ strtolower($note->description ?? '') }}"
                                    data-status="{{ $note->is_active ? 'active' : 'inactive' }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 rounded-full bg-primary-100 flex items-center justify-center mr-3">
                                                <span class="text-xs font-bold text-primary-600">{{ substr($note->course->code, 0, 2) }}</span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $note->course->code }}</p>
                                                <p class="text-xs text-gray-500">{{ Str::limit($note->course->name, 30) }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $note->title }}</div>
                                        @if($note->description)
                                            <div class="text-xs text-gray-500 mt-1">{{ Str::limit($note->description, 50) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-600">{{ $note->formatted_size }}</span>
                                        <div class="text-xs text-gray-400">{{ strtoupper(pathinfo($note->file_name, PATHINFO_EXTENSION)) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 text-gray-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                            <span class="text-sm font-medium text-gray-700">{{ $note->downloads_count }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $note->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $note->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-3">
                                            <a href="{{ route('lecturer.notes.edit', $note) }}" 
                                               class="text-primary-600 hover:text-primary-900 transition-colors"
                                               aria-label="Edit {{ $note->title }}">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <form action="{{ route('lecturer.notes.destroy', $note) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this note? This action cannot be undone.')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 transition-colors" aria-label="Delete {{ $note->title }}">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $notes->links() }}
                    </div>
                </div>
            @else
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No notes uploaded yet</h3>
                    <p class="mt-2 text-sm text-gray-500 max-w-sm mx-auto">
                        Get started by uploading your first course note. Your students will be able to access it immediately.
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('lecturer.notes.create') }}" 
                           class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Upload Your First Note
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Live Search and Filter Script -->
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('note-search');
            const statusFilter = document.getElementById('status-filter');
            const rows = document.querySelectorAll('.note-row');
            const visibleCountSpan = document.getElementById('visible-count');
            
            function filterNotes() {
                const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
                const statusValue = statusFilter ? statusFilter.value : 'all';
                let visibleCount = 0;
                
                rows.forEach(row => {
                    const title = row.dataset.title || '';
                    const course = row.dataset.course || '';
                    const description = row.dataset.description || '';
                    const rowStatus = row.dataset.status || '';
                    
                    const matchesSearch = title.includes(searchTerm) || course.includes(searchTerm) || description.includes(searchTerm);
                    const matchesStatus = statusValue === 'all' || rowStatus === statusValue;
                    const isVisible = matchesSearch && matchesStatus;
                    
                    row.style.display = isVisible ? '' : 'none';
                    if (isVisible) visibleCount++;
                });
                
                // Update visible count
                if (visibleCountSpan) {
                    visibleCountSpan.textContent = visibleCount;
                }
                
                // Show/hide "no results" message
                let noResultsMsg = document.getElementById('no-search-results');
                if (visibleCount === 0 && rows.length > 0) {
                    if (!noResultsMsg) {
                        noResultsMsg = document.createElement('tr');
                        noResultsMsg.id = 'no-search-results';
                        noResultsMsg.innerHTML = '<td colspan="6" class="px-6 py-12 text-center text-gray-500">📭 No notes match your search criteria.</td>';
                        document.getElementById('notes-table-body').appendChild(noResultsMsg);
                    }
                } else if (noResultsMsg) {
                    noResultsMsg.remove();
                }
            }
            
            if (searchInput) searchInput.addEventListener('input', filterNotes);
            if (statusFilter) statusFilter.addEventListener('change', filterNotes);
        });

        // Export notes to CSV
        function exportNotes() {
            const rows = document.querySelectorAll('.note-row');
            const csvData = [['Course Code', 'Course Name', 'Title', 'Description', 'File Size', 'Downloads', 'Status', 'Uploaded At']];
            
            rows.forEach(row => {
                if (row.style.display !== 'none') {
                    const courseCode = row.querySelector('td:first-child .text-sm.font-medium')?.innerText || '';
                    const courseName = row.querySelector('td:first-child .text-xs.text-gray-500')?.innerText || '';
                    const title = row.querySelector('td:nth-child(2) .text-sm.font-medium')?.innerText || '';
                    const description = row.querySelector('td:nth-child(2) .text-xs.text-gray-500')?.innerText || '';
                    const fileSize = row.querySelector('td:nth-child(3) .text-sm')?.innerText || '';
                    const downloads = row.querySelector('td:nth-child(4) .text-sm.font-medium')?.innerText || '0';
                    const status = row.querySelector('td:nth-child(5) span')?.innerText || '';
                    const uploadedAt = row.querySelector('td:nth-child(2) .text-xs.text-gray-500:last-child')?.innerText || '';
                    
                    csvData.push([courseCode, courseName, title, description, fileSize, downloads, status, uploadedAt]);
                }
            });
            
            const csvContent = csvData.map(row => row.map(cell => `"${cell}"`).join(',')).join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `notes_export_{{ date('Y-m-d_H-i-s') }}.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }
    </script>
    @endpush
</x-app-layout>