<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\Auth;
use Intranet\Events\ActivityReport;
use Intranet\Events\PreventAction;

class Programacion extends Model
{

    use BatoiModels,TraitEstado;
    
    public $fileField = 'idModulo';
    protected $table = "programaciones";
    protected $fillable = [
        'idModulo',
        'ciclo',
        'idProfesor',
        'desde',
        'hasta',
        'fichero',
    ];
    protected $rules = [
        'desde' => 'required|date',
        'hasta' => 'required|date|after:desde',
        'idModulo' => 'required',
        'fichero' => 'mimes:pdf'
    ];
    protected $inputTypes = [
        'idModulo' => ['type' => 'select'],
        'idProfesor' => ['type' => 'hidden'],
        'desde' => ['type' => 'date'],
        'hasta' => ['type' => 'date'],
        'fichero' => ['type' => 'file'],
        
    ];
    protected $dispatchesEvents = [
        'deleting' => PreventAction::class,
        'created' => ActivityReport::class,
        'deleted' => ActivityReport::class,
    ];
    protected $hidden = ['created_at', 'updated_at'];

    public function Modulo()
    {
        return $this->belongsTo(Modulo::class, 'idModulo', 'codigo');
    }

    public function Profesor()
    {
        return $this->belongsTo(Profesor::class, 'idProfesor', 'dni');
    }

    public function __construct()
    {
        if (AuthUser()) $this->idProfesor = AuthUser()->dni;
        $this->desde = '01-09-'.substr(Curso(),0,4);
        $this->hasta = '31-07-'.substr(Curso(),5,4);
        $this->criterios = 0;
        $this->metodologia = 0;
    }

    public function getDesdeAttribute($entrada)
    {
        $fecha = new Date($entrada);
        return $fecha->format('d-m-Y');
    }

    public function getHastaAttribute($salida)
    {
        $fecha = new Date($salida);
        return $fecha->format('d-m-Y');
    }

    public function getidModuloOptions()
    {
        $misModulos = [];
        $modulos = Modulo::Mismodulos()->Lectivos()->get();
        foreach ($modulos as $modulo){
            $dciclo = isset($modulo->Ciclo->ciclo)?$modulo->Ciclo->ciclo:'';
            $misModulos[$modulo->codigo] = $modulo->literal." - $dciclo - ";
        }
        return $misModulos;
    }

    public function scopeMisProgramaciones($query,$profesor = null)
    {
        if (!$profesor) $profesor = AuthUser()->dni;
        $modulos = Horario::select('modulo')
                ->distinct()
                ->Profesor($profesor)
                ->where('modulo', '!=', null)
                ->get()
                ->toarray();
        return $query->whereIn('idModulo', $modulos)->where('desde','<=',Hoy())->where('hasta','>=',Hoy());
    }

    public function scopeDepartamento($query)
    {
        $modulos = Modulo::select('codigo')
                ->Departamento()
                ->get()
                ->toarray();
        return $query->whereIn('idModulo', $modulos)->where('desde','<=',Hoy())->where('hasta','>=',Hoy());
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
        return isset($this->Modulo->Ciclo->Departament->cliteral)?$this->Modulo->Departament->literal:'';
    }
    public function getXModuloAttribute(){
        return $this->Modulo->literal;
    }
    public function getXNombreAttribute(){
        return $this->Profesor->fullName;
    }
    public function getSituacionAttribute(){
        return isblankTrans('models.Comision.' . $this->estado) ? trans('messages.situations.' . $this->estado) : trans('models.Comision.' . $this->estado);
    }
    public static function resolve($id,$mensaje = null)
    {
        $elemento = Programacion::findorFail($id);
        Documento::crea($elemento,['tipoDocumento' => 'Programacion' ,'fichero' => $elemento->fichero,
            'modulo'=>$elemento->Modulo->literal,'propietario' => $elemento->Profesor->FullName,
            'descripcion'=>'Autorizada dia '. Hoy('d-m-Y'),'ciclo'=> $elemento->ciclo, 'tags' => 'Programació']);
        return static::putEstado($id, config('modelos.' . getClass(static::class) . '.resolve'), $mensaje);
    }
    
}
