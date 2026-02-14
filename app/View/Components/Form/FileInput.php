<?php

namespace Intranet\View\Components\Form;


use Illuminate\View\Component;

class FileInput extends Component
{
    public string $name;
    public string $label;
    public mixed $currentFile;
    public array $params;

    public function __construct(string $name, string $label, mixed $currentFile = null, array $params = [])
    {
        $this->name = $name;
        $this->label = $label;
        $this->currentFile = $currentFile;
        $this->params = $params;
    }

    public function render()
    {
        return view('components.form.file-input');
    }
}
