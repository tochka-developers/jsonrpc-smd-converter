<?php

namespace Tochka\JsonRpcSmdConverter;


class JsonRpcConverterServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Command::class,
            ]);
        }

        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('jsonrpcsmdconverter.php')
        ], 'config');
    }
}