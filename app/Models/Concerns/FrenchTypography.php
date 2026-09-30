<?php

namespace App\Models\Concerns;

use App\Support\TechPulse;
use Illuminate\Database\Eloquent\Casts\Attribute;

/** Espaces insécables avant « : ; ? ! » et dans les guillemets, pour les titres et résumés affichés. */
trait FrenchTypography
{
    protected function title(): Attribute
    {
        return Attribute::get(fn (?string $v) => TechPulse::typo($v));
    }

    protected function summary(): Attribute
    {
        return Attribute::get(fn (?string $v) => TechPulse::typo($v));
    }
}
