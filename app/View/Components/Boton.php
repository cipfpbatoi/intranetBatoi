<?php

namespace Intranet\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Boton extends Component
{
    public $href;
    public $class;
    public $id;
    public $icon;
    public $text;
    public $img;
    public $onclick;

    public function __construct($href, $class = 'btn-primary', $id = null, $icon = null, $text = 'Botó', $img = null, $onclick = null)
    {
        $this->href = $href;
        $this->class = $class;
        $this->id = $id;
        $this->icon = $icon;
        $this->text = $text;
        $this->img = $img;
        $this->onclick = $onclick;
    }

    public function render()
    {
        return view('components.boton');
    }
}
