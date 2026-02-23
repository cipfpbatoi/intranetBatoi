<?php

namespace Intranet\View\Components\Layouts;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Session;
use Illuminate\View\Component;
use Intranet\Services\HR\FitxatgeService;

class Topmenu extends Component
{
    public $user;
    public $isAlumno;
    public $userChange;
    public ?string $entrada;
    public ?string $salida;

    /**
     * Create a new component instance.
     */
    public function __construct(FitxatgeService $fitxatgeService)
    {
        $this->user = authUser();
        $this->isAlumno = isset($this->user->nia);
        $this->userChange = Session::get('userChange');
        $this->entrada = $this->isAlumno ? null : $fitxatgeService->sessionEntry();
        $this->salida = $this->isAlumno ? null : $fitxatgeService->sessionExit();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.layouts.topmenu');
    }
}
