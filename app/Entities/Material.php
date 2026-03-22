<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Events\ActivityReport;
use Intranet\Presentation\Crud\MaterialCrudSchema;
 
/**
 * Model de material inventariable/no inventariable.
 */
class Material extends Model
{
    use \Intranet\Entities\Concerns\BatoiModels;
 
    protected $table = 'materiales';
    public $timestamps = false;
    protected $fillable = [
        'nserieprov',
        'descripcion',
        'marca',
        'modelo',
        'ISBN',
        'espacio',
        'procedencia',
        'proveedor',
        'estado',
        'unidades',
        'inventariable',
        'articulo_lote_id'
    ];

   
    protected $rules = MaterialCrudSchema::RULES;
    protected $inputTypes = MaterialCrudSchema::INPUT_TYPES;
    protected $dispatchesEvents = [
        'saved' => ActivityReport::class,
        'deleted' => ActivityReport::class,
    ];

    protected $attributes = ['estado'=>1];


    public function Espacios()
    {
        return $this->belongsTo(Espacio::class, 'espacio', 'aula');
    }

    public function LoteArticulo()
    {
        return $this->belongsTo(ArticuloLote::class, 'articulo_lote_id');
    }


    public function getEstadoOptions()
    {
        return config('auxiliares.estadoMaterial');
    }

    public function getStateAttribute()
    {
        return config('auxiliares.estadoMaterial')[$this->estado];
    }

    public function getEspacioOptions()
    {
        return hazArray(Espacio::all(), 'aula', 'descripcion');
    }

    /**
     * Retorna l'espai visible en inventari.
     *
     * Si hi ha una proposta pendent de canvi d'ubicació (tipo=1, estado=0),
     * es mostra l'etiqueta de proposta; en qualsevol altre cas, l'espai real.
     *
     * @return string
     */
    public function getEspaiAttribute()
    {
        $tePropostaUbicacioPendent = MaterialBaja::query()
            ->where('idMaterial', $this->id)
            ->where('tipo', 1)
            ->where('estado', 0)
            ->exists();

        if ($tePropostaUbicacioPendent) {
            return 'Proposta Nova Ubicació';
        }

        return $this->espacio;
    }

    public function getProcedenciaOptions()
    {
        return config('auxiliares.procedenciaMaterial');
    }

}
