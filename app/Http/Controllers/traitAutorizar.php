<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use DB;



trait traitAutorizar
{

    protected $init = 1; //estat quan s'inicialitza
    protected $notFollow = false; // quan pasa alguna cosa du a la pestana final de l'estat
    
    // cancela pasa a estat -1
    protected function cancel($id)
    {
        $this->class::putEstado($id,-1);
        return back();
    }
    
    //inicializat a init (normalment 1)
    protected function init($id)
    {
        $this->class::putEstado($id,$this->init);
        return back();
    }
    
    //imprimeix
    protected function _print($id)
    {
        $this->class::_print($id);
    }

    //resol    
    protected function resolve($id, $redirect = true,Request $request)
    {
        $inicial = $this->class::getEstado($id);
        $final = $this->class::resolve($id,$request->explicacion);
        if ($redirect) {
            return $this->follow($inicial, $final);
        }
    }

    // estat + 1
    protected function accept($id, $redirect = true)
    {
        $inicial = $this->class::getEstado($id);
        
        $final = $this->class::putEstado($id,$inicial+1);
        if ($redirect) {
            return $this->follow($inicial, $final);
        }
    }

    // estat -1
    protected function resign($id, $redirect = true)
    {
        $inicial = $this->class::getEstado($id);
        $final = $this->class::putEstado($id,$inicial-1);
        if ($redirect) {
            return $this->follow($inicial, $final);
        }
    }

    // refusa
    protected function refuse(Request $request, $id, $redirect = true)
    {
        $inicial = $this->class::getEstado($id);
        $final = $this->class::refuse($id,$request->explicacion);
        if ($redirect) {
            return $this->follow($inicial, $final);
        }
    }
    
    // rediriguix o no a un altra pestana
    private function follow($inicial,$final)
    {
        return $this->notFollow ? back()->with('pestana', $inicial) : back()->with('pestana', $final);
    }

    
    //efectuar sobre tots una acció
    protected function makeAll($todos, $accion)
    {
        if (is_string($accion)) {
            foreach ($todos as $uno) {
                $this->$accion($uno->id, false);
            }
        }
        else {
            foreach ($todos as $uno) {
                $this->class::putEstado($uno->id, $accion);
            }
        }
    }
    
    //crea link a gestor documental
    protected static function makeLink($todos,$doc)
    {
        foreach ($todos as $uno) {
            $uno->idDocumento = $doc;
            $uno->save();
         }
    }
    

}
