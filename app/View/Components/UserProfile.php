<?php

namespace Intranet\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class UserProfile extends Component
{
    /**
     * Create a new component instance.
     */
    public $usuario;
    public function __construct($usuario)
    {
        $this->usuario = $usuario;
    }


    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.user-profile');
    }
}
