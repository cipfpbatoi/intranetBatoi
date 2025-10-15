<?php

 

namespace Intranet\Livewire\BustiaVioleta;

use Livewire\Component;
use Livewire\WithFileUploads;
use Intranet\Entities\BustiaVioleta;
use Intranet\Services\VioletHasher;

class Form extends Component
{
    use WithFileUploads;

    public $categoria = '';
    public $mensaje = '';
    public $anonimo = true;
    public $adjunto = null;

    protected $rules = [
        'categoria' => 'nullable|string|max:100',
        'mensaje'   => 'required|string|min:10|max:5000',
        'anonimo'   => 'boolean',
        'adjunto'   => 'nullable|file|max:4096', // 4MB
    ];

    public function submit()
    {
        $this->validate();

        $user = authUser(); // adapta si cal
        if (!$user || empty($user->dni)) {
            $this->addError('mensaje', 'No s’ha pogut identificar el teu DNI.');
            return;
        }

        $rol = isset($user->nia) ? 'alumno' : 'profesor';
        $dni = $user->dni;
        $nombre = method_exists($user, 'getShortName') ? $user->getShortName() : ($user->FullName ?? ($user->nombre ?? null));

        $path = null;
        if ($this->adjunto) {
            // requereix: php artisan storage:link
            $path = $this->adjunto->store('bustia_violeta', 'public');
        }

        BustiaVioleta::create([
            'dni'           => $this->anonimo ? null : $dni,
            'rol'           => $rol,
            'anonimo'       => (bool) $this->anonimo,
            'autor_nombre'  => $this->anonimo ? null : $nombre,
            'categoria'     => $this->categoria ?: null,
            'mensaje'       => $this->mensaje,
            'estado'        => 'nou',
            'publicable'    => false,
            'dni_hash'      => VioletHasher::dniHash($dni),
            'adjunto_path'  => $path,
        ]);

        $this->reset(['categoria','mensaje','anonimo','adjunto']);
        session()->flash('ok', 'El teu missatge s’ha enviat correctament.');
    }

    public function render()
    {
        return view('livewire.bustia-violeta.form');
    }
}
