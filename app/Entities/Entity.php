<?php


namespace Intranet\Entities;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Jenssegers\Date\Date;

/**
 * Class Entity
 * @package Intranet\Entities
 */
class Entity extends Model
{
    protected $formFields = [];
    /**
     * @return string
     */
    public function getDateFormat()
    {
        return 'Y-m-d H:i:s';
    }

    /**
     * @param $id
     * @param array $tipo
     */
    public function setType($id, array $tipo)
    {
        $this->formFields[$id] = $tipo;
    }
    /**
     * @param $id
     */
    public function deleteType($id){
        unset($this->formFields[$id]);
    }

    public function getType($id)
    {
        return $this->formFields[$id];
    }


    /**
     * @return bool
     */
    public function existsDatepicker()
    {
        if (isset($this->type)) {
            foreach ($this->formFields as $type) {
                if ($this->isTypeDate($type)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param $type
     * @return bool
     */
    private function isTypeDate($type)
    {
        if (strpos($type, 'ate') || strpos($type, 'ime') || strpos($type, 'ag')) {
            return true;
        }
        return false;
    }


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
        return $nombre . $clase . '.' . $extension;
    }

    /**
     * @param $key
     * @param $value
     * @return mixed|string|null
     */
    private function fillField($key, $value){
        $type = $this->formFields[$key]??null;
        if ($type == 'date') {
            return (new Date($value))->format('Y-m-d');
        }
        if ($type == 'datetime') {
            return (new Date($value))->format('Y-m-d H:i');
        }
        if ($type == 'select') {
            return $value == ''?null:$value;
        }
        if ($type == 'file') {
            return $value = $this->$key;
        }
        if ($type == 'checkbox'){
            return $value == null?0:1;
        }
        return $value;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function fillAll(Request $request)
    {
        foreach ($request->except(['_token','_method']) as $item => $value){
            $this->$item = $this->fillField($item,$value);
        }
        $this->save();
        if ($request->hasFile('fichero')) {
            $this->fillFile($request->file('fichero'));
        }
        $primaryKey =  $this->primaryKey ?? 'id';
        return $this->$primaryKey;
    }


    private function translate($key){
        return !strpos(trans('validation.attributes.' . $key), 'alidation.') ? trans('validation.attributes.' . $key) : ucwords($key);
    }

    public function fillDefautOptions()
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
            /*
            if ($property == 'inline'){
                $parametres = array_merge($parametres, ['inline' => 'inline']);
            }
            */

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


    /**
     * @param $field
     * @return bool
     */
    public function has($field)
    {
        if (isset($this->$field) || is_null($this->$field)) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function getLinkAttribute()
    {
        if (isset($this->fichero) && file_exists(storage_path('app/' . $this->fichero))) {
            return true;
        }
        return false;
    }

   
}