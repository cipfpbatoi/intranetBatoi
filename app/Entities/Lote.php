<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;


class Lote extends Model
{

    protected $table = 'lotes';
    protected $primaryKey = 'registre';
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = ['registre',  'proveedor','factura','procedencia','fechaAlta','departamento_id' ];

    use BatoiModels;

    protected $inputTypes = [
        'registre' => ['type' => 'text'],
        'procedencia' => ['type' => 'select'],
        'fechaAlta' => ['type' => 'date'],
        'departamento_id' => ['type' => 'select']
    ];

    public function ArticuloLote(){
        return $this->hasMany(ArticuloLote::class,'lote_id','registre');
    }

    public function Departamento(){
        return $this->belongsTo(Departamento::class);
    }

    public function getProcedenciaOptions()
    {
        return config('auxiliares.procedenciaMaterial');
    }

    public function getDepartamentoIdOptions()
    {
        return hazArray(Departamento::where('didactico',1)->whereNotNull('idProfesor')->get(),'id','vliteral');
    }

    public function Materiales(){
        return $this->hasManyThrough(Material::class,ArticuloLote::class, 'lote_id','articulo_lote_id','registre','id');
    }
    public function colaboraciones()
    {
        return $this->hasManyThrough(Colaboracion::class, Centro::class,'idEmpresa','idCentro','id');
    }


    public function getOrigenAttribute(){
        return $this->procedencia?config('auxiliares.procedenciaMaterial')[$this->procedencia]:config('auxiliares.procedenciaMaterial')[0];
    }

    public function getEstadoAttribute(){
        if ($this->articuloLote()->count()) {
            if ($this->materiales()->count()) {
              if ($this->materiales()->where('espacio','INVENT')->count()) {
                  return 2;
              }
              return 3;
            }
            return 1;
        }
        return 0;
    }

    public function getDepartamentAttribute(){
        return $this->Departamento?$this->Departamento->vliteral:'No assignat';
    }

}
