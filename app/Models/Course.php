<?php

namespace App\Models;

use App\Support\TechPulse;
use App\Models\Concerns\FrenchTypography;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use FrenchTypography;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['outcomes' => 'array'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('position');
    }

    public function getLevelLabelAttribute(): string
    {
        return TechPulse::levelLabel($this->level);
    }

    public function getThemeLabelAttribute(): string
    {
        return TechPulse::themeLabel($this->theme);
    }

    public function totalMinutes(): int
    {
        return (int) $this->lessons->sum('minutes');
    }
}
