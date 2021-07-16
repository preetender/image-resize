<?php

namespace Leve\Uploader;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        // carregar arquivo de configuração do pacote
        $this->mergeConfigFrom(__DIR__ . '/../config/uploader.php', 'uploader');
    }
}
