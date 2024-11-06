@extends('layout')

@section('content')
    <div class="mx-auto px-4 pt-4 pb-8">
        <h1 class="text-3xl font-bold text-black mb-8">Top Anime List</h1>

        <!-- Search and Sort Form -->
        <form action="{{ route('animes.index') }}" method="GET" class="mb-6">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search anime..."
                        class="w-full px-4 py-2 rounded border">
                </div>
                <div class="flex gap-4">
                    <select name="sort" class="px-4 py-2 rounded border">
                        <option value="rank" {{ request('sort') === 'rank' ? 'selected' : '' }}>Rank</option>
                        <option value="score" {{ request('sort') === 'score' ? 'selected' : '' }}>Score</option>
                        <option value="popularity" {{ request('sort') === 'popularity' ? 'selected' : '' }}>Popularity
                        </option>
                    </select>
                    <select name="direction" class="px-4 py-2 rounded border">
                        <option value="asc" {{ request('direction') === 'asc' ? 'selected' : '' }}>Ascending</option>
                        <option value="desc" {{ request('direction') === 'desc' ? 'selected' : '' }}>Descending
                        </option>
                    </select>
                    <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Apply
                    </button>
                </div>
            </div>
        </form>

        <!-- Anime Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach ($animes as $anime)
                <div class="bg-white rounded-lg border hover:shadow-md overflow-hidden">
                    <img class="mb-5 w-full border-0 rounded-top-1 "
                        src="{{ $anime->images['jpg']['image_url'] ?? 'https://www.contentviewspro.com/wp-content/uploads/2017/07/default_image.png' }}"
                        alt="" srcset="">
                    <div class="p-4">
                        <div class="font-bold text-xl text-black mb-2">
                            {{ $anime->titles['en'] ?? 'unknown' }}
                        </div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-500">Rank: #{{ $anime->rank }}</span>
                            <span class="text-sm font-semibold text-blue-500">Score: {{ $anime->score }}</span>
                        </div>
                        <div class="text-sm text-gray-600">
                            Episodes: {{ $anime->episodes ?? 'N/A' }}
                        </div>
                        <a href="{{ route('animes.show', $anime) }}"
                            class="mt-4 inline-block px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm">
                            View Details
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $animes->links() }}
        </div>
    </div>
@endsection
