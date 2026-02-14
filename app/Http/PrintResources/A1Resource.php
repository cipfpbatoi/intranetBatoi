<?php

namespace Intranet\Http\PrintResources;



class A1Resource extends PrintResource
{
    
    public function __construct($empresa)
    {
        $this->empresa = $empresa;
        $this->file = 'Anexo_I.pdf';
        $this->flatten = true;
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray()
    {

        $director = cargo('director');

        return [
            'cod centre' => config('contacto.codi'),
            'centro' => config('contacto.nombre'),
            'cod num' => $this->empresa->concierto,
            'cod any' => year($this->dataSig()),
            'delegue' => $this->empresa->gerente,
            'f_dia' => day($this->dataSig()),
            'f_mes' => mes($this->dataSig()),
            'f_any' => year($this->dataSig()),
            'director' => $director->fullName,
            'contacto centro' =>
                config('contacto.direccion').'('.
                config('contacto.poblacion').') - '.
                config('contacto.email').' - '.
                config('contacto.telefono'),
            'representante' => $this->empresa->gerente,
            'nombre empresa' => $this->empresa->nombre.' '.$this->empresa->cif,
            'contacto empresa' =>
                $this->empresa->direccion.'('.
                $this->empresa->localidad.') - '.
                $this->empresa->email.' - '.
                $this->empresa->telefono,
            'email' => $this->empresa->email,
            'población' => config('contacto.poblacion'),
            'dia' => day($this->dataSig()),
            'mes' => month($this->dataSig()),
            'año' => substr(year($this->dataSig()), 2, 2),
        ];
    }

    private function dataSig(){
        return $this->empresa->data_signatura??Hoy();
    }
}
