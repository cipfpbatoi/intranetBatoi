<?php
namespace Intranet\Finders;

use Intranet\Entities\Activity;

abstract class Finder {

    protected $dni;
    protected $document;
    protected $modelo;

    public function __construct($document)
    {
        $this->dni = AuthUser()->dni??apiAuthUser()->dni;
        $this->document = $document;
    }

    protected function existsActivity($id){
        if ($this->document->unique) {
            return Activity::where('model_class',$this->modelo)->where('model_id',$id)->where('document','=',$this->document->subject)->count();
        } else {
            return 0;
        }
    }

    public function getDocument(){
        return $this->document;
    }
}