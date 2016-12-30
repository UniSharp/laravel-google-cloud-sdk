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
        $this->app->singleton('google.cloud', function () {
            return with(new ServiceBuilder([
                'keyFilePath' => base_path(env('GOOGLE_CLOUD_KEY_FILE'))
            ]));
        });

        $this->app->singleton('bigQuery', function () {
            return app('google.cloud')->bigQuery();
        });

        $this->app->singleton('datastore', function () {
            return app('google.cloud')->datastore();
        });

        $this->app->singleton('logging', function () {
            return app('google.cloud')->logging();
        });

        $this->app->singleton('naturalLanguage', function () {
            return app('google.cloud')->naturalLanguage();
        });

        $this->app->singleton('pubsub', function () {
            return app('google.cloud')->pubsub();
        });

        $this->app->singleton('speech', function () {
            return app('google.cloud')->speech();
        });

        $this->app->singleton('storage', function () {
            return app('google.cloud')->storage();
        });

        $this->app->singleton('vision', function () {
            return app('google.cloud')->vision();
        });

        $this->app->singleton('translate', function () {
            return app('google.cloud')->translate();
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
