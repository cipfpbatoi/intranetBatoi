<?php

namespace Intranet\View\Components;

use Illuminate\View\Component;
use Intranet\Entities\Activity as ActivityModel;

class Activity extends Component
{
    public ActivityModel $activity;
    public $fecha ;
    public $action;
    public $class;

    public function __construct(ActivityModel $activity)
    {
        $this->activity = $activity;
        $this->fecha = fechaCurta($activity->created_at);
        $this->class = $this->getClass();
        $this->action = $this->getAction();
    }

    public function render()
    {
        return view('components.activity');
    }

    public function getClass()
    {
        return match (firstWord($this->activity->document)) {
            'Recordatori' => 'flag',
            'Informació'  => 'lock',
            'Revisió'     => 'check',
            'Sol·licitud' => 'bell',
            default       => null
        };
    }

    public function getAction()
    {
        return match ($this->activity->action) {
            'email'  => 'envelope',
            'visita' => 'car',
            'phone'  => 'phone',
            'book'   => 'book',
            default  => null
        };
    }
}
