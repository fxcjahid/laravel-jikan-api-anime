<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AnimeController extends Controller
{
    public function index(Request $request)
    {
        $query = Anime::query();

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->whereRaw("JSON_EXTRACT(titles, '$.en') LIKE ?", ['%' . $search . '%'])
                    ->orWhereRaw("JSON_EXTRACT(titles, '$.pl') LIKE ?", ['%' . $search . '%']);
            });
        }

        // Sort functionality
        $sort      = $request->get('sort', 'rank');
        $direction = $request->get('direction', 'asc');
        $query->orderBy($sort, $direction);

        $animes = $query->paginate(20)->withQueryString();

        return view('animes.index', compact('animes'));
    }

    public function show(Anime $anime)
    {
        return view('animes.show', compact('anime'));
    }
}
