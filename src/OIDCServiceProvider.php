<?php

namespace oidcsociolite\laraveloidc;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory;
use oidcsociolite\laraveloidc\SocialiteProviders\OIDCProvider;

class OIDCServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/oidc.php', 'oidc');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     *
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        $this->registerPublishing();
        $this->configureSocialiteProvider();
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__ . '/../config/oidc.php' => config_path('oidc.php'),
        ], 'oidc-config');
    }

    /**
     * Configure the OIDC Socialite provider.
     *
     * @return void
     *
     * @throws BindingResolutionException
     */
    protected function configureSocialiteProvider(): void
    {
        $socialite = $this->app->make(Factory::class);

        $socialite->extend('oidc', function ($app) use ($socialite) {
            $config = $app['config']['services.oidc'];

            return $socialite->buildProvider(OIDCProvider::class, $config);
        });
    }
}
