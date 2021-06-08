<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Styde\Html\Facades\Alert;

class Activity extends Model
{
    protected $fillable = ['comentari'];


    public static function record($action, Model $model = null,$comentari = null,$fecha = null,$document=null)
    {
        $activity = new Activity();
        $activity->action = $action;
        if ($model) {
            $key = $model->primaryKey;
            $activity->model_class = get_class($model);
            $activity->model_id = $model->$key;
        }
        $activity->comentari = $comentari;
        $activity->document = $document;
        if ($fecha){
            $activity->setCreatedAt(FechaInglesaLarga($fecha));
            $activity->setUpdatedAt(FechaInglesaLarga($fecha));
        }

        auth()->user()->Activity()->save($activity);
        Alert::success(trans('models.modelos.' . substr($activity->model_class, 18)) . ' ' . trans("messages.generic.$action"));
    }

    public function scopeProfesor($query, $profesor)
    {
        return $query->where('author_id', '=', $profesor);
    }

    public function getUpdateAtAttribute($salida)
    {
        $fecha = new Date($salida);
        return $fecha->format('d-m-Y H:i');
    }

    public function Propietario()
    {
        return $this->belongsTo(Profesor::class, 'author_id', 'dni');
    }

    public function scopeModelo($query,$modelo){
        return $query->where('model_class','Intranet\Entities\\'.$modelo);
    }

    public function scopeMail($query)
    {
        return $query->where('action','email')->orWhere('action','phone')->orWhere('action','visita')->orWhere('action','review');
    }
    public function scopeId($query,$id){
        return $query->where('model_id',$id);
    }
    public function scopeIds($query,$ids){
        return $query->whereIn('model_id',$ids);
    }
    public function scopeRelationId($query,$id){
        $colaboracion = Fct::find($id)->idColaboracion;
        return $query->where('model_id',$id)->orWhere('model_id',$colaboracion);
    }

}
