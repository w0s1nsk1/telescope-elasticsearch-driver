<?php

namespace W0s1nsk1\TelescopeElasticsearchDriver;

use W0s1nsk1\TelescopeElasticsearchDriver\ElasticsearchEntriesRepository;
use Illuminate\Support\ServiceProvider;
use Laravel\Telescope\Contracts\ClearableRepository;
use Laravel\Telescope\Contracts\EntriesRepository;
use Laravel\Telescope\Contracts\PrunableRepository;

class TelescopeElasticsearchDriverServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('telescope-elasticsearch-driver.php'),
            ], 'telescope-elasticsearch-driver-config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'telescope-elasticsearch-driver');

        if (!$this->usingElasticsearchDriver()) {
            return;
        }

        $this->registerStorageDriver();
    }


    /**
     * Determine if we should register the bindings.
     *
     * @return bool
     */
    protected function usingElasticsearchDriver(): bool
    {
        return config('telescope.driver') === 'elasticsearch';
    }

    /**
     * Register elasticsearch storage driver.
     *
     * @return void
     */
    protected function registerStorageDriver(): void
    {
        $this->app->singleton(EntriesRepository::class, ElasticsearchEntriesRepository::class);
        $this->app->singleton(ClearableRepository::class, ElasticsearchEntriesRepository::class);
        $this->app->singleton(PrunableRepository::class, ElasticsearchEntriesRepository::class);
    }
}
