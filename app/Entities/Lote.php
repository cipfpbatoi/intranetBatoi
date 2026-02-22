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

    use \Intranet\Entities\Concerns\BatoiModels;

    protected $inputTypes = [
        'registre' => ['type' => 'text'],
        'procedencia' => ['type' => 'select'],
        'fechaAlta' => ['type' => 'date'],
        'departamento_id' => ['type' => 'select']
    ];

    public function ArticuloLote()
    {
        return $this->hasMany(ArticuloLote::class, 'lote_id', 'registre');
    }

    public function Departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function getProcedenciaOptions()
    {
        return config('auxiliares.procedenciaMaterial');
    }

    public function getDepartamentoIdOptions()
    {
        return hazArray(Departamento::where('didactico', 1)->whereNotNull('idProfesor')->get(), 'id', 'vliteral');
    }

    public function Materiales()
    {
        return $this->hasManyThrough(
            Material::class,
            ArticuloLote::class,
            'lote_id',
            'articulo_lote_id',
            'registre',
            'id'
        );
    }
     
    public function getOrigenAttribute()
    {
        return $this->procedencia?
            config('auxiliares.procedenciaMaterial')[$this->procedencia]:
            config('auxiliares.procedenciaMaterial')[0];
    }

    public function getEstadoAttribute()
    {
        if ($this->resolveArticuloLoteCount() === 0) {
            return 0;
        }

        [$materialesTotal, $materialesInvent] = $this->resolveMaterialesStats();

        if ($materialesTotal === 0) {
            return 1;
        }

        return $materialesInvent > 0 ? 2 : 3;
    }

    private function resolveArticuloLoteCount(): int
    {
        if (array_key_exists('articulo_lote_count', $this->attributes)) {
            return (int) $this->attributes['articulo_lote_count'];
        }

        if ($this->relationLoaded('ArticuloLote')) {
            return $this->getRelation('ArticuloLote')->count();
        }

        return $this->ArticuloLote()->count();
    }

    /**
     * @return array{0:int,1:int}
     */
    private function resolveMaterialesStats(): array
    {
        if (array_key_exists('materiales_count', $this->attributes)
            && array_key_exists('materiales_invent_count', $this->attributes)) {
            return [
                (int) $this->attributes['materiales_count'],
                (int) $this->attributes['materiales_invent_count'],
            ];
        }

        if ($this->relationLoaded('Materiales')) {
            $materiales = $this->getRelation('Materiales');

            return [
                $materiales->count(),
                $materiales->where('espacio', 'INVENT')->count(),
            ];
        }

        $stats = $this->Materiales()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN espacio = 'INVENT' THEN 1 ELSE 0 END) as invent_total")
            ->first();

        return [
            (int) ($stats->total ?? 0),
            (int) ($stats->invent_total ?? 0),
        ];
    }

    public function getEstatAttribute()
    {
        return config('auxiliares.estadosLote')[$this->estado];
    }

    public function getDepartamentAttribute()
    {
        return $this->Departamento?$this->Departamento->vliteral:'No assignat';
    }

}
