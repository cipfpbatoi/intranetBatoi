<?php

namespace Intranet\Livewire\BustiaVioleta;

use Livewire\Component;
use Livewire\WithPagination;
use Intranet\Entities\BustiaVioleta;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class AdminList extends Component
{
    use WithPagination;

    public $categoria = '';
    public $estado = '';
    public $search = '';

    protected $queryString = ['categoria','estado','search'];

    public function mount()
    {
        //Gate::authorize(config('violetbox.admin_gate', 'manage-bustia-violeta'));
    }

    public function updating($prop) { $this->resetPage(); }

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
        //Gate::authorize(config('violetbox.admin_gate', 'manage-bustia-violeta'));

        $q = BustiaVioleta::query()->latest();

        if ($this->categoria) $q->where('categoria', $this->categoria);
        if ($this->estado)   $q->where('estado', $this->estado);
        if ($this->search)   $q->where('mensaje', 'like', "%{$this->search}%");

        return view('livewire.bustia-violeta.admin-list', [
            'entrades' => $q->paginate(15),
        ]);
    }
}