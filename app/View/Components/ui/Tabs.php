<?php

namespace Intranet\View\Components\ui;

use Illuminate\View\Component;

class Tabs extends Component
{
    public string $id;
    public $pestanyes;
    public $panel;

    public function __construct(string $id , $panel)
    {
        $this->id = $id;
        $this->panel = $panel;
        $this->pestanyes = $this->panel->getPestanas();
    }

    public function render()
    {
        return view('components.ui.tabs');
    }
}
