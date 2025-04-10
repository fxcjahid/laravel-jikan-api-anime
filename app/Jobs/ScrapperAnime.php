<?php

namespace App\Jobs;

use Exception;
use Throwable;
use App\Models\Anime;
use Illuminate\Support\Str;
use App\Services\JikanApiService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Exceptions\JikanApiException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ScrapperAnime implements ShouldQueue
{
    use Queueable;

    /**
     * Jikan API Service Provider
     *
     * @var JikanApiService $jikanService
     */
    private $jikanService;

    /**
     * Initialize jikan service
     * @param \App\Services\JikanApiService $jikanService
     */
    public function __construct()
    {
        $this->jikanService = new JikanApiService;
    }

    /**
     * Execute the job.
     */
    public function handle() : void
    {
        try {
            DB::beginTransaction();

            $this->importAnimeData();

            DB::commit();

            Cache::put('last_anime_import', now(), now()->addDay());

        } catch (JikanApiException $e) {

            DB::rollBack();

            Log::error('Anime Import Failed - Jikan API Error', [
                'message'     => $e->getMessage(),
                'status_code' => $e->getStatusCode(),
            ]);

        } catch (Exception $e) {

            DB::rollBack();

            Log::error('Anime Import Failed - Unexpected Error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
        }
    }


    /**
     * Checking availity for run command
     * @return bool
     */
    private function shouldRun() : bool
    {
        if ($this->option('force')) {
            return true;
        }

        $lastRun = Cache::get('last_anime_import');

        return ! $lastRun || $lastRun->addHours(12)->isPast();
    }

    /**
     * Insert data from API to database
     * @return int
     */
    private function importAnimeData() : int
    {
        $processed = 0;
        $pages     = 4; // 4 pages × 25 items = 100 anime

        for ($page = 1; $page <= $pages; $page++) {
            $animeList = $this->jikanService->getTopAnime($page);

            foreach ($animeList as $animeData) {
                $titles = [
                    'en' => $animeData['title_english'] ?? $animeData['title'],
                    'pl' => $animeData['title'] // Note: Jikan API doesn't provide Polish titles
                ];

                $slugs = [
                    'en' => Str::slug($titles['en']),
                    'pl' => Str::slug($titles['pl']),
                ];

                Anime::updateOrCreate(['mal_id' => $animeData['mal_id']],
                    [
                        'titles'     => $titles,
                        'slugs'      => $slugs,
                        'images'     => $animeData['images'],
                        'synopsis'   => $animeData['synopsis'],
                        'type'       => $animeData['type'],
                        'episodes'   => $animeData['episodes'],
                        'score'      => $animeData['score'],
                        'rank'       => $animeData['rank'],
                        'popularity' => $animeData['popularity'],
                        'status'     => $animeData['status'],
                        'aired_from' => $animeData['aired']['from'],
                        'aired_to'   => $animeData['aired']['to'],
                        'response'   => $animeData,
                    ],
                );

                $processed++;
            }

            if ($page < $pages) {
                // $this->warn("Waiting for rate limit...");
            }
        }

        return $processed;
    }

    public function failed(Throwable $exception)
    {
        Log::error("Job failed: {$exception->getMessage()}");
    }
}
