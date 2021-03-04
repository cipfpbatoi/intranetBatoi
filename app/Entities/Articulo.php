<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;



class Articulo extends Model
{

    protected $table = 'articulos';
    public $timestamps = false;
    protected $fillable = [ 'descripcion', 'fichero'];


    use BatoiModels;

    protected $rules = [
        'descripcion' => 'required',
    ];
    protected $inputTypes = [
        'fichero' => ['type' => 'file'],
    ];

    public function Lote()
    {
        return $this->hasManyThrough(Lote::class, ArticuloLote::class, 'articulo_id', 'lote_id','id','registre');
    }

/**
    public function fillFile($file){
        if (!$file->isValid()){
            Alert::danger(trans('messages.generic.invalidFormat'));
            return ;
        }
        $this->fichero = $file->storeAs('Articulo'
            ,$this->id.'.'.$file->getClientOriginalExtension(),'public');
        $this->save();

    }**/

}
