<?php

namespace Unisharp\GoogleCloud\Providers;

use Google\Cloud\ServiceBuilder;
use Illuminate\Support\ServiceProvider;

class GoogleCloudServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton('datastore', function () {
            return with(new ServiceBuilder([
                'keyFilePath' => base_path(env('GOOGLE_CLOUD_KEY_FILE'))
            ]))->datastore();
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
