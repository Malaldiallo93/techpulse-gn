<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewFlag extends Model
{
    protected $guarded = [];

    public const STATES = ['open', 'fixed', 'removed', 'ok'];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
