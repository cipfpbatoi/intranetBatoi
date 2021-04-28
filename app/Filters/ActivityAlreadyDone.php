<?php

namespace Intranet\Filters;
use Intranet\Entities\Activity;

class ActivityAlreadyDone implements Filter {

    private $document;


    public function __construct($document){
        $this->document = $document;
    }

    public function exec(&$elements){
        foreach ($elements as $element){
            if (!$this->existsActivity($element->id) && $this->checkFcts($this->document->fcts,count($element->Fcts))){
                $element->marked = true;
            } else {
                $element->marked = false;
            }
        }
    }

    public function getDocument()
    {
        return $this->document;
    }

    private function existsActivity($id){
        return Activity::where('model_class','Intranet\Entities\Colaboracion')->where('model_id',$id)->where('document','=',$this->document->subject)->count();
    }
    private function checkFcts($needsFcts,$existsFcts){
        if ($needsFcts && $existsFcts) {
            return true;
        }
        if (!$needsFcts && !$existsFcts) {
            return true;
        }
        return false;
    }



}