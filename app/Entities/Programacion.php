<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Events\ActivityReport;
use Intranet\Events\PreventAction;
use Intranet\Services\General\StateService;

class Programacion extends Model
{

    use \Intranet\Entities\Concerns\BatoiModels;
    
    public $fileField = 'idModuloCiclo';
    protected $table = "programaciones";
    protected $fillable = [
        'idModuloCiclo',
        'fichero',
    ];
    protected $rules = [
        'fichero' => 'mimes:pdf'
    ];
    protected $inputTypes = [
        'fichero' => ['type' => 'file'],
        'idModuloCiclo' => ['type' => 'select']
    ];
    
    protected $dispatchesEvents = [
        'deleting' => PreventAction::class,
        'created' => ActivityReport::class,
        'deleted' => ActivityReport::class,
    ];
    protected $hidden = ['created_at', 'updated_at'];

    protected $attributes = ['criterios'=>0,'metodologia'=>0];
    
    public function ModuloCiclo()
    {
        return $this->belongsTo(Modulo_ciclo::class, 'idModuloCiclo', 'id');
    }

    public function Departament()
    {
        return $this->hasOneThrough(Departamento::class, Modulo_ciclo::class, 'id', 'id','idModuloCiclo','idDepartamento');
    }
    public function Ciclo()
    {
        return $this->hasOneThrough(Ciclo::class, Modulo_ciclo::class,'id','id','idModuloCiclo','idCiclo');
    }
    public function Modulo()
    {
        return $this->hasOneThrough(Modulo::class, Modulo_ciclo::class,'id','codigo','idModuloCiclo','idModulo');
    }

    public function Profesor()
    {
        return $this->belongsTo(Profesor::class, 'Profesor', 'dni');
    }

    public function Grupo()
    {
        return $this->hasOneThrough(Grupo::class, Modulo_grupo::class,'id','codigo','idModuloCiclo','idGrupo');
    }



    public function getidModuloCicloOptions()
    {
        $horas = Horario::query()
            ->select('modulo', 'idGrupo')
            ->Profesor(authUser()->dni)
            ->whereNotNull('idGrupo')
            ->whereNotNull('modulo')
            ->whereNotIn('modulo', config('constants.modulosNoLectivos'))
            ->with('Grupo:codigo,idCiclo')
            ->distinct()
            ->get();

        $pairs = $horas
            ->filter(fn ($hora) => $hora->Grupo && $hora->Grupo->idCiclo)
            ->map(fn ($hora) => [
                'idModulo' => $hora->modulo,
                'idCiclo' => $hora->Grupo->idCiclo,
            ])
            ->unique(fn (array $pair) => $pair['idModulo'] . '|' . $pair['idCiclo'])
            ->values();

        if ($pairs->isEmpty()) {
            return [];
        }

        $validKeys = $pairs
            ->mapWithKeys(fn (array $pair) => [$pair['idModulo'] . '|' . $pair['idCiclo'] => true]);

        return Modulo_ciclo::query()
            ->whereIn('idModulo', $pairs->pluck('idModulo')->unique())
            ->whereIn('idCiclo', $pairs->pluck('idCiclo')->unique())
            ->with(['Modulo:codigo,literal', 'Ciclo:id,literal'])
            ->get()
            ->filter(fn ($mc) => isset($validKeys[$mc->idModulo . '|' . $mc->idCiclo]))
            ->mapWithKeys(fn ($mc) => [$mc->id => $mc->Modulo->literal . ' - ' . $mc->Ciclo->literal])
            ->all();
    }

    public function scopeMisProgramaciones($query,$dni = null)
    {
        $profesor = $dni??authUser()->dni;
        $horas = Horario::query()
            ->select('modulo', 'idGrupo')
            ->distinct()
            ->whereNotNull('idGrupo')
            ->whereNotNull('modulo')
            ->whereNotIn('modulo', config('constants.modulosSinProgramacion'))
            ->Profesor($profesor)
            ->with('Grupo:codigo,idCiclo')
            ->get();

        $pairs = $horas
            ->filter(fn ($hora) => $hora->Grupo && $hora->Grupo->idCiclo)
            ->map(fn ($hora) => [
                'idModulo' => $hora->modulo,
                'idCiclo' => $hora->Grupo->idCiclo,
            ])
            ->unique(fn (array $pair) => $pair['idModulo'] . '|' . $pair['idCiclo'])
            ->values();

        if ($pairs->isEmpty()) {
            return $query->whereIn('idModuloCiclo', []);
        }

        $validKeys = $pairs
            ->mapWithKeys(fn (array $pair) => [$pair['idModulo'] . '|' . $pair['idCiclo'] => true]);

        $modulos = Modulo_ciclo::query()
            ->whereIn('idModulo', $pairs->pluck('idModulo')->unique())
            ->whereIn('idCiclo', $pairs->pluck('idCiclo')->unique())
            ->get()
            ->filter(fn ($mc) => isset($validKeys[$mc->idModulo . '|' . $mc->idCiclo]))
            ->pluck('id')
            ->unique()
            ->values()
            ->all();

        return $query->whereIn('idModuloCiclo', $modulos);
    }

    public function scopeDepartamento($query, $departamento = null)
    {
        $departamento = $departamento ?? authUser()->departamento;

        return $query->whereIn(
            'idModuloCiclo',
            hazArray(Modulo_ciclo::where('idDepartamento', $departamento)->get(), 'id', 'id')
        );
    }
    
    public function nomFichero()
    {
        $fichero = basename($this->fichero);
        $partido = explode('_',$fichero);
        if (count($partido) == 3){
            return $partido[0].'_'.$partido[1];
        }
        else
        {
            return $partido[0];
        }
    }

    public function getXdepartamentoAttribute(){
        return $this->Departament->literal??'';
    }
    public function getXModuloAttribute(){
        return $this->Modulo->literal??'';
    }
    public function getXCicloAttribute(){
        return $this->Ciclo->literal??'';
    }
    public function getDescripcionAttribute()
    {
        return isset($this->ModuloCiclo->idCiclo)?$this->ModuloCiclo->Aciclo." - ".$this->ModuloCiclo->Xmodulo:'';
    }
    public function getXnombreAttribute()
    {
        return $this->Profesor->ShortName??'';
    }

    public function getSituacionAttribute()
    {
        return isblankTrans('models.Programacion.' . $this->estado)
            ? trans('messages.situations.' . $this->estado)
            : trans('models.Programacion.' . $this->estado);
    }

    public static function resolve($id, $mensaje = null)
    {
        $programacion = Programacion::findOrFail($id);
        $staServ = new StateService($programacion);
        return $staServ->putEstado( config('modelos.' . getClass(static::class) . '.resolve'), $mensaje);
    }
    
}
