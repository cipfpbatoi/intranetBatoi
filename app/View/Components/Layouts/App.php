<?php

namespace Intranet\View\Components\Layouts;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class App extends Component
{
    public $user;
    public $panel;
    /**
     * Create a new component instance.
     */
    public function __construct($panel=null)
    {
        $this->user = authUser();
        $this->panel = $panel;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.layouts.app');
    }
}
