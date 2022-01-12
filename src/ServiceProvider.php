<?php

namespace Maduser\Laravel\ViewModel;

use Exception;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Illuminate\Support\Str;
use Maduser\Laravel\Support\Helpers\CSV;

class ServiceProvider extends LaravelServiceProvider {
    /**
     * @throws Exception
     */
    public function boot()
    {
        ViewModel::macro('toCsv', function() {
            return CSV::create($this->toArray());
        });

        Request::macro('wantsCsv', function () {
            $acceptable = Request::getAcceptableContentTypes();

            return isset($acceptable[0]) && Str::contains($acceptable[0], ['/csv', '+csv']);
        });

        Response::macro('csv', function (
            array $content,
            string $filename = 'file.csv',
            int $status = 200
        ) {
            return Response::make(CSV::create($content), $status)->withHeaders([
                'Content-type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename=' . $filename
            ]);
        });
    }
}

