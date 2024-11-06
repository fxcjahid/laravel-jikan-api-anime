<?php

namespace App\Console\Commands;

use App\Models\Anime;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use App\Services\JikanApiService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Exceptions\JikanApiException;
use Illuminate\Support\Facades\Cache;

class ImportAnimeData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'anime:import {--force : Force import even if recently run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import top 100 anime from Jikan API';

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
    public function __construct(JikanApiService $jikanService)
    {
        parent::__construct();
        $this->jikanService = $jikanService;
    }

    /**
     * Execute the console command.
     * @return boolean|int
     */
    public function handle() : int
    {
        if (! $this->shouldRun()) {
            $this->warn('Import was recently run. Use --force to override.');
            return 0;
        }

        $this->info('Starting anime import...');

        try {
            DB::beginTransaction();

            $processed = $this->importAnimeData();

            DB::commit();

            $this->info("Import completed! Processed {$processed} anime entries.");
            Cache::put('last_anime_import', now(), now()->addDay());

            return 0;
        } catch (JikanApiException $e) {

            DB::rollBack();
            $this->error("Jikan API Error: " . $e->getMessage());

            Log::error('Anime Import Failed - Jikan API Error', [
                'message'     => $e->getMessage(),
                'status_code' => $e->getStatusCode(),
            ]);

            return 1;
        } catch (\Exception $e) {

            DB::rollBack();
            $this->error("An unexpected error occurred: " . $e->getMessage());

            Log::error('Anime Import Failed - Unexpected Error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return 1;
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
                $this->info("Processed anime: {$titles['en']}");
            }

            if ($page < $pages) {
                $this->warn("Waiting for rate limit...");
            }
        }

        return $processed;
    }
}
