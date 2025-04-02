<?php

namespace Intranet\View\Components\Layouts;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Panel extends Component
{
    public $panel;
    public $pestanyes;
    /**
     * Create a new component instance.
     */
    public function __construct($panel)
    {
        $this->panel = $panel;
        $this->pestanyes = $panel->getPestanas();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.layouts.panel');
    }
}
