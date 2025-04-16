<?php

namespace Intranet\View\Components\Layouts;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Topnav extends Component
{
    public $user;
    public $notifications;
    public $totalNotifications;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
       $this->user = authUser();
       $this->notifications = $this->user->unreadNotifications()->paginate(6);
       $this->totalNotifications = $this->notifications->total();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.layouts.topnav');
    }
}
