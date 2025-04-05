<?php

namespace Intranet\View\Components\Layouts;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Panel extends Component
{
     public $title;
    /**
     * Create a new component instance.
     */
    public function __construct(
        public $panel,

    )
    {
         $this->title = $panel->getTitulo();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.layouts.panel');
    }
}
