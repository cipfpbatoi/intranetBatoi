<?php

namespace Intranet\View\Components\Layouts;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Intranet\Services\NavigationService;

class Titlebar extends Component
{
    public $ajuda;
    public $href;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->ajuda = existsHelp(substr(url()->current(), strlen(url('/'))));
        $this->href = NavigationService::getPreviousUrl().'?back=true';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.layouts.titlebar');
    }
}
