<?php

namespace Intranet\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Horari extends Component
{
    /**
     * Create a new component instance.
     */
    public $horario;
    public $config;

    public function __construct($horario, $config = [])
    {
        $this->horario = $horario;
        $this->config = $config;
    }

    public function render()
    {
        return view('components.horari');
    }
}
