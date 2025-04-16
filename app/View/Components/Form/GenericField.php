<?php

namespace Intranet\View\Components\Form;


use Illuminate\View\Component;

class GenericField extends Component
{
    public string $name;
    public string $type;
    public mixed $value;
    public array $params;

    public function __construct(string $name, string $type, mixed $value = null, array $params = [])
    {
        $this->name = $name;
        $this->type = $type;
        $this->value = $value;
        $this->params = $params;
    }

    public function render()
    {
        return view('components.form.generic-field');
    }
}
