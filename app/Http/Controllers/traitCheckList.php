<?php

namespace Intranet\Http\Controllers;

use DB;
use Illuminate\Http\Request;

trait traitCheckList
{

    protected $mensajeCheckList = '';

    public function __construct()
    {
        parent::__construct();
        
        for ($i = 0; $i < $this->items; $i++) {
            $this->panel->items[2 ** $i] = trans("models." . $this->model . ".item$i");
        }
    }

    public function checkList(Request $request, $id)
    {
        $elemento = $this->class::findOrFail($id);
        $elemento->checkList = $this->whosCheck($request->items);
        $elemento->save();
        if ($this->allCheck($elemento->checkList)) {
            return $this->accept($id);
        }

        $request->explicacion = $this->whosLeft($elemento->checkList);
        return $this->refuse($request, $id);
    }

    private function whosLeft($checked)
    {
        $todos = checkItems($checked);
        $mensaje = '';
        for ($i = 0; $i < $this->items; $i++) {
            if (!in_array(2 ** $i, $todos)) {
                $mensaje .= trans("models." . $this->model . ".item$i") . " , ";
            }
        }
        return $mensaje;
    }

    private function whosCheck($items)
    {
        $suma = 0;
        if (isset($items)&& $items>0) {
            foreach ($items as $item) {
                $suma += $item;
            }
        }
        return $suma;
    }

    private function allCheck($suma)
    {
        return (2 ** ($this->items)) - 1 == $suma;
    }

}
