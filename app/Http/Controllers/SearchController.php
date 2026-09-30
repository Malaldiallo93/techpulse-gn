<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Course;
use App\Models\GlossaryTerm;
use App\Models\Lesson;
use App\Models\Opportunity;
use App\Support\TechPulse;

class SearchController extends Controller
{
    public function index()
    {
        return view('pages.search');
    }

    /**
     * Index compact de tout le site (quelques Ko), mis en cache par le service worker :
     * la recherche se fait sur le téléphone, à chaque frappe, même hors ligne.
     * Clés courtes : t=type, ti=titre, th=thème, m=méta, k=mots-clés, u=url, d=date limite (ms), x=texte.
     */
    public function data()
    {
        $items = [];
        foreach (Article::published()->latest('published_at')->get() as $a) {
            $items[] = ['t' => 'a', 'ti' => $a->title, 'th' => $a->theme, 'u' => $a->url(),
                'm' => $a->level_label.' · '.$a->reading_minutes.' min · '.TechPulse::relativeDay($a->published_at),
                'k' => TechPulse::norm(implode(' ', $a->tags ?? []).' '.ArticleController::plain($a->summary.' '.$a->essential.' '.implode(' ', $a->key_points ?? []).' '.implode(' ', $a->body ?? [])))];
        }
        foreach (Opportunity::open()->get() as $o) {
            $items[] = ['t' => 'o', 'ti' => $o->title, 'th' => 'opp', 'u' => route('opportunities.show', $o),
                'm' => $o->type.' · '.$o->money.' · '.$o->level_label, 'd' => $o->deadline_at->getTimestampMs(),
                'k' => TechPulse::norm($o->type.' '.$o->org.' '.$o->place.' '.$o->description)];
        }
        foreach (Course::with('lessons')->orderBy('position')->get() as $c) {
            $items[] = ['t' => 'p', 'ti' => $c->title, 'th' => $c->theme, 'u' => route('courses.show', $c),
                'm' => 'Parcours · '.$c->lessons->count().' leçons · '.TechPulse::duration($c->totalMinutes()).' · '.$c->level_label,
                'k' => TechPulse::norm($c->description.' '.$c->lessons->pluck('title')->implode(' '))];
        }
        foreach (Lesson::with('course')->where('kind', 'Leçon')->orderBy('course_id')->orderBy('position')->get() as $l) {
            $items[] = ['t' => 't', 'ti' => $l->title, 'th' => $l->course->theme, 'u' => route('courses.lesson', [$l->course, $l->position]),
                'm' => 'Tutoriel · '.$l->minutes.' min · '.$l->course->level_label,
                'k' => TechPulse::norm($l->summary.' '.$l->course->title)];
        }
        foreach (GlossaryTerm::orderBy('term')->get() as $g) {
            $items[] = ['t' => 'g', 'ti' => $g->term, 'th' => $g->theme, 'u' => route('glossary').'#'.$g->slug,
                'x' => $g->definition, 'k' => TechPulse::norm($g->english.' '.$g->keywords)];
        }

        return response()->json($items, 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            ->header('Cache-Control', 'public, max-age=600');
    }
}
