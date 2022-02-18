<?php 

namespace GomaGaming\Logs;

use Illuminate\Support\ServiceProvider;

class GomaGamingLogsServiceProvider extends ServiceProvider
{

    public function boot()
    {     
        if (app() instanceof \Illuminate\Foundation\Application) {
            $this->publishes([
                __DIR__ . '/../config/gomagaminglogs.php' => config_path('gomagaminglogs.php')
            ]);
        }       
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/gomagaminglogs.php', 'gomagaminglogs');

        $this->app->singleton(GomaGamingLogs::class, function() {
            return new GomaGamingLogs();
        });      
    }    

}