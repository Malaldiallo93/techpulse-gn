<?php

namespace App\Models;

use App\Support\TechPulse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class GlossaryTerm extends Model
{
    protected $guarded = [];

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class);
    }

    public function getLetterAttribute(): string
    {
        return strtoupper(TechPulse::norm($this->term)[0]);
    }

    /** Nombre d'articles publiés où le terme apparaît. */
    public function getReadCountAttribute(): int
    {
        return $this->articles_count ?? $this->articles()->where('status', 'published')->count();
    }
}
