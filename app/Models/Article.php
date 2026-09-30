<?php

namespace App\Models;

use App\Support\TechPulse;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Concerns\FrenchTypography;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    use FrenchTypography;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'key_points' => 'array',
            'body' => 'array',
            'tags' => 'array',
            'source_paragraphs' => 'array',
            'draft_blocks' => 'array',
            'draft_edits' => 'array',
            'source_date' => 'date',
            'published_at' => 'datetime',
            'ai_drafted_at' => 'datetime',
            'source_checked' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('status', 'published')->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function flags(): HasMany
    {
        return $this->hasMany(ReviewFlag::class)->orderBy('position');
    }

    public function terms(): BelongsToMany
    {
        return $this->belongsToMany(GlossaryTerm::class);
    }

    public function getThemeLabelAttribute(): string
    {
        return TechPulse::themeLabel($this->theme);
    }

    public function getLevelLabelAttribute(): string
    {
        return TechPulse::levelLabel($this->level);
    }

    public function url(): string
    {
        return route('articles.show', $this);
    }

    /** Termes du glossaire référencés dans le corps : [[slug|texte]]. */
    public function termSlugs(): array
    {
        $text = implode("\n", array_merge($this->body ?? [], $this->key_points ?? [], [$this->essential, $this->why]));
        preg_match_all('/\[\[([a-z0-9-]+)\|/', $text, $m);

        return array_values(array_unique($m[1]));
    }

    public function syncTerms(): void
    {
        $ids = GlossaryTerm::whereIn('slug', $this->termSlugs())->pluck('id');
        $this->terms()->sync($ids);
    }

    // ---------- Relecture ----------

    /** Le bloc réécrit à la main neutralise le signalement qu'il contient. */
    public function flagBlock(string $key): ?string
    {
        foreach ($this->draft_blocks ?? [] as $b) {
            foreach ($b['segs'] as $s) {
                if (($s['f'] ?? null) === $key) {
                    return $b['id'];
                }
            }
        }

        return null;
    }

    public function effectiveFlagState(ReviewFlag $f): string
    {
        $block = $this->flagBlock($f->key);
        if ($block !== null && isset(($this->draft_edits ?? [])[$block])) {
            return 'edited';
        }

        return $f->state;
    }

    public function openFlagsCount(): int
    {
        return $this->flags->filter(fn ($f) => $this->effectiveFlagState($f) === 'open')->count();
    }

    public function canPublish(): bool
    {
        return $this->status === 'review' && $this->openFlagsCount() === 0 && $this->source_checked;
    }

    /** Texte d'un bloc, en appliquant l'état des signalements (remplacé, retiré). */
    public function blockText(array $block): string
    {
        $edits = $this->draft_edits ?? [];
        if (isset($edits[$block['id']])) {
            return $edits[$block['id']];
        }
        $flags = $this->flags->keyBy('key');
        $out = '';
        foreach ($block['segs'] as $s) {
            if (isset($s['f'])) {
                $f = $flags[$s['f']] ?? null;
                if (! $f || $f->state === 'removed') {
                    continue;
                }
                $out .= $f->state === 'fixed' ? $f->fix : $f->flagged;
            } else {
                $out .= $s['t'];
            }
        }
        $out = preg_replace('/\s+([.,])/u', '$1', $out);
        $out = preg_replace('/,(\s*,)+/u', ',', $out);
        $out = preg_replace('/,\s*\./u', '.', $out);
        $out = preg_replace('/:\s*\./u', '.', $out);

        return trim(preg_replace('/\s{2,}/u', ' ', $out));
    }

    /** Recopie le brouillon validé dans les champs lus par le public. */
    public function compileDraft(): void
    {
        $blocks = collect($this->draft_blocks ?? [])->keyBy('id');
        if ($blocks->isEmpty()) {
            return;
        }
        $this->essential = $this->blockText($blocks['chapo']);
        $this->key_points = $blocks->filter(fn ($b, $id) => str_starts_with($id, 'p'))->map(fn ($b) => $this->blockText($b))->values()->all();
        $this->why = $this->blockText($blocks['why']);
        $this->summary ??= $this->essential;
    }
}
