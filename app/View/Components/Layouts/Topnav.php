<?php

namespace Intranet\View\Components\Layouts;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Intranet\Services\HR\FitxatgeService;

class Topnav extends Component
{
    public $user;
    public $notifications;
    public $totalNotifications;
    public bool $inside;
    /**
     * Create a new component instance.
     */
    public function __construct(FitxatgeService $fitxatgeService)
    {
       $this->user = authUser();
       $this->notifications = $this->user->unreadNotifications()->paginate(6);
       $this->totalNotifications = $this->notifications->total();
       $this->inside = !isset($this->user->nia)
           && $fitxatgeService->isInside((string) $this->user->dni, true);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.layouts.topnav');
    }
}
