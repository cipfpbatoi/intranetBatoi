<?php

namespace Intranet\Entities;

use Illuminate\Http\Request;
use Jenssegers\Date\Date;

/**
 * Trait BatoiModels
 * @package Intranet\Entities
 */
trait BatoiModels
{

    /**
     * @return string
     */
    public function getDateFormat()
    {
        return 'Y-m-d H:i:s';
    }

    /**
     * @return mixed
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * @param $campo
     * @return bool
     */
    public function isRequired($campo)
    {
        if (isset($this->rules[$campo])&&strpos($this->rules[$campo], 'equired')) return true;
        return false;
    }

    /**
     * @param $id
     * @param array $tipo
     */
    public function setInputType($id, array $tipo)
    {
        $this->inputTypes[$id] = $tipo;
    }

    /**
     * @param $id
     */
    public function deleteInputType($id){
        unset($this->inputTypes[$id]);
        foreach ($this->fillable as $key => $item){
            if ($item == $id) unset($this->fillable[$key]);
        }
    }

    /**
     * @param $field
     * @param bool $first
     */
    public function addFillable($field, $first=false)
    {
        if ($first){
            array_unshift($this->fillable, $field);
        }
        else {
            $ultimo = array_pop($this->fillable);
            $this->fillable[] = $field;
            $this->fillable[] = $ultimo;
        }
    }

    /**
     * @param $id
     * @param $rule
     */
    public function setRule($id, $rule)
    {
        $this->rules[$id] = $rule;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getRule($id)
    {
        return $this->rules[$id];
    }
//    public function removeRequired($id)
//    {
//        if ($this->isRequired($id))
//            $this->setRule($id,substr($this->getRule($id),9));
//    }

    /**
     * @param $campo
     * @return array
     */
    public function getInputType($campo)
    {
        return isset($this->inputTypes[$campo]) ? $this->inputTypes[$campo] : ['type' => 'text'];
    }

    /**
     * @return bool
     */
    public function existsDatepicker()
    {
        foreach ($this->inputTypes as $type)
            if ($this->isTypeDate($type)) return true;
        return false;
    }

    /**
     * @param $type
     * @return bool
     */
    public function isTypeDate($type)
    {
        if (isset($type['type']) && (strpos($type['type'], 'ate') or strpos($type['type'], 'ime') or strpos($type['type'], 'ag'))) return true;
        return false;
    }

//    private function lookForEspecialFields()
//    {
//        $campos = [];
//        foreach ($this->inputTypes as $key => $type) {
//            if (isset($type['type'])) {
//                if (strpos($type['type'], 'ate')) {
//                    if (strpos($type['type'], 'ime'))
//                        $campos[$key] = 'datetime';
//                    else
//                        $campos[$key] = 'date';
//                }
//                if (strpos($type['type'], 'ile')) {
//                    $campos[$key] = 'file';
//                }
//                if (strpos($type['type'], 'elect')) {
//                    $campos[$key] = 'select';
//                }
//            }
//        }
//        return $campos;
//    }

    /**
     * @param Request $request
     */
    public function fillFile($file){
        if (!$file->isValid()){
            Alert::danger(trans('messages.generic.invalidFormat'));
            return ;
        }
        $clase = getClase($this) == 'Documento'?$this->tipoDocumento:getClase($this);
        $this->fichero = $file->storeAs($this->getDirectory($clase)
            ,$this->getFileName($file->getClientOriginalExtension(),$clase));
        $this->save();
        
    }

    private function getDirectory($clase){
        return '/gestor/' . Curso() . '/' . $clase;
    }

    private function getFileName($extension,$clase)
    {
        $nombre = isset($this->id)?$this->id.'_':'';
        if (isset($this->fileField)){
            $field = $this->fileField;
            $nombre .= $this->$field.'_';
        }
        $nombre .= $clase . '.' . $extension;
        return $nombre;
    }


    /**
     * @param $key
     * @param $value
     * @return mixed|string|null
     */
    private function fillField($key, $value){
        $type = isset($this->inputTypes[$key]['type'])?$this->inputTypes[$key]['type']:null;
        if ($type == 'date') return (new Date($value))->format('Y-m-d');
        if ($type == 'datetime') return (new Date($value))->format('Y-m-d H:i');
        if ($type == 'select') return $value == ''?null:$value;
        if ($type == 'file') return $value = $this->$key;
        return $value;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function fillAll(Request $request)
    {
        $fillable = $this->notFillable?array_diff($this->fillable,$this->notFillable):$this->fillable;
        //$dates = $this->lookForEspecialFields();
        foreach ($fillable as $key)  $this->$key = $this->fillField($key,$request->$key);

        $this->save();
        
        if ($request->hasFile('fichero')) $this->fillFile($request->file('fichero'));
        
        $primaryKey = isset($this->primaryKey) ? $this->primaryKey : 'id';
        return $this->$primaryKey;
    }


    /**
     * @return array
     */
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
                case 'multiple' :
                    $InputType[$property]['type']='select';
                    $parametres[] = 'multiple';
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


    /**
     * @param $field
     * @return bool
     */
    public function has($field)
    {
        if (isset($this->$field) || is_null($this->$field)) return true;
        return false;
    }

    /**
     * @return bool
     */
    public function getLinkAttribute()
    {
        if (isset($this->fichero) && file_exists(storage_path('app/' . $this->fichero)))
            return true;
        return false;
    }

    public function saveContact($contacto,$email){
        $this->contacto = $contacto;
        $this->email = $email;
        return $this->save();
    }
}
