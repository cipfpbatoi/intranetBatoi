<?php


namespace Intranet\Entities;

use Illuminate\Support\Facades\App;

class TipoReunion
{
    private $features;

    public function __construct($id){
        $this->features = config('tablas.tipoReunion')[$id];
    }

    public function __get($key){
        return $this->features[$key]??[];
    }

    public function __isset($key){
        return isset($this->features[$key]);
    }

    public static function allSelect($colectivo = null,$superUsuari=false)
    {
        $todos = [];
        foreach (config('tablas.tipoReunion') as $a) {
            if ($superUsuari || UserisAllow($a['rol']))
                if (!isset($a['ocultar']))
                    if (isset($colectivo)){
                        if ($a['colectivo']==$colectivo) $todos[$a['index']] = self::literal($a);}
                    else    
                        $todos[$a['index']] = self::literal($a);
        }
        return $todos;
    }

    public static function find($id){
        return new TipoReunion($id);
    }

    public static function all()
    {
        return config('tablas.tipoReunion');
    }

    private static function literal($a){
        return App::getLocale(session('lang')) == 'es'?$a['cliteral']:$a['vliteral'];
    }

    public function get()
    {
        return $this->features;
    }



    
    

}
