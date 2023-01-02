<?php

namespace Intranet\View\Components;

use Illuminate\View\Component;
use function view;

class Label extends Component
{
    public $id;
    public $cab1;
    public $cab2;
    public $title;
    public $subtitle;
    public $clock;
    public $view;

    /**
     * @param $id
     * @param $desde
     * @param $hasta
     * @param $title
     * @param $subtitle
     * @param $clock
     * @param $view
     */

    public function __construct($id, $cab1, $cab2, $title, $subtitle=null, $inside=null, $view='date')
    {
        $this->id = $id;
        $this->cab1 = $cab1;
        $this->cab2 = $cab2;
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->inside = $inside;
        $this->view = $view;
    }


    public function render()
    {
        return view('components.label.'.$this->view);
    }
}
