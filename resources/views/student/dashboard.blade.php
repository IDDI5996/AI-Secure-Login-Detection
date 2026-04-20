<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Student Portal – Fourth Year, Semester 2 Notes') }}
            </h2>
            <div class="flex items-center gap-3">
                <!-- Notification Bell -->
                <div class="relative" x-data="{ open: false, newNotes: {{ session('new_notes_count', 0) }} }">
                    <button @click="open = !open" class="relative p-2 text-gray-500 hover:text-gray-700 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span x-show="newNotes > 0" x-text="newNotes" 
                              class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 min-w-[18px] text-center"></span>
                    </button>
                    <div x-show="open" @click.away="open = false" 
                         class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                        <div class="p-3 border-b border-gray-200 font-semibold text-gray-700">Notifications</div>
                        <div class="max-h-96 overflow-y-auto">
                            <div class="p-3 text-sm text-gray-500">🎉 New notes have been added to your courses!</div>
                            <div class="p-3 text-xs text-gray-400 border-t border-gray-100 text-center">
                                <a href="#" class="text-primary-600 hover:underline">Mark all as read</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-sm text-gray-500">
                    <span class="hidden sm:inline">Last updated: </span>{{ now()->format('M d, Y') }}
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Hero Section -->
            <div class="mb-10 bg-gradient-to-r from-primary-600 to-primary-800 rounded-2xl shadow-xl overflow-hidden">
                <div class="relative px-6 py-8 md:py-12 md:px-12">
                    <div class="absolute inset-0 opacity-10">
                        <svg class="absolute right-0 top-0 h-64 w-64" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                        </svg>
                    </div>
                    
                    <div class="relative z-10">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                            <div>
                                <h1 class="text-2xl md:text-3xl font-bold text-white">
                                    Welcome back, {{ auth()->user()->name }}!
                                </h1>
                                <p class="text-primary-100 mt-2 max-w-2xl">
                                    Access all your course materials for Semester 2. Stay updated with the latest notes and resources.
                                </p>
                            </div>
                            <div class="mt-4 md:mt-0">
                                <div class="flex space-x-4 text-white">
                                    <div class="text-center">
                                        <div class="text-3xl font-bold">{{ $courses->count() }}</div>
                                        <div class="text-sm text-primary-100">Courses</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-3xl font-bold">{{ $courses->sum('notes_count') }}</div>
                                        <div class="text-sm text-primary-100">Notes</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="mb-8">
                <div class="relative max-w-md">
                    <label for="course-search" class="sr-only">Search courses</label>
                    <input type="text" id="course-search" placeholder="Search by course code or name..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                           aria-label="Search courses">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Recent Uploads Section -->
            @if($recentNotes->count())
            <div class="mb-12">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-900 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Recently Added Notes
                    </h3>
                    <span class="text-xs text-gray-500">{{ $recentNotes->count() }} new</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @foreach($recentNotes as $note)
                    <div class="group bg-white rounded-xl border border-gray-200 hover:shadow-lg hover:border-primary-200 transition-all duration-300 overflow-hidden">
                        <div class="p-5">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                                            {{ $note->course->code }}
                                        </span>
                                        <span class="text-xs text-gray-500">{{ $note->created_at->diffForHumans() }}</span>
                                    </div>
                                    <a href="{{ route('student.course-notes', $note->course) }}" 
                                       class="block font-semibold text-gray-800 hover:text-primary-600 transition-colors duration-200">
                                        {{ $note->title }}
                                    </a>
                                    @if($note->description)
                                        <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ $note->description }}</p>
                                    @endif
                                    <div class="flex items-center mt-3 space-x-3 text-xs text-gray-500">
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
                                    </div>
                                </div>
                                <a href="{{ route('student.download', $note) }}" 
                                   class="ml-4 flex-shrink-0 p-2 text-primary-600 hover:text-primary-800 hover:bg-primary-50 rounded-full transition-colors duration-200"
                                   aria-label="Download {{ $note->title }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <div class="mb-12 bg-gray-50 rounded-xl p-8 text-center border border-gray-200">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No notes yet</h3>
                <p class="mt-1 text-sm text-gray-500">Check back later for newly uploaded materials.</p>
            </div>
            @endif

            <!-- Courses Grid -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-900 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        Your Courses
                    </h3>
                    <span class="text-xs text-gray-500">{{ $courses->count() }} total</span>
                </div>
                
                <!-- Course count badge for empty search -->
                <div id="search-feedback" class="text-center text-sm text-gray-500 mb-4 hidden">
                    No courses match your search.
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" id="courses-grid">
                    @forelse($courses as $course)
                    <a href="{{ route('student.course-notes', $course) }}" 
                       class="course-card block bg-white rounded-xl border border-gray-200 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden group"
                       data-course-code="{{ strtolower($course->code) }}" 
                       data-course-name="{{ strtolower($course->name) }}">
                        <div class="relative">
                            <div class="h-2 {{ $course->type == 'core' ? 'bg-primary-500' : 'bg-purple-500' }}"></div>
                            <div class="p-5">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $course->type == 'core' ? 'bg-primary-100 text-primary-800' : 'bg-purple-100 text-purple-800' }}">
                                            {{ ucfirst($course->type) }}
                                        </span>
                                        <p class="text-sm font-mono text-primary-600 mt-2">{{ $course->code }}</p>
                                    </div>
                                    <div class="flex items-center space-x-1 text-xs text-gray-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span>{{ $course->notes_count }} note{{ $course->notes_count != 1 ? 's' : '' }}</span>
                                    </div>
                                </div>
                                <h4 class="font-bold text-gray-800 mt-2 line-clamp-2 group-hover:text-primary-600 transition-colors">
                                    {{ $course->name }}
                                </h4>
                                <div class="flex items-center justify-between mt-4">
                                    <div class="flex items-center text-xs text-gray-500">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $course->credits }} credits
                                    </div>
                                    <div class="text-primary-600 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                    @empty
                    <div class="col-span-full text-center py-12 bg-gray-50 rounded-xl">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No courses found</h3>
                        <p class="mt-1 text-sm text-gray-500">Check back later for semester updates.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('course-search');
            const courseCards = document.querySelectorAll('.course-card');
            const searchFeedback = document.getElementById('search-feedback');
            
            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    const query = e.target.value.toLowerCase().trim();
                    let visibleCount = 0;
                    
                    courseCards.forEach(card => {
                        const code = card.dataset.courseCode || '';
                        const name = card.dataset.courseName || '';
                        const matches = code.includes(query) || name.includes(query);
                        card.style.display = matches ? '' : 'none';
                        if (matches) visibleCount++;
                    });
                    
                    if (searchFeedback) {
                        if (visibleCount === 0 && query !== '') {
                            searchFeedback.classList.remove('hidden');
                        } else {
                            searchFeedback.classList.add('hidden');
                        }
                    }
                });
            }
        });
    </script>
    @endpush

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</x-app-layout>