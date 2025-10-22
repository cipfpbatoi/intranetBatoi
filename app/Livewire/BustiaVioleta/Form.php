<?php

namespace Intranet\Livewire\BustiaVioleta;

use Livewire\Component;
use Intranet\Entities\BustiaVioleta;
use Intranet\Services\VioletHasher;

class Form extends Component
{
   

    public $tipus = 'violeta';
    public $categories = [];
    public $categoria = '';
    public $mensaje = '';
    public $finalitat = 'escoltar';
    public $anonimo = true;
  

    public function mount()
    {
        $this->reloadCategories();
        // si la categoria actual no existeix per al tipus, la netegem
        if ($this->categoria && ! array_key_exists($this->categoria, $this->categories)) {
            $this->categoria = '';
        }
    }

    public function updatedTipus($value)
    {
        $this->tipus = in_array($value, ['violeta','convivencia']) ? $value : 'violeta';
        $this->reloadCategories();
        $this->categoria = ''; // canvia el conjunt → reinicia selecció
    }

    protected function reloadCategories(): void
    {
        $this->categories = config("busties.{$this->tipus}", []);
    }



    protected function rules()
    {
        return [
            'tipus'     => 'required|in:violeta,convivencia',
            'categoria' => 'nullable|string|max:100',
            'finalitat' => 'required|in:parlar,escoltar,visibilitzar',
            'mensaje'   => 'required|string|min:10|max:5000',
            'anonimo'   => 'boolean',
        ];
    }

    public function updatedFinalitat($value)
    {
        // UX: si vol "parlar", automàticament desactivem l'anonimat
        if ($value === 'parlar') {
            $this->anonimo = false;
        }
    }

    public function submit()
    {
        $this->validate();

        if ($this->finalitat === 'parlar' && $this->anonimo) {
            $this->addError('anonimo', 'Si vols parlar amb nosaltres, no pot ser anònim.');
            return;
        }

        $user = authUser();
        if (!$user || empty($user->dni)) {
            $this->addError('mensaje', 'No s’ha pogut identificar el teu DNI.');
            return;
        }

        $rol = isset($user->nia) ? 'alumno' : 'profesor';
        $dni = $user->dni;
        $nom = method_exists($user,'getShortName') ? $user->getShortName() : ($user->FullName ?? ($user->nombre ?? null));
        

        BustiaVioleta::create([
            'tipus'         => $this->tipus,
            'dni'           => $this->anonimo ? null : $dni,
            'rol'           => $rol,
            'anonimo'       => (bool)$this->anonimo,
            'autor_nombre'  => $this->anonimo ? null : $nom,
            'categoria'     => $this->categoria ?: null,
            'mensaje'       => $this->mensaje,
            'estado'        => 'nou',
            'finalitat'     => $this->finalitat,
            'dni_hash'      => VioletHasher::dniHash($dni),
        ]);

        $this->reset(['categoria','finalitat','mensaje','anonimo' ]); 
        $this->finalitat = 'escoltar';
        session()->flash('ok', 'El teu missatge s’ha enviat correctament.');
    }


    public function render()
    {
        return view('livewire.bustia-violeta.form');
    }
}
