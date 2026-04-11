<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NoteController extends Controller
{
    public function dashboard()
    {
        $courses = Course::withCount(['notes' => function ($q) {
            $q->where('is_active', true);
        }])->orderBy('code')->get();

        $recentNotes = Note::where('is_active', true)
            ->with('course')
            ->latest()
            ->limit(5)
            ->get();

        return view('student.dashboard', compact('courses', 'recentNotes'));
    }

    public function courseNotes(Course $course)
    {
        $notes = $course->notes()->where('is_active', true)->latest()->paginate(10);
        return view('student.course-notes', compact('course', 'notes'));
    }

    public function download(Note $note)
    {
        if (!$note->is_active) {
            abort(404);
        }
        $note->increment('downloads_count');
        return Storage::disk('public')->download($note->file_path, $note->file_name);
    }
}