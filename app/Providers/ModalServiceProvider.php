<?php // src/ModalServiceProviderphp

namespace Intranet\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ModalServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'../../resources/views/batoiModal', 'batoi-modal');

        Blade::component('Intranet\Componentes\Modal','modal');
    }
}