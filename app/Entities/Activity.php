<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Styde\Html\Facades\Alert;

class Activity extends Model
{
    protected $fillable = ['action', 'model_class', 'model_id', 'comentari', 'document', 'author_id', 'created_at'];

    /*
    |--------------------------------------------------------------------------
    | Factory Method per a Crear una Activitat
    |--------------------------------------------------------------------------
    */
    public static function record(string $action, ?Model $model = null, ?string $comentari = null, ?string $fecha = null, ?string $document = null)
    {
        $activity = new self([
            'action'     => $action,
            'comentari'  => $comentari,
            'document'   => $document,
            'model_class' => $model ? get_class($model) : null,
            'model_id'   => $model?->getKey(),
            'created_at' => $fecha ? fechaInglesaLarga($fecha) : now(),
        ]);

        $user = auth()->user();
        if ($user) {
            $user->Activity()->save($activity);
        }

        self::notifyUser($activity);
        return $activity;
    }

    /*
    |--------------------------------------------------------------------------
    | Notificació d'Alertes
    |--------------------------------------------------------------------------
    */
    private static function notifyUser(Activity $activity)
    {
        if ($activity->model_class) {
            $modelName = trans('models.modelos.' . class_basename($activity->model_class));
            $message = trans("messages.generic.{$activity->action}");
            Alert::success("$modelName $message");
        }
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
        return $query->where('model_id', $id)->orWhere('model_id', $colaboracion);
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

    /*
    |--------------------------------------------------------------------------
    | Mètode de Presentació - Ha de traslladar-se a un View Component o Blade
    |--------------------------------------------------------------------------
    */
    public function render()
    {
        return view('partials.activity', [
            'class'     => $this->getIconClass(),
            'id'        => $this->id,
            'action'    => $this->getActionIcon(),
            'fecha'     => fechaCurta($this->created_at),
            'comentari' => $this->comentari
        ]);
    }

    private function getIconClass()
    {
        return match (firstWord($this->document)) {
            'Recordatori' => 'flag',
            'Informació'  => 'lock',
            'Revisió'     => 'check',
            'Sol·licitud' => 'bell',
            default       => null
        };
    }

    private function getActionIcon()
    {
        return match ($this->action) {
            'email'  => 'envelope',
            'visita' => 'car',
            'phone'  => 'phone',
            'book'   => 'book',
            default  => null
        };
    }
}
