<?php

namespace App\Http\Controllers\API;

use App\Models\Anime;
use App\Http\Controllers\Controller;

class AnimeController extends Controller
{
    public function show(string $slug)
    {
        $anime = Anime::where(function ($query) use ($slug) {
            $lang = request()->query('lang');
            $query->whereRaw("JSON_EXTRACT(slugs, '$." . $lang . "') = ?", [$slug]);
        })->first();

        if (! $anime) {
            return response()->json([
                'message' => 'Anime not found',
                'error'   => 'The requested anime slug does not exist in the specified language.',
            ], 404);
        }

        return response()->json($anime);
    }
}
