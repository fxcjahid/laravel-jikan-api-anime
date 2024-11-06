<?php

namespace App\Console\Commands;

use App\Models\Anime;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportAnimeData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'anime:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import top 100 anime from Jikan API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting anime import...');

        try {
            /**
             * Jikan API has a pagination limit of 25 items per page
             */

            $pages     = 4; // 4 pages × 25 items = 100 anime
            $processed = 0;

            for ($page = 1; $page <= $pages; $page++) {

                /**
                 *  Respect Jikan API rate limiting
                 */
                if ($page > 1) {
                    sleep(1);
                }

                $response = Http::get("https://api.jikan.moe/v4/top/anime", [
                    'page'  => $page,
                    'limit' => 25,
                ]);

                if (! $response->successful()) {
                    $this->error("Failed to fetch page {$page}");
                    continue;
                }

                $animeList = $response->json()['data'];

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
                            'synopsis'   => $animeData['synopsis'],
                            'type'       => $animeData['type'],
                            'episodes'   => $animeData['episodes'],
                            'score'      => $animeData['score'],
                            'rank'       => $animeData['rank'],
                            'popularity' => $animeData['popularity'],
                            'status'     => $animeData['status'],
                            'aired_from' => $animeData['aired']['from'],
                            'aired_to'   => $animeData['aired']['to'],
                        ],
                    );

                    $processed++;
                    $this->info("Processed anime: {$titles['en']}");
                }
            }

            $this->info("Import completed! Processed {$processed} anime entries.");

        } catch (\Exception $e) {
            $this->error("An error occurred: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
