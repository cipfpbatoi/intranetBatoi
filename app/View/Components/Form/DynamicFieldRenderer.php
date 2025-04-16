<?php

namespace Intranet\View\Components\Form;

use Illuminate\View\Component;

class DynamicFieldRenderer extends Component
{
    public string $name;
    public string $type;
    public ?string $label;
    public mixed $value;
    public array $params;
    public mixed $currentFile;

    public function __construct(
        string $name,
        string $type,
        ?string $label = null,
        mixed $value = null,
        array $params = [],
        mixed $currentFile = null
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->label = $label;
        $this->value = $value;
        $this->params = $params;
        $this->currentFile = $currentFile;
    }

    public function render()
    {
        return view('components.form.dynamic-field-renderer');
    }
}
