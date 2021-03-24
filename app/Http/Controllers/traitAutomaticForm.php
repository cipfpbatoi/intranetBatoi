<?php

namespace Intranet\Http\Controllers;

use Response;


trait traitAutomaticForm{


    private function translate($key){
        return !strpos(trans('validation.attributes.' . $key), 'alidation.') ? trans('validation.attributes.' . $key) : ucwords($key);
    }

    public function fillDefaultOptionsToForm()
    {
        $InputType = [];
        foreach ($this->formFields as $key => $property) {
            $parametres = [];
            $InputType[$key]['type'] = $this->aspect($parametres,$property);

            $parametres['id'] = $key . '_id';
            $parametres['ph'] = $this->translate($key);
            $parametres['class'] = 'col-md-7 col-xs-12 ' . $InputType[$key]['type'];

            $InputType[$key]['default'] = null;

            if ($property == 'disabled'){
                $parametres = array_merge($parametres, ['disabled' => 'disabled']);
            }
            if ($property == 'disableAll'){
                $parametres = array_merge($parametres, ['disabled' => 'on']);
            }
            if ($property == 'inline'){
                $parametres = array_merge($parametres, ['inline' => 'inline']);
            }

            $InputType[$key]['params'] = $parametres;
        }
        return($InputType);
    }

    private function aspect(&$parametres,$originalType){
        switch ($originalType){
            case 'multiple' :
                $finalType='select';
                $parametres[] = 'multiple';
                break;
            case 'name':
            case 'card':
            case 'time':
            case 'date':
            case 'datetime':
                $parametres['template'] = 'themes/bootstrap/fields/'.$originalType;
                $finalType='text';
                break;
            default:
                $finalType = $originalType;
        }
        return $finalType;
    }
    
}
