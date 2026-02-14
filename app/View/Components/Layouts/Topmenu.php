<?php

namespace Intranet\View\Components\Layouts;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Session;
use Illuminate\View\Component;

class Topmenu extends Component
{
    public $user;
    public $isAlumno;
    public $userChange;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->user = authUser();
        $this->isAlumno = isset($this->user->nia);
        $this->userChange = Session::get('userChange');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.layouts.topmenu');
    }
}
