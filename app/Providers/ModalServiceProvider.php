<?php // src/ModalServiceProviderphp

namespace Intranet\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class ModalServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'../../resources/views/batoiModal', 'batoi-modal');

        Blade::component('Intranet\Botones\Modal','modal');
    }
}