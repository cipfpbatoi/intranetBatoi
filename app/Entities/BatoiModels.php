<?php

namespace Intranet\Entities;

use Illuminate\Http\Request;
use Jenssegers\Date\Date;

trait BatoiModels
{

    public function getDateFormat()
    {
        return 'Y-m-d H:i:s';
    }

    public function getRules()
    {
        return $this->rules;
    }

    public function isRequired($campo)
    {
        if (isset($this->rules[$campo]))
            if (strpos($this->rules[$campo], 'equired'))
                return true;
        return false;
    }

    public function setInputType($id, array $tipo)
    {
        $this->inputTypes[$id] = $tipo;
    }
    public function deleteInputType($id){
        unset($this->inputTypes[$id]);
        foreach ($this->fillable as $key => $item){
            if ($item == $id) unset($this->fillable[$key]);
        }
    }
    public function addFillable($field)
    {
        $ultimo = array_pop($this->fillable);
        $this->fillable[] = $field;
        $this->fillable[] = $ultimo;
    }
    public function setRule($id,$rule)
    {
        $this->rules[$id] = $rule;
    }
    public function getRule($id)
    {
        return $this->rules[$id];
    }
    public function removeRequired($id)
    {
        if ($this->isRequired($id))
            $this->setRule($id,substr($this->getRule($id),9));
    }

//    public function getInputTypes()
//    {
//        return $this->$inputTypes;
//    }
    public function getInputType($campo)
    {
        return isset($this->inputTypes[$campo]) ? $this->inputTypes[$campo] : ['type' => 'text'];
    }

    public function isDatepicker()
    {
        $thereis = false;
        foreach ($this->inputTypes as $type)
            if (isset($type['type'])) {
                if (strpos($type['type'], 'ate') or strpos($type['type'], 'ime') or strpos($type['type'], 'ag'))
                    $thereis = true;
            }
        return $thereis;
    }

    private function EspecialFields()
    {
        $campos = [];
        foreach ($this->inputTypes as $key => $type) {
            if (isset($type['type'])) {
                if (strpos($type['type'], 'ate')) {
                    if (strpos($type['type'], 'ime'))
                        $campos[$key] = 'datetime';
                    else
                        $campos[$key] = 'date';
                }
                if (strpos($type['type'], 'ile')) {
                    $campos[$key] = 'file';
                }
                if (strpos($type['type'], 'elect')) {
                    $campos[$key] = 'select';
                }
            }
        }
        return $campos;
    }

    public function fillAll(Request $request)
    {
        $dates = $this->EspecialFields();
        foreach ($this->fillable as $key) {
            if (isset($dates[$key])) {
                if ($dates[$key] == 'date')
                    $this->$key = (new Date($request->$key))->format('Y-m-d');
                if ($dates[$key] == 'datetime')
                    $this->$key = (new Date($request->$key))->format('Y-m-d H:i');
                if ($dates[$key] == 'select')
                    $this->$key = $request->$key == ''?null:$request->$key;
            } else {
                if (isset($request->$key)) 
                    $this->$key = $request->$key;
            }
        }
        
        $this->save();
        
        if ($request->hasFile('fichero')) {
            if ($request->file('fichero')->isValid()) {
                $clase = getClase($this) == 'Documento'?$this->tipoDocumento:getClase($this);
                $extension = $request->file('fichero')->getClientOriginalExtension();
                $nombre = isset($this->id)?$this->id.'_':'';
                if (isset($this->fileField)){
                    $field = $this->fileField;
                    $nombre .= $this->$field.'_';
                }
                
                $nombre .= $clase . '.' . $extension;
                $directorio = '/gestor/' . Curso() . '/' . $clase;
                $this->fichero = $request->file('fichero')->storeAs($directorio,$nombre);
                $this->save();
            } else {
                Alert::danger(trans('messages.generic.invalidFormat'));
            }
        }
        $primaryKey = isset($this->primaryKey) ? $this->primaryKey : 'id';
        return $this->$primaryKey;
    }

    //que és cada camp y quines opcions te
    public function fillDefautOptions()
    {
        $InputType = [];
        foreach ($this->getfillable() as $property) {
            $parametres = [];
            $inputTpe = $this->getInputType($property);
            $InputType[$property]['type'] = isset($inputTpe['type']) ? $inputTpe['type'] : 'text';
            switch ($InputType[$property]['type']){
                case 'name':
                case 'card':
                case 'time':
                case 'date':
                case 'datetime' :      
                    $parametres['template'] = 'themes/bootstrap/fields/'.$InputType[$property]['type'];
                    $InputType[$property]['type']='text';
                    break;
            }
            $ph = !strpos(trans('validation.attributes.' . $property), 'alidation.') ? trans('validation.attributes.' . $property) : ucwords($property);
            
            $parametres['id'] = $property . '_id';
            $parametres['ph'] = $ph;
            $parametres['class'] = 'col-md-7 col-xs-12 ' . $InputType[$property]['type'];
            
            $InputType[$property]['default'] = isset($inputTpe['default']) ? $inputTpe['default'] : null;
            
            if (isset($inputTpe['disabled']))
                $parametres = array_merge($parametres, ['disabled' => 'disabled']);
             if (isset($inputTpe['disableAll']))
                $parametres = array_merge($parametres, ['disabled' => 'on']);
            if ($this->isRequired($property))
                $parametres = array_merge($parametres, ['required']);
            if (isset($inputTpe['inline']))
                $parametres = array_merge($parametres, ['inline' => 'inline']);
            
            $InputType[$property]['params'] = $parametres;
        }
        return($InputType);
    }
    
    protected function llena($parametres)
    {
        if (isset($parametres)) {
            foreach ($parametres as $key => $valor) {
                $this->$key = $valor;
            }
        }
    }
    
    public function getLinkAttribute()
    {
        if (isset($this->fichero) && file_exists(storage_path('app/' . $this->fichero)))
            return true;
        else
            return false;
    }

}
