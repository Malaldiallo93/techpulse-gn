<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseProgress;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $courses = Course::withCount('lessons')->withSum('lessons', 'minutes')->orderBy('position')->get();
        $progress = $this->progressMap($request);

        // « Reprendre » : le dernier parcours entamé sur cet appareil, sinon le premier du catalogue.
        $resumeRow = CourseProgress::where('device_id', $request->attributes->get('device'))
            ->where('done', '>', 0)->latest('updated_at')->get()
            ->first(fn ($p) => $p->done < ($courses->firstWhere('id', $p->course_id)?->lessons_count ?? 0));
        $resume = $resumeRow ? $courses->firstWhere('id', $resumeRow->course_id) : null;

        return view('pages.courses', [
            'courses' => $courses,
            'progress' => $progress,
            'resume' => $resume?->load('lessons'),
            'starter' => $resume ? null : $courses->first()?->load('lessons'),
        ]);
    }

    public function show(Request $request, Course $course)
    {
        $course->load('lessons');

        return view('pages.course', [
            'c' => $course,
            'done' => min($this->progressMap($request)[$course->id] ?? 0, $course->lessons->count()),
        ]);
    }

    public function lesson(Request $request, Course $course, int $position)
    {
        $course->load('lessons');
        $lesson = $course->lessons->firstWhere('position', $position) ?? abort(404);

        return view('pages.lesson', [
            'c' => $course,
            'l' => $lesson,
            'done' => $this->progressMap($request)[$course->id] ?? 0,
            'next' => $course->lessons->firstWhere('position', $position + 1),
        ]);
    }

    /**
     * Enregistre la progression. `done` = nombre de leçons terminées (jamais en recul,
     * sauf remise à zéro explicite) : la version locale et celle du serveur se rejoignent.
     */
    public function progress(Request $request, Course $course)
    {
        $total = $course->lessons()->count();
        $data = $request->validate(['done' => ['nullable', 'integer', 'min:0', 'max:'.$total], 'reset' => ['nullable', 'boolean']]);
        $row = CourseProgress::firstOrNew(['course_id' => $course->id, 'device_id' => $request->attributes->get('device')]);
        $current = (int) $row->done;
        $row->done = $request->boolean('reset') ? 0 : max($current, (int) ($data['done'] ?? $current + 1));
        $row->done = min($row->done, $total);
        $row->save();

        if ($request->expectsJson()) {
            return response()->json(['done' => $row->done, 'total' => $total]);
        }

        return redirect()->route('courses.show', $course);
    }

    /** @return array<int,int> course_id => leçons terminées, pour cet appareil. */
    private function progressMap(Request $request): array
    {
        return CourseProgress::where('device_id', $request->attributes->get('device'))->pluck('done', 'course_id')->all();
    }
}
