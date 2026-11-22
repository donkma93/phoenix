<?php

namespace Modules\QueryMonitor;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\QueryMonitor\Analyzer\ExplainAnalyzer;
use Modules\QueryMonitor\Analyzer\TuningAnalyzer;
use Modules\QueryMonitor\Collector\QueryCollector;
use Modules\QueryMonitor\Console\CreateUserCommand;
use Modules\QueryMonitor\Http\Middleware\MonitorAuthenticate;
use Modules\QueryMonitor\Storage\MonitorStore;

class QueryMonitorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/query-monitor.php', 'query-monitor');

        $this->app->singleton(MonitorStore::class, function () {
            return new MonitorStore((string) config('query-monitor.sqlite_path'));
        });

        $this->app->singleton(QueryCollector::class, function ($app) {
            return new QueryCollector($app->make(MonitorStore::class));
        });

        $this->app->singleton(ExplainAnalyzer::class, function () {
            return new ExplainAnalyzer();
        });

        $this->app->singleton(TuningAnalyzer::class, function ($app) {
            return new TuningAnalyzer($app->make(ExplainAnalyzer::class));
        });
    }

    public function boot(): void
    {
        if (!config('query-monitor.enabled', true)) {
            return;
        }

        // Load isolated views under namespace "query-monitor"
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'query-monitor');

        // Register middleware alias
        $this->app['router']->aliasMiddleware('query-monitor.auth', MonitorAuthenticate::class);

        // Register isolated routes (using standalone web middleware stack, decoupled from main app's SetLocale)
        Route::middleware([
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ])
            ->prefix((string) config('query-monitor.route_prefix', 'query-monitor'))
            ->name('query-monitor.')
            ->group(__DIR__ . '/../routes/web.php');

        // Register Artisan CLI command
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateUserCommand::class,
            ]);
        }

        // Listen to all database queries and flush once per request lifecycle
        $collector = $this->app->make(QueryCollector::class);

        DB::listen(function (QueryExecuted $query) use ($collector) {
            $collector->record($query);
        });

        $this->app->terminating(function () use ($collector) {
            $collector->flush();
        });
    }
}
