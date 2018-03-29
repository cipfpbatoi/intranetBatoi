<?php

namespace Intranet\Providers;

use Styde\Html\HtmlServiceProvider as ServiceProvider;
use Intranet\Handlers\MyAccessHandler as BasicAccessHandler;
use Styde\Html\Access\AccessHandler;
use Illuminate\Contracts\Auth\Access\Gate;

class HtmlServiceProvider extends ServiceProvider
{

    protected function registerAccessHandler()
    {
        $this->app->singleton('access', function ($app) {
            $guard = $app['config']->get('html.guard', null);
            $handler = new BasicAccessHandler($app['auth']->guard($guard));

            $gate = $app->make(Gate::class);
            if ($gate) {
                $handler->setGate($gate);
            }

            return $handler;
        });

        $this->app->alias('access', AccessHandler::class);
    }

}
