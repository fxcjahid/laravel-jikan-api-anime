<?php

namespace App\Console\Commands;

use App\Jobs\ScrapperAnime;
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
        ScrapperAnime::dispatch();
        return 0;
    }
}
