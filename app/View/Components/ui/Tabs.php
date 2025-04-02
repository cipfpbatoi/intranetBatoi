<?php

namespace Intranet\View\Components\ui;

use Illuminate\View\Component;

class Tabs extends Component
{
    public string $id;
    public $pestanyes;

    public function __construct(string $id, $pestanyes)
    {
        $this->id = $id;
        $this->pestanyes = $pestanyes;
    }

    public function render()
    {
        return view('components.ui.tabs');
    }
}
