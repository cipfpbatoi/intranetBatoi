<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonBasico;
use Intranet\Botones\BotonIcon;
use Illuminate\Support\Facades\Session;
use Jenssegers\Date\Date;



trait traitPanel{
    
    
    
    public function index()
    {
        $todos = isset($this->orden)?$this->search($this->orden):$this->search('desde');
        
        $this->crea_pestanas(config('modelos.'.$this->model.'.estados'),"profile.".strtolower($this->model));
        $this->iniBotones();
        Session::put('redirect','Panel'.$this->model.'Controller@index');
        return $this->grid($todos);
    }
    
//    public function index($condicion=null)
//    {
//        
//        $activa = Session::get('pestana') ? Session::get('pestana') : 1;
//        $todos = isset($this->orden)?$this->search($this->orden):$this->search('desde');
//        
//        foreach (config('modelos.'.$this->model.'.estados') as $key => $estado) {
//            $this->panel->setPestana($estado, $key == $activa ? true : false, "profile." .
//                strtolower($this->model),
//                isset($condicion)?dd(array_merge(['estado',$key],$condicion)):
//                ['estado',$key]);
//        }
//        $this->iniBotones();
//        Session::put('redirect','Panel'.$this->model.'Controller@index');
//        return $this->grid($todos);
//    }
    
    
    
    // carrega els elements
    protected function search()
    {
        $orden = isset($this->orden)?$this->orden:'desde';
        if (Session::get('completa'))
            return $this->class::where('estado', '>', '0')->orderBy($orden,'desc')->get();
        else{
            return $this->class::where('estado', '>', '0')
                    ->where(function($q){
                        $orden = isset($this->orden)?$this->orden:'desde';
                        $fecha = Date::now()->subDays(config('variables.diasNoCompleta'));
                        return $q->where('estado','!=',config('modelos.'.$this->model.'.resolve'))
                                ->where('estado','!=',config('modelos.'.$this->model.'.completa'))
                            ->orWhere($orden,'>',$fecha->toDateString());
                            
                    })
                    ->orderBy($orden,'desc')
                    ->get();
                   
        }    
    }
    /*
     * en $default vienen los botones colectivos
     * ademas añade el de autorizar para estado =1 , desatorizar para estado 2 y refuse para estado=1
     */
    protected function setAuthBotonera($default = ['2' => 'pdf', '1' => 'autorizar'],$enlace = true)
    {
        // Botons colectius
        foreach ($default as $item => $valor) {
            if ($this->class::where('estado', '=', $item)->count())
                $this->panel->setBoton('index', new BotonBasico("$this->model.$valor", ['id'=>$valor], true));
        }
        // Botons individuals
        $this->panel->setBoton('profile', new BotonIcon("$this->model.authorize", ['class' => 'btn-success authorize', 'where' => ['estado', '==', '1']], $enlace));
        $this->panel->setBoton('profile', new BotonIcon("$this->model.unauthorize", ['class' => 'btn-danger unauthorize', 'where' => ['estado', '==', '2']], $enlace));
        $this->panel->setBoton('profile', new BotonIcon("$this->model.refuse", ['class' => 'btn-danger refuse', 'where' => ['estado', '==', '1']], $enlace));
    }
}
