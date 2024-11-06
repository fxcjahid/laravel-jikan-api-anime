@extends('layout')

@section('title', $anime->titles['en'])

@section('content')
    <a href="{{ route('animes.index') }}" class="text-blue-500 hover:text-blue-600 mb-6 inline-block">
        ← Back to List
    </a>

    <div class="bg-white text-black p-6">
        <div class="mb-6">
            <h1 class="text-3xl font-bold mb-2">{{ $anime->titles['en'] }}</h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <h3 class="text-lg font-semibold mb-4">Details</h3>
                <dl class="grid grid-cols-2 gap-4">
                    <dt class="text-gray-600">Type:</dt>
                    <dd>{{ $anime->type }}</dd>

                    <dt class="text-gray-600">Episodes:</dt>
                    <dd>{{ $anime->episodes ?? 'N/A' }}</dd>

                    <dt class="text-gray-600">Status:</dt>
                    <dd>{{ $anime->status }}</dd>

                    <dt class="text-gray-600">Score:</dt>
                    <dd class="font-semibold text-blue-500">{{ $anime->score }}</dd>

                    <dt class="text-gray-600">Rank:</dt>
                    <dd>#{{ $anime->rank }}</dd>

                    <dt class="text-gray-600">Popularity:</dt>
                    <dd>#{{ $anime->popularity }}</dd>

                    <dt class="text-gray-600">Aired From:</dt>
                    <dd>{{ $anime->aired_from?->format('Y-m-d') ?? 'N/A' }}</dd>

                    <dt class="text-gray-600">Aired To:</dt>
                    <dd>{{ $anime->aired_to?->format('Y-m-d') ?? 'N/A' }}</dd>
                </dl>
            </div>

            <div>
                <img class="mb-5 w-full rounded-lg h-[50%] object-cover"
                    src="{{ $anime->images['jpg']['image_url'] ?? 'https://www.contentviewspro.com/wp-content/uploads/2017/07/default_image.png' }}"
                    alt="" srcset="">
                <h3 class="text-lg font-semibold mb-4">Synopsis</h3>
                <p class="text-gray-700 leading-relaxed">
                    {{ $anime->synopsis ?? 'No synopsis available.' }}
                </p>
            </div>
        </div>
    </div>
@endsection
