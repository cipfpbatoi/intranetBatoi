<?php

namespace Intranet\View\Components\Layouts;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Leftside extends Component
{
    public $user;
    public $isAlumno;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->user = authUser();
        $this->isAlumno = $this->user->rol % config('roles.rol.alumno') == 0;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.layouts.leftside');
    }
}
