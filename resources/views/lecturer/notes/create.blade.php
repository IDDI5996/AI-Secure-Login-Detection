<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Upload New Note') }}
            </h2>
            <a href="{{ route('lecturer.notes.index') }}" 
               class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to My Notes
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <!-- Form Header -->
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-primary-100 rounded-lg p-2">
                            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-lg font-medium text-gray-900">Note Information</h3>
                            <p class="text-sm text-gray-500">Fill in the details below to share course materials with your students.</p>
                        </div>
                    </div>
                </div>

                <!-- Form Body -->
                <div class="p-6">
                    <form action="{{ route('lecturer.notes.store') }}" method="POST" enctype="multipart/form-data" id="upload-form">
                        @csrf

                        <!-- Course Selection -->
                        <div class="mb-6">
                            <label for="course_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Course <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select name="course_id" id="course_id" required
                                        class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md transition duration-150">
                                    <option value="">Select a course</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                            {{ $course->code }} – {{ $course->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                            @error('course_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Title -->
                        <div class="mb-6">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                                Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="title" required maxlength="200"
                                   value="{{ old('title') }}"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 sm:text-sm transition duration-150"
                                   placeholder="e.g., Introduction to Ethical Hacking - Week 1">
                            <div class="flex justify-between items-center mt-1">
                                <p class="text-xs text-gray-500">Max 200 characters</p>
                                <p class="text-xs text-gray-500"><span id="title-count">0</span>/200</p>
                            </div>
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                Description <span class="text-gray-400 text-xs">(optional)</span>
                            </label>
                            <textarea name="description" id="description" rows="4" maxlength="1000"
                                      class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 sm:text-sm transition duration-150"
                                      placeholder="Provide additional context or instructions for students...">{{ old('description') }}</textarea>
                            <div class="flex justify-between items-center mt-1">
                                <p class="text-xs text-gray-500">Brief description of the note content</p>
                                <p class="text-xs text-gray-500"><span id="desc-count">0</span>/1000</p>
                            </div>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- File Upload Area -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                File <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-primary-400 transition-colors duration-150"
                                 id="dropzone">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="note_file" class="relative cursor-pointer bg-white rounded-md font-medium text-primary-600 hover:text-primary-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary-500">
                                            <span>Upload a file</span>
                                            <input id="note_file" name="note_file" type="file" class="sr-only" accept=".pdf,.doc,.docx,.ppt,.pptx,.txt" required>
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        PDF, DOC, DOCX, PPT, PPTX, TXT up to 20MB
                                    </p>
                                    <div id="file-info" class="hidden mt-2 text-sm text-gray-700"></div>
                                </div>
                            </div>
                            @error('note_file')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                            <a href="{{ route('lecturer.notes.index') }}" 
                               class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition duration-150">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition duration-150 transform hover:-translate-y-0.5">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                Upload Note
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Helpful Tips Card -->
            <div class="mt-6 bg-blue-50 rounded-lg border border-blue-200 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Upload Guidelines</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Use clear, descriptive titles so students can easily find materials.</li>
                                <li>Keep file sizes under 20MB for optimal performance.</li>
                                <li>Supported formats: PDF, Word documents, PowerPoint presentations, and text files.</li>
                                <li>You can edit or delete uploaded notes later from your dashboard.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for Character Counters, File Preview, and Drag & Drop -->
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Character counters
            const titleInput = document.getElementById('title');
            const descInput = document.getElementById('description');
            const titleCount = document.getElementById('title-count');
            const descCount = document.getElementById('desc-count');

            if (titleInput && titleCount) {
                titleCount.textContent = titleInput.value.length;
                titleInput.addEventListener('input', function() {
                    titleCount.textContent = this.value.length;
                });
            }

            if (descInput && descCount) {
                descCount.textContent = descInput.value.length;
                descInput.addEventListener('input', function() {
                    descCount.textContent = this.value.length;
                });
            }

            // File input handling
            const fileInput = document.getElementById('note_file');
            const fileInfo = document.getElementById('file-info');
            const dropzone = document.getElementById('dropzone');
            const MAX_SIZE = 20 * 1024 * 1024; // 20MB

            function updateFileInfo(file) {
                if (!file) {
                    fileInfo.classList.add('hidden');
                    fileInfo.innerHTML = '';
                    return;
                }

                const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
                const isValid = file.size <= MAX_SIZE;
                const icon = isValid ? '✅' : '❌';
                const colorClass = isValid ? 'text-green-700' : 'text-red-700';
                
                fileInfo.innerHTML = `
                    <div class="flex items-center justify-center space-x-2 ${colorClass}">
                        <span>${icon}</span>
                        <span>${file.name}</span>
                        <span class="text-xs">(${sizeMB} MB)</span>
                        ${!isValid ? '<span class="text-xs">- Exceeds 20MB limit</span>' : ''}
                    </div>
                `;
                fileInfo.classList.remove('hidden');
                
                if (!isValid) {
                    fileInput.setCustomValidity('File size must not exceed 20MB.');
                } else {
                    fileInput.setCustomValidity('');
                }
            }

            fileInput.addEventListener('change', function(e) {
                if (this.files && this.files[0]) {
                    updateFileInfo(this.files[0]);
                } else {
                    updateFileInfo(null);
                }
            });

            // Drag & Drop functionality
            if (dropzone) {
                dropzone.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    this.classList.add('border-primary-500', 'bg-primary-50');
                });

                dropzone.addEventListener('dragleave', function(e) {
                    e.preventDefault();
                    this.classList.remove('border-primary-500', 'bg-primary-50');
                });

                dropzone.addEventListener('drop', function(e) {
                    e.preventDefault();
                    this.classList.remove('border-primary-500', 'bg-primary-50');
                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        fileInput.files = files;
                        updateFileInfo(files[0]);
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>