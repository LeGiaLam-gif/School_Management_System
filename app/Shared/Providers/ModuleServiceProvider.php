<?php

namespace App\Shared\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/**
 * Lop co so cho ServiceProvider cua moi module.
 *
 * Moi module tu dang ky: migrations, views (namespace theo ten module, vd
 * `identity::auth.login`), route web/api va Policy. Them module moi chi can
 * tao thu muc theo mau + 1 provider ke thua lop nay + khai bao trong
 * bootstrap/providers.php.
 */
abstract class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Ten thu muc module, vd `Identity`.
     */
    abstract protected function moduleName(): string;

    /**
     * Policy cua module: [Model::class => Policy::class].
     *
     * @var array<class-string, class-string>
     */
    protected array $policies = [];

    protected function moduleKey(): string
    {
        return strtolower($this->moduleName());
    }

    protected function modulePath(string $path = ''): string
    {
        $base = app_path('Modules/'.$this->moduleName());

        return $path === '' ? $base : $base.'/'.ltrim($path, '/');
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom($this->modulePath('Database/Migrations'));

        if (is_dir($this->modulePath('Views'))) {
            $this->loadViewsFrom($this->modulePath('Views'), $this->moduleKey());
        }

        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }

        $this->registerRoutes();
    }

    protected function registerRoutes(): void
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        $web = $this->modulePath('Routes/web.php');
        if (is_file($web)) {
            Route::middleware('web')->group($web);
        }

        $api = $this->modulePath('Routes/api.php');
        if (config('sms.api_enabled') && is_file($api)) {
            Route::middleware('api')->prefix('api/v1')->name('api.')->group($api);
        }
    }
}
