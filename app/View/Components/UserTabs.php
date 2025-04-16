<?php

namespace Intranet\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class UserTabs extends Component
{
    /**
     * Create a new component instance.
     */
    public $tabs;
    public function __construct($tabs)
    {
        $this->tabs = $tabs;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.user-tabs');
    }
}
