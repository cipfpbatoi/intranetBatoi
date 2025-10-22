<?php

namespace Intranet\Livewire\BustiaVioleta;

use Livewire\Component;
use Livewire\WithPagination;
use Intranet\Entities\BustiaVioleta;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Intranet\Entities\Profesor;
use Intranet\Entities\Alumno;   

class AdminList extends Component
{
    use WithPagination;

    public $categoria = '';
    public $estado = '';
    public $search = '';
    public $tipus = ''; // '' | 'violeta' | 'convivencia'
    public $finalitat = ''; // '' | 'parlar' | 'escoltar' | 'visibilitzar'
    protected $queryString = ['categoria','estado','search','tipus','finalitat'];

    public $showContact = false;
    public $contact = [
        'rol' => null,
        'nom' => null,
        'email' => null,
        'telefon' => null,
        'grup' => null,
        'dni' => null,
    ];

    

    public function mount()
    {
        //Gate::authorize(config('violetbox.admin_gate', 'manage-bustia-violeta'));
    }

    public function updating($prop) { $this->resetPage(); }

    public function viewContact(int $id)
    {
        //Gate::authorize(config('violetbox.admin_gate', 'manage-bustia-violeta'));

        $m = BustiaVioleta::findOrFail($id);

        // Protecció: només si no és anònim i té DNI
        if ($m->anonimo || empty($m->dni)) {
            session()->flash('ok', "La fitxa #{$m->id} és anònima o no té DNI.");
            return;
        }

        $this->contact = [
            'rol' => $m->rol,
            'nom' => null,
            'email' => null,
            'telefon' => null,
            'grup' => null,
            'dni' => $m->dni,
        ];

        if ($m->rol === 'profesor') {
            $p = Profesor::where('dni', $m->dni)->first();
            if ($p) {
                $this->contact['nom'] = $p->FullName ?? ($p->nombre ?? null);
                $this->contact['email'] = $p->email ?? $p->emailItaca ?? null;
                $this->contact['telefon'] = $p->movil1 .'-'. $p->movil2;
            }
        } else { // alumne
            // adapta "grupo"/relació segons el teu model (ex.: $a->Grupo?->nombre)
            $a = Alumno::where('dni', $m->dni)->with('Grupo')->first();
            if ($a) {
                $this->contact['nom'] = $a->FullName ?? ($a->nombre ?? null);
                $this->contact['email'] = $a->email ?? null;
                $this->contact['telefon'] = $a->telef1 .'-'. $a->telef2;
                $this->contact['grup'] = optional($a->Grupo->first())->nombre
                    ?? $a->Grupo
                    ?? null;
            }
        }

        $this->showContact = true;
        $this->dispatchBrowserEvent('open-contact');
    }

    public function closeContact()
    {
        $this->showContact = false;
        $this->contact = [
            'rol' => null, 'nom' => null, 'email' => null, 'telefon' => null, 'grup' => null, 'dni' => null,
        ];
        $this->dispatchBrowserEvent('close-contact');
    }

    public function setEstado(int $id, string $estado)
    {
        //Gate::authorize(config('violetbox.admin_gate', 'manage-bustia-violeta'));
        if (!in_array($estado, ['nou','en_revisio','tancat'])) return;

        if ($m = BustiaVioleta::find($id)) {
            $m->estado = $estado;
            $m->save();
            session()->flash('ok', "Entrada #{$id} → estat {$estado}");
        }
    }

    public function togglePublicable(int $id)
    {
        //Gate::authorize(config('violetbox.admin_gate', 'manage-bustia-violeta'));
        if ($m = BustiaVioleta::find($id)) {
            $m->publicable = !$m->publicable;
            $m->save();
            session()->flash('ok', "Entrada #{$id} → publicable: " . ($m->publicable ? 'Sí' : 'No'));
        }
    }

    public function delete(int $id)
    {
        //Gate::authorize(config('violetbox.admin_gate', 'manage-bustia-violeta'));
        if ($m = BustiaVioleta::find($id)) {
            if ($m->adjunto_path) {
                Storage::disk('public')->delete($m->adjunto_path);
            }
            $m->delete();
            session()->flash('ok', "Entrada #{$id} eliminada.");
            $this->resetPage();
        }
    }

    public function render()
    {
        //Gate::authorize(config('violetbox.admin_gate','manage-bustia-violeta'));
        $q = BustiaVioleta::query()->latest();

        if ($this->tipus)     $q->where('tipus', $this->tipus);
        if ($this->categoria) $q->where('categoria', $this->categoria);
        if ($this->estado)    $q->where('estado', $this->estado);
        if ($this->finalitat) $q->where('finalitat', $this->finalitat);
        if ($this->search)    $q->where('mensaje', 'like', "%{$this->search}%");

        return view('livewire.bustia-violeta.admin-list', ['entrades' => $q->paginate(15)]);
    }
}