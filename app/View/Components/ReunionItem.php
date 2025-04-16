<?php

namespace Intranet\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ReunionItem extends Component
{
    /**
     * Create a new component instance.
     */
    public $reunion;
    public function __construct($reunion)
    {
        $this->reunion = $reunion;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.reunion-item');
    }
}
