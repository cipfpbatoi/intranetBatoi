<?php
namespace Intranet\App\Http\Livewire;

use Intranet\Entities\Lote as Factura;
use Livewire\Component;

class Lote extends Component
{
    public $registre, $proveedor, $procedencia, $fechaAlta, $selected_id;
    public $updateMode = false;

    public function render()
    {
        $data = Factura::all();
        $elemento = new Factura();
        $default = $elemento->fillDefautOptions(); // ompli caracteristiques dels camps
        return view('livewire.lote.lote',compact('data','default','elemento'));
    }
    private function resetInput()
    {
        $this->registre = null;
        $this->proveedor = null;
        $this->procedencia = null;
        $this->fechaAlta = null;
    }

    public function store()
    {
        $this->validate([
            'registre' => 'required|min:5',
            'proveedor' => 'required'
        ]);
        Factura::create([
            'registre' => $this->registre,
            'proveedor' => $this->proveedor,
            'procedencia' => $this->procedencia,
            'fechaAlta' => $this->fechaAlta,
        ]);
        $this->resetInput();
    }

    public function edit($id)
    {
        $record = Factura::findOrFail($id);
        $this->selected_id = $id;
        $this->registre = $record->registre;
        $this->proveedor = $record->proveedor;
        $this->procedencia = $record->procedencia;
        $this->fechaAlta = $record->fechaAlta;
        $this->updateMode = true;
    }

    public function update()
    {
        $this->validate([
            'selected_id' => 'required|numeric',
            'registre' => 'required|min:5',
            'proveedor' => 'required'
        ]);
        if ($this->selected_id) {
            $record = Factura::find($this->selected_id);
            $record->update([
                'registre' => $this->registre,
                'proveedor' => $this->proveedor,
                'procedencia' => $this->procedencia,
                'fechaAlta' => $this->fechaAlta,
            ]);
            $this->resetInput();
            $this->updateMode = false;
        }
    }
    public function destroy($id)
    {
        if ($id) {
            $record = Factura::where('id', $id);
            $record->delete();
        }
    }
}
