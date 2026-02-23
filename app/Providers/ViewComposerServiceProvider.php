<?php

namespace Intranet\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Intranet\Services\HR\FitxatgeService;

class ViewComposerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('layouts.partials.topnav', function ($view): void {
            $insideAuth = false;
            if (auth()->check() && !isset(authUser()->nia)) {
                $insideAuth = app(FitxatgeService::class)->isInside((string) authUser()->dni, true);
            }

            $view->with('insideAuth', $insideAuth);
        });

        View::composer([
            'intranet.partials.profile.equipo',
            'intranet.partials.profile.profesor',
            'intranet.partials.profile.profesorRes',
        ], function ($view): void {
            $fitxatgeService = app(FitxatgeService::class);
            $insideCache = [];

            $insideByDni = static function (string $dni) use ($fitxatgeService, &$insideCache): bool {
                if (!array_key_exists($dni, $insideCache)) {
                    $insideCache[$dni] = $fitxatgeService->isInside($dni, false);
                }

                return $insideCache[$dni];
            };

            $view->with('insideByDni', $insideByDni);
        });
    }
}
