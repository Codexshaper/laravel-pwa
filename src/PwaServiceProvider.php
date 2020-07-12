<?php

namespace CodexShaper\PWA;

use CodexShaper\PWA\Commands\InstallPwa;
use CodexShaper\PWA\PWA;
use CodexShaper\PWA\Model\Setting;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;

class PwaServiceProvider extends ServiceProvider
{

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'pwa');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('pwa', function () {
            return new PWA();
        });
        $this->mergeConfigFrom(
            __DIR__ . '/../config/pwa.php', 'config'
        );
        $this->loadHelpers();
        $this->registerBladeDirectives();
        $this->registerPublish();
        $this->registerCommands();
    }

    /**
     * Register blade directories.
     *
     * @return void
     */
    protected function registerBladeDirectives()
    {
        $this->app->afterResolving('blade.compiler', function (BladeCompiler $blade) {
            $blade->directive('PWA', function () {
                $pwa = Setting::where('domain', '=', request()->getHttpHost())->first();
                echo view('pwa::meta', compact('pwa'))->render();
            });
        });

    }

    /**
     * Load all helpers.
     *
     * @return void
     */
    protected function loadHelpers()
    {
        foreach (glob(__DIR__ . '/Helpers/*.php') as $filename) {
            require_once $filename;
        }
    }

    /**
     * Register publishable assets.
     *
     * @return void
     */
    protected function registerPublish()
    {
        $publishable = [
            'pwa.config'    => [
                __DIR__ . '/../config/pwa.php' => config_path('pwa.php'),
            ],
            'pwa.migrations'    => [
                __DIR__ . '/../database/migrations/' => database_path('migrations'),
            ],
            'pwa.tenant.migrations'    => [
                __DIR__ . '/../database/migrations/' => database_path('migrations/tenant'),
            ],
            'pwa.seeds'     => [
                __DIR__ . "/../database/seeds/" => database_path('seeds'),
            ],
            'pwa.views'     => [
                __DIR__ . '/../resources/views' => resource_path('views/vendor/pwa'),
            ],
            'pwa.resources' => [
                __DIR__ . '/../resources' => resource_path('views/vendor/pwa'),
            ],
            'pwa.lang' => [
                __DIR__ . '/../resources/lang' => resource_path('lang'),
            ],
        ];

        foreach ($publishable as $group => $paths) {
            $this->publishes($paths, $group);
        }
    }

    /**
     * Register commands.
     *
     * @return void
     */
    private function registerCommands()
    {
        $this->commands(InstallPwa::class);
    }
}
