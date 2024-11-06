<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anime extends Model
{
    protected $fillable = [
        'mal_id',
        'titles',
        'slugs',
        'images',
        'synopsis',
        'type',
        'episodes',
        'score',
        'rank',
        'popularity',
        'status',
        'aired_from',
        'aired_to',
        'response',
    ];

    protected $casts = [
        'titles'     => 'json',
        'slugs'      => 'json',
        'images'     => 'json',
        'response'   => 'json',
        'aired_from' => 'date',
        'aired_to'   => 'date',
    ];
}
