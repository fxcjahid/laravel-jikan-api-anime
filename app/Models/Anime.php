<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anime extends Model
{
    protected $fillable = [
        'mal_id',
        'titles',
        'slugs',
        'synopsis',
        'type',
        'episodes',
        'score',
        'rank',
        'popularity',
        'status',
        'aired_from',
        'aired_to',
    ];

    protected $casts = [
        'titles'     => 'array',
        'slugs'      => 'array',
        'aired_from' => 'date',
        'aired_to'   => 'date',
    ];
}
