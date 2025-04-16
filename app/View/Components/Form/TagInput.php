<?php

namespace Intranet\View\Components\Form;


use Illuminate\View\Component;

class TagInput extends Component
{
    public string $name;
    public ?string $value;

    public function __construct(string $name, ?string $value = null)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function render()
    {
        return view('components.form.tag-input');
    }
}
