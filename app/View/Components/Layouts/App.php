<?php

namespace Intranet\View\Components\Layouts;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Layout principal de la Intranet amb selector de runtime JS.
 *
 * Modes disponibles:
 * - legacy: només assets clàssics de `public/js`.
 * - hybrid: assets clàssics + entrada Vite moderna (`resources/assets/js/app.js`).
 * - vite: només entrades Vite (`legacy-app.js`, `app.js`, `ppIntranet.js`).
 */
class App extends Component
{
    /**
     * Usuari autenticat actual.
     *
     * @var mixed
     */
    public $user;


    /**
     * Crea una nova instància del component.
     *
     * @param mixed $panel
     * @param string $title
     * @param bool $skipLegacyJs
     * @param string $jsMode
     */
    public function __construct(
        public $panel = null,
        public string $title = ' ',
        public bool $skipLegacyJs = false,
        public string $jsMode = 'hybrid'
    )
    {
        $this->user = authUser();
        if (!in_array($this->jsMode, ['legacy', 'hybrid', 'vite'], true)) {
            $this->jsMode = 'hybrid';
        }
    }

    /**
     * Retorna la vista que representa el component.
     */
    public function render(): View|Closure|string
    {
        return view('components.layouts.app');
    }
}
