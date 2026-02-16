<?php

namespace Intranet\Entities;

use Intranet\Application\Activity\ActivityService;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = ['action', 'model_class', 'model_id', 'comentari', 'document', 'author_id', 'created_at'];

    /**
     * Manté API estàtica legacy delegant en el servei d'aplicació.
     */
    public static function record(
        string $action,
        ?Model $model = null,
        ?string $comentari = null,
        ?string $fecha = null,
        ?string $document = null
    ): self
    {
        return app(ActivityService::class)->record($action, $model, $comentari, $fecha, $document);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes de Consulta
    |--------------------------------------------------------------------------
    */
    public function scopeProfesor($query, $profesor)
    {
        return $query->where('author_id', $profesor);
    }

    public function scopeModelo($query, $modelo)
    {
        return $query->where('model_class', 'Intranet\Entities\\' . $modelo);
    }

    public function scopeNotUpdate($query)
    {
        return $query->whereNotIn('action', ['update', 'create', 'delete']);
    }

    public function scopeMail($query)
    {
        return $query->whereIn('action', ['email', 'phone', 'visita', 'review']);
    }

    public function scopeId($query, $id)
    {
        return $query->where('model_id', $id);
    }

    public function scopeIds($query, $ids)
    {
        return $query->whereIn('model_id', $ids);
    }

    public function scopeRelationId($query, $id)
    {
        $colaboracion = Fct::find($id)?->idColaboracion;
        return $query->where(function ($inner) use ($id, $colaboracion) {
            $inner->where('model_id', $id);
            if ($colaboracion !== null) {
                $inner->orWhere('model_id', $colaboracion);
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relacions
    |--------------------------------------------------------------------------
    */
    public function propietario()
    {
        return $this->belongsTo(Profesor::class, 'author_id', 'dni');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors i Mutators
    |--------------------------------------------------------------------------
    */
    public function getUpdatedAtAttribute($value)
    {
        return $value ? (new \DateTime($value))->format('d-m-Y H:i') : null;
    }

}
