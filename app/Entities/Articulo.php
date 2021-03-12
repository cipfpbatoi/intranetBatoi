<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;



class Articulo extends Model
{

    use BatoiModels;

    protected   $table = 'articulos';
    public      $timestamps = false;
    protected   $fillable = [ 'descripcion', 'fichero'];
    protected   $inputTypes = [
        'fichero' => ['type' => 'file'],
    ];

    public function Lote()
    {
        return $this->hasManyThrough(Lote::class, ArticuloLote::class, 'articulo_id', 'lote_id','id','registre');
    }

    public function getMiniaturaAttribute()
    {
        if ($this->fichero)
            return "<img src='".asset('/storage/'.$this->fichero)."' heigth='40px' width='60px'/>";
        else
            return "Sense imatge";
    }

    public function fillFile($file){
        if (!$file->isValid()){
            Alert::danger(trans('messages.generic.invalidFormat'));
            return ;
        }
        $this->fichero = $file->storeAs('Articulos'
            ,$this->id.'.'.$file->getClientOriginalExtension(),'public');
        $this->save();
    }

}
