<?php

namespace Intranet\View\Components\Layouts;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Pestanas extends Component
{
    public $pestanas;

    /**
     * Create a new component instance.
     */
    public function __construct(public $panel, public $elemento)
    {
        $this->pestanas = $panel->getPestanas();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.layouts.pestanas');
    }
}
