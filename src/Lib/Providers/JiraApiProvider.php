<?php

namespace GomaGaming\Logs\Lib;

use GomaGaming\Logs\Lib\JiraApi;
use Illuminate\Support\ServiceProvider;

class JiraApiProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        if(config('gomagaminglogs.jira.create_issues'))
        {
            $this->app->singleton(JiraApi::class, function ($app) {
                return new JiraApi(config('gomagaminglogs.jira.project_domain'));
            });
        }
    }
}
