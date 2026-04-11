<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NoteController extends Controller
{
    public function index()
    {
        $notes = Note::with(['course', 'uploader'])
            ->where('uploaded_by', auth()->id())
            ->latest()
            ->paginate(15);
        return view('lecturer.notes.index', compact('notes'));
    }

    public function create()
    {
        $courses = Course::orderBy('code')->get();
        return view('lecturer.notes.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'note_file' => 'required|file|max:20480',
        ]);

        $file = $request->file('note_file');
        $path = $file->store('notes', 'public');

        Note::create([
            'course_id' => $request->course_id,
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'uploaded_by' => auth()->id(),
            'downloads_count' => 0,
            'is_active' => true,
        ]);

        return redirect()->route('lecturer.notes.index')
            ->with('success', 'Note uploaded successfully.');
    }

    public function edit(Note $note)
    {
        if ($note->uploaded_by !== auth()->id() && !auth()->user()->is_admin) {
            abort(403);
        }
        $courses = Course::orderBy('code')->get();
        return view('lecturer.notes.edit', compact('note', 'courses'));
    }

    public function update(Request $request, Note $note)
    {
        if ($note->uploaded_by !== auth()->id() && !auth()->user()->is_admin) {
            abort(403);
        }

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $note->update($request->only(['course_id', 'title', 'description', 'is_active']));

        if ($request->hasFile('note_file')) {
            Storage::disk('public')->delete($note->file_path);
            $file = $request->file('note_file');
            $path = $file->store('notes', 'public');
            $note->update([
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);
        }

        return redirect()->route('lecturer.notes.index')
            ->with('success', 'Note updated.');
    }

    public function destroy(Note $note)
    {
        if ($note->uploaded_by !== auth()->id() && !auth()->user()->is_admin) {
            abort(403);
        }
        Storage::disk('public')->delete($note->file_path);
        $note->delete();
        return redirect()->route('lecturer.notes.index')
            ->with('success', 'Note deleted.');
    }
}