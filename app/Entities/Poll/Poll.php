<?php

namespace Intranet\Entities\Poll;


use Intranet\Entities\BatoiModels;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Date\Date;


class Poll extends Model
{
    use BatoiModels;
    
    protected $fillable = ['title','desde','hasta','idPPoll'];
    protected $rules = [
        'title' => 'required',
        'desde' => 'required',
        'hasta' => 'required',
        'idPPoll' => 'required'
    ];
    protected $inputTypes = [
        'desde' => ['type' => 'date'],
        'hasta' => ['type' => 'date'],
        'idPPoll' => ['type' => 'select']
    ];
    public $timestamps = false;
    
    /**
     * A poll has many options related to
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function Plantilla()
    {
        return $this->hasOne(PPoll::class, 'id', 'idPPoll');
    }

    public function getStateAttribute()
    {
        if (vigente($this->desde, $this->hasta)) {
            return 'Activa';
        }
        $fin = new Date($this->hasta);
        if (hoy()>$fin->format('Y-m-d')) {
            return 'Finalizada';
        }
        return 'No activa';
    }

    public function getKeyUserAttribute()
    {
        $modelo = $this->modelo;
        return $modelo::keyInterviewed();
    }
    public function getAnonymousAttribute()
    {
        return $this->Plantilla->anonymous??false;
    }
    public function getQueAttribute()
    {
        return $this->Plantilla->what??'';
    }
    public function getRemainsAttribute()
    {
        return $this->Plantilla->remains??null;
    }
    public function getModeloAttribute()
    {
        return 'Intranet\\Entities\\Poll\\'.$this->Plantilla->what;
    }
    public function getVistaAttribute()
    {
        $modelo = $this->modelo;
        return $modelo::vista();
    }

    public function getIdPPollOptions()
    {
        return hazArray(PPoll::all(), 'id', 'title');
    }

    public function getDesdeAttribute($entrada)
    {
        //desde
        $fecha = new Date($entrada);
        return $fecha->format('d-m-Y');
    }

    public function getHastaAttribute($entrada)
    {
        // hasta
        $fecha = new Date($entrada);
        return $fecha->format('d-m-Y');
    }
}
