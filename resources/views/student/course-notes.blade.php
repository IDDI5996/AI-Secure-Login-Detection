<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center space-x-3">
                    <div class="h-10 w-10 rounded-full {{ $course->type == 'core' ? 'bg-primary-100' : 'bg-purple-100' }} flex items-center justify-center">
                        <span class="text-lg font-bold {{ $course->type == 'core' ? 'text-primary-600' : 'text-purple-600' }}">
                            {{ substr($course->code, 0, 2) }}
                        </span>
                    </div>
                    <div>
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                            {{ $course->code }}: {{ $course->name }}
                        </h2>
                        <p class="text-sm text-gray-500 mt-0.5">
                            {{ $course->credits }} credits • {{ ucfirst($course->type) }} course
                        </p>
                    </div>
                </div>
            </div>
            <a href="{{ route('student.dashboard') }}" 
               class="inline-flex items-center text-sm text-primary-600 hover:text-primary-700 transition-colors duration-200">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Course Stats Bar -->
            <div class="mb-8 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="grid grid-cols-2 md:grid-cols-4 divide-x divide-gray-200">
                    <div class="p-4 text-center">
                        <div class="text-2xl font-bold text-primary-600">{{ $notes->total() }}</div>
                        <div class="text-xs text-gray-500 mt-1">Total Notes</div>
                    </div>
                    <div class="p-4 text-center">
                        <div class="text-2xl font-bold text-gray-800">{{ $notes->sum('downloads_count') }}</div>
                        <div class="text-xs text-gray-500 mt-1">Total Downloads</div>
                    </div>
                    <div class="p-4 text-center">
                        <div class="text-2xl font-bold text-gray-800">{{ $notes->where('is_active', true)->count() }}</div>
                        <div class="text-xs text-gray-500 mt-1">Active Notes</div>
                    </div>
                    <div class="p-4 text-center">
                        <div class="text-sm font-medium text-gray-600">
                            @if($notes->isNotEmpty())
                                Last update: {{ $notes->first()->created_at->format('M d, Y') }}
                            @else
                                No notes yet
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search & Filter for Notes -->
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="relative flex-1 max-w-md">
                    <label for="note-search" class="sr-only">Search notes</label>
                    <input type="text" id="note-search" placeholder="Search by title or description..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                           aria-label="Search notes">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
                <div class="text-sm text-gray-500">
                    Showing {{ $notes->firstItem() ?? 0 }} - {{ $notes->lastItem() ?? 0 }} of {{ $notes->total() }} notes
                </div>
            </div>

            <!-- Notes List -->
            @if($notes->count())
                <div class="space-y-4" id="notes-list">
                    @foreach($notes as $note)
                    <div class="note-item bg-white rounded-xl border border-gray-200 hover:shadow-lg hover:border-primary-200 transition-all duration-300 overflow-hidden"
                         data-note-title="{{ strtolower($note->title) }}"
                         data-note-description="{{ strtolower($note->description ?? '') }}">
                        <div class="p-5">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-center space-x-3">
                                            <!-- File type icon -->
                                            <div class="flex-shrink-0">
                                                @php
                                                    $extension = pathinfo($note->file_name, PATHINFO_EXTENSION);
                                                    $iconColor = match($extension) {
                                                        'pdf' => 'bg-red-100 text-red-600',
                                                        'doc', 'docx' => 'bg-blue-100 text-blue-600',
                                                        'ppt', 'pptx' => 'bg-orange-100 text-orange-600',
                                                        'txt' => 'bg-gray-100 text-gray-600',
                                                        default => 'bg-primary-100 text-primary-600',
                                                    };
                                                @endphp
                                                <div class="w-10 h-10 rounded-lg {{ $iconColor }} flex items-center justify-center">
                                                    <span class="text-xs font-bold uppercase">{{ substr($extension ?: 'file', 0, 3) }}</span>
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="font-semibold text-gray-800 hover:text-primary-600 transition-colors">
                                                    {{ $note->title }}
                                                </h4>
                                                <div class="flex flex-wrap items-center gap-3 mt-1 text-xs text-gray-500">
                                                    <span class="flex items-center">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                                        </svg>
                                                        {{ $note->formatted_size }}
                                                    </span>
                                                    <span class="flex items-center">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                        </svg>
                                                        {{ $note->downloads_count }} downloads
                                                    </span>
                                                    <span class="flex items-center">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                        {{ $note->created_at->format('M d, Y') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @if($note->description)
                                        <p class="text-sm text-gray-600 mt-3 pl-13 border-l-2 border-primary-200 ml-12 pl-3">
                                            {{ $note->description }}
                                        </p>
                                    @endif
                                </div>
                                
                                <div class="flex-shrink-0">
                                    <a href="{{ route('student.download', $note) }}" 
                                       class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200 transform hover:-translate-y-0.5"
                                       aria-label="Download {{ $note->title }}">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination with styling -->
                <div class="mt-8">
                    {{ $notes->links() }}
                </div>
            @else
                <div class="bg-white rounded-xl shadow-sm p-12 text-center border border-gray-200">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No notes available for this course yet</h3>
                    <p class="mt-2 text-sm text-gray-500 max-w-sm mx-auto">
                        Notes will appear here once uploaded by your lecturers. Check back later or contact your course instructor.
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('student.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-primary-700 bg-primary-100 hover:bg-primary-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            Browse Other Courses
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Live Search Script -->
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('note-search');
            const noteItems = document.querySelectorAll('.note-item');
            
            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    const query = e.target.value.toLowerCase().trim();
                    
                    noteItems.forEach(item => {
                        const title = item.dataset.noteTitle || '';
                        const description = item.dataset.noteDescription || '';
                        const matches = title.includes(query) || description.includes(query);
                        item.style.display = matches ? '' : 'none';
                    });
                    
                    // Optional: Show/hide a "no results" message
                    let visibleCount = 0;
                    noteItems.forEach(item => {
                        if (item.style.display !== 'none') visibleCount++;
                    });
                    
                    let noResultsMsg = document.getElementById('no-search-results');
                    if (visibleCount === 0 && noteItems.length > 0) {
                        if (!noResultsMsg) {
                            noResultsMsg = document.createElement('div');
                            noResultsMsg.id = 'no-search-results';
                            noResultsMsg.className = 'text-center py-8 text-gray-500';
                            noResultsMsg.innerHTML = '<p class="text-sm">No notes match your search.</p>';
                            document.getElementById('notes-list').after(noResultsMsg);
                        }
                    } else if (noResultsMsg) {
                        noResultsMsg.remove();
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>