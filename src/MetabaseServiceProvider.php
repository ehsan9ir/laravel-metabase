<?php


namespace Ehsan9\MetabaseLaravel;

use Illuminate\Support\ServiceProvider;

class MetabaseServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('MetabaseApi');
        $this->mergeConfigFrom(__DIR__.'/config.php', 'metabase-api');

        if(config('metabase-api.is_make_when_bind_app')) {
            $this->app->singleton(MetabaseApi::class, function () {
                return new MetabaseApi(
                    config('metabase-api.url'), config('metabase-api.username'), config('metabase-api.password')
                );
            });
        }
    }

    public function boot()
    {
        $this->publishes([__DIR__.'/config.php' => config_path('metabase-api.php')]);
    }
}
