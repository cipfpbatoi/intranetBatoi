<?php
namespace Intranet\Entities\Poll;


use Illuminate\Database\Eloquent\Model;
use Intranet\Entities\Modulo_grupo;
use Intranet\Entities\Fct;

class Vote extends Model
{
    protected $table = 'votes';
    
    public function Option()
    {
        return $this->belongsTo(Option::class, 'option_id','id');
    }
    public function ModuloGrupo(){
        return $this->belongsTo(Modulo_grupo::class,'idOption1','id');
    }

    public function Actividad(){
        return $this->belongsTo(Actividad::class,'idOption1','id');
    }
    public function Fct(){
        return $this->belongsTo(Fct::class,'idOption1','id');
    }
    public function Profesor()
    {
        return $this->belongsTo(Profesor::class, 'idOption2','dni');
    }
    public function Poll()
    {
        return $this->belongsTo( Poll::class,'idPoll');
    }
    
    public function getIsValueAttribute()
    {
        return $this->Option->scala;
    }
    private function optionsPoll($id){
        return hazArray(Poll::find($id)->Plantilla->options,'id');
    }
    private function optionsNumericPoll($id){
        return hazArray(Poll::find($id)->Plantilla->options->where('scala','>',0),'id');
    }
    public function scopeMyVotes($query,$id,$modulo){
        return $query->where('idOption2', authUser()->dni)->whereIn('option_id',$this->optionsPoll($id))
            ->where('idOption1',$modulo);
    }
    public function scopeGetVotes($query,$poll,$option1,$option2=null){
        return $query->where('idPoll', $poll)
            ->whereIn('idOption1',$option1)
            ->where('idOption2',$option2);
    }
    public function scopeMyGroupVotes($query,$id,$modulos){
        return $query->whereIn('idOption1',$modulos)
            ->whereIn('option_id',$this->optionsPoll($id));
    }
    public function scopeAllNumericVotes($query,$id){
        return $query->whereIn('option_id',$this->optionsNumericPoll($id));
    }
    public function getGrupoAttribute(){
        return $this->ModuloGrupo->Grupo->literal;
    }
    public function getDepartmentoAttribute(){
        return $this->Profesor->departamento->literal;
    }
    public function getCicloAttribute(){
        return $this->ModuloGrupo->ModuloCiclo->Ciclo->literal;
    }
    public function getQuestionAttribute(){
        return $this->Option->question;
    }
    public function getAnswerAttribute()
    {
        if ($this->isValue) return $this->value;
        return $this->text;
    }
    public function getYearAttribute()
    {
        return substr($this->Poll->hasta,6,4);
    }
    public function getInstructorAttribute()
    {
        return $this->Fct->Instructor->nombre;
    }
    public function scopeTipusEnquesta($query,$tipusEnquesta)
    {
        $ppol = hazArray(PPoll::where('what',$tipusEnquesta)->get(),'id','id');
        $poll = hazArray(Poll::whereIn('idPPoll',$ppol)->get(),'id','id');
        return $this->whereIn('idPoll',$poll);
    }

}
