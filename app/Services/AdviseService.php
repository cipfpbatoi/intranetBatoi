<?php

namespace Intranet\Services;

use Intranet\Componentes\Mensaje;
require_once base_path('app/Helpers/MyHelpers.php');

class AdviseService
{
    protected object $element;
    protected string $explanation;
    protected string $link;

    public static function exec(object $element, ?string $message = null): void
    {
        (new self($element, $message))->send();
    }

    public function __construct(object $element, ?string $message = null)
    {
        $this->element = $element;
        $this->setExplanation($message);
        $this->setLink();
    }

    protected static function file(): string
    {
        return is_file(base_path() . '/config/avisos.php') ? 'avisos.' : 'contacto.';
    }

    protected function getAdvises(): array
    {
        $modelClass = getClase($this->element);
        return collect(config("modelos.$modelClass.avisos", []))
            ->filter(fn($adviseState) => in_array($this->element->estado, $adviseState))
            ->keys()
            ->toArray();
    }

    protected function addDescriptionToMessage(): string
    {
        $description = '';

        if (isset($this->element->estado)) {
            $description = sprintf(
                "%s %s %s",
                getClase($this->element),
                primryKey($this->element),
                trans("models." . getClase($this->element) . '.' . $this->element->estado)
            );
        }

        if (!empty($this->element->descriptionField)) {
            $field = $this->element->descriptionField;
            $text = str_replace(["\r\n", "\n", "\r"], ' ', $this->element->$field);
            $description = mb_substr($text, 0, 50) . ". ";
        }

        return isset($this->element->estado)
            ? $description . blankTrans("models." . $this->element->estado . "." . getClase($this->element))
            : $description;
    }

    protected function advise($dnis): void
    {
        foreach ((array) $dnis as $dni) {
            Mensaje::send($dni, $this->explanation, $this->link);
        }
    }

    protected function setExplanation(?string $message): void
    {
        $this->explanation = trim($message . ". " . $this->addDescriptionToMessage());
    }

    protected function setLink(): void
    {
        $modelClass = strtolower(getClase($this->element));
        $this->link = "/$modelClass/{$this->element->id}" . ($this->element->estado < 2 ? "/edit" : "/show");
    }

    public function send(): void
    {
        foreach ($this->getAdvises() as $people) {
            match ($people) {
                'Creador' =>
                    $this->advise($this->element->Creador()),
                'director', 'jefeEstudios', 'secretario', 'orientador', 'vicedirector' =>
                    $this->advise(config(self::file() . $people)),
                'jefeDepartamento' =>
                    $this->advise($this->element->Profesor->dni ?? authUser()->miJefe),
                default =>
                    !empty($this->element->$people) && $this->advise($this->element->$people),
            };
        }
    }
}
