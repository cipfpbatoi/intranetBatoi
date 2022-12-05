<?php

namespace Intranet\Livewire;

use Intranet\Entities\Hora;
use Intranet\Entities\Guardia;
use Livewire\Component;
use Livewire\WithPagination;
use Jenssegers\Date\Date;

class Controlguardia extends Component
{
    public $horas;
    public $dias;
    public $firstDay;
    public $lastDay;


    public function mount()
    {
        $this->horas = Hora::all();
        $this->dias  = array('L','M','X','J','V');
        $this->firstDay = new Date('last monday');
        $this->lastDay = new Date('last monday');
        $this->lastDay->addDays(5);
    }

    public function weekBefore()
    {
        $this->firstDay->subDays(7);
        $this->lastDay->subDays(7);
    }

    public function weekAfter()
    {
        $this->firstDay->addDays(7);
        $this->lastDay->addDays(7);
    }

    public function render()
    {
        $registers = array();
        $guardies =  Guardia::where('dia', '>=', $this->firstDay->format('Y-m-d'))->
        where('dia', '<=', $this->lastDay->format('Y-m-d'))->get();
        foreach ($guardies as $guarda) {
            $registers[$guarda->hora][nameDay($guarda->dia)][] = $guarda;
        }
        return view('livewire.guardias', compact('registers'));
    }


}
