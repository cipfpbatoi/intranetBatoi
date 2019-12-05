<?php

namespace Intranet\Entities\Poll;


use Intranet\Entities\BatoiModels;
use Illuminate\Database\Eloquent\Model;


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
        return $this->hasOne(PPoll::class,'id','idPPoll');
    }

    public function getActiuAttribute(){
        return vigente($this->desde,$this->hasta)?'Activa':'No activa';
    }

    public function getKeyUserAttribute(){
        $modelo = $this->modelo;
        return $modelo::keyInterviewed();
    }
    public function getAnonymousAttribute(){
        return $this->Plantilla->anonymous;
    }
    public function getQueAttribute(){
        return $this->Plantilla->what;
    }
    public function getModeloAttribute(){
        return 'Intranet\\Entities\\Poll\\'.$this->Plantilla->what;
    }
    public function getVistaAttribute(){
        $modelo = $this->modelo;
        return $modelo::vista();
    }

    public function getIdPPollOptions(){
        return hazArray(PPoll::all(),'id','title');
    }



}
