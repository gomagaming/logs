<?php 

namespace GomaGaming\Logs;

use Illuminate\Support\ServiceProvider;
use GomaGaming\Logs\Console\DeleteLogs;
use Illuminate\Support\Facades\Route;
use GomaGaming\Logs\Lib\JiraApi;

class GomaGamingLogsServiceProvider extends ServiceProvider
{

    public function boot()
    {     
        if (app() instanceof \Illuminate\Foundation\Application) {
            $this->publishes([
                __DIR__ . '/../config/gomagaminglogs.php' => config_path('gomagaminglogs.php')
            ]);
        }

        if ($this->app->runningInConsole()) {
            if (! class_exists('CreateLogsTable')) {
              $this->publishes([
                __DIR__ . '/../database/migrations/create_logs_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_logs_table.php'),
              ], 'migrations');
            }
        }

        if ($this->app->runningInConsole()) {
            $this->commands([
                DeleteLogs::class,
            ]);
        }

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'gomagaming');        

        $this->registerRoutes();
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/gomagaminglogs.php', 'gomagaminglogs');

        $this->app->singleton(GomaGamingLogs::class, function() {
            return new GomaGamingLogs();
        });      

        if(config('gomagaminglogs.jira.create_issues'))
        {
            $this->app->singleton(JiraApi::class, function ($app) {
                return new JiraApi(config('gomagaminglogs.jira.project_domain'));
            });
        }
    }

    protected function registerRoutes()
    {
        Route::group(['prefix' => 'gglogs'], function () {
            Route::group(['prefix' => 'api'], function () {

                $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

            });
        });
    }

}
