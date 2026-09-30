<?php

namespace App\Models;

use App\Support\TechPulse;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Concerns\FrenchTypography;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Opportunity extends Model
{
    use FrenchTypography;

    protected $guarded = [];

    public const TYPES = ['Bourse', 'Stage', 'Emploi', 'Formation', 'Concours'];

    protected function casts(): array
    {
        return [
            'countries' => 'array',
            'conditions' => 'array',
            'documents' => 'array',
            'deadline_at' => 'datetime',
            'published' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeOpen(Builder $q): Builder
    {
        return $q->where('published', true)->where('deadline_at', '>', now())->orderBy('deadline_at');
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(OpportunityReminder::class);
    }

    public function countdown(): array
    {
        return TechPulse::countdown($this->deadline_at);
    }

    public function getLevelLabelAttribute(): string
    {
        return TechPulse::levelLabel($this->level);
    }

    public function getCountriesLabelAttribute(): string
    {
        return implode(', ', $this->countries);
    }

    public function getDeadlineLabelAttribute(): string
    {
        return $this->deadline_at->locale('fr')->translatedFormat('j F');
    }

    public function getIsFreeAttribute(): bool
    {
        return $this->money === 'Gratuit';
    }

    /** Libellé court de la carte : type affiché (« Hackathon » pour un concours de 48 h, etc.). */
    public function getBadgeAttribute(): string
    {
        return $this->kind_label ?: $this->type;
    }
}
