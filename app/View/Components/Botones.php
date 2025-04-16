<?php

namespace Intranet\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Botones extends Component
{
    public Collection $botones;

    public function __construct(
        public mixed $panel,
        public string $tipo,
        public mixed $elemento = null,
        public bool $centrado = true
    ) {
        if ($tipo === 'grid') {
            $this->centrado = false;
        }

        // Calculem els botons un sol cop
        $this->botones = collect($this->panel->getBotones($this->tipo))
            ->map(fn($btn) => $this->elemento ? $btn->render($this->elemento) : $btn->render())
            ->filter()
            ->values();
    }

    public function render(): View|Closure|string
    {
        return view('components.botones', [
            'botones' => $this->botones,
        ]);
    }
}
