<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Exceptions\JikanApiException;
use Throwable;

class Handler extends ExceptionHandler
{
    public function register() : void
    {
        $this->reportable(function (JikanApiException $e) {
            //
        });

        $this->renderable(function (JikanApiException $e) {
            return response()->json([
                'message' => 'External API Error',
                'error'   => $e->getMessage(),
            ], $e->getStatusCode());
        });
    }
}
