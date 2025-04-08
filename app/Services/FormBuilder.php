<?php

namespace Intranet\Services;

use Illuminate\View\View;

class FormBuilder
{

    private $elemento;
    private $default;
    private $fillable;


    public function __construct($elemento, $formFields = null)
    {
        $this->elemento = $elemento;

        if ($formFields != null) {
            $this->default = $this->fillDefaultOptionsToForm($formFields);
            $this->fillable = array_keys($formFields);
        } else {
            $this->fillable = $elemento->getFillable();
            $this->default = $this->fillDefaultOptionsFromModel();
        }
    }

    /**
     * @return mixed
     */
    public function getElemento()
    {
        return $this->elemento;
    }

    /**
     * @return array
     */
    public function getDefault(): array
    {
        return $this->default;
    }

        public function render(string $method = 'POST',? string $afterView = null): View
        {
           return view('themes.bootstrap.form', [
                'elemento' => $this->elemento,
                'method' => $method,
                'default' => $this->default,
                'fillable' => $this->fillable,
                'afterView' => $afterView,
                'formulario' => $this,
            ]);
        }

    public function modal()
    {
        $elemento = $this->elemento;
        $default = $this->default;
        $fillable = $this->fillable;
        return view('themes.bootstrap.formodal', compact('elemento', 'default', 'fillable'));
    }

    private function fillDefaultOptionsToForm($formFields)
    {
        $inputType = [];
        foreach ($formFields as $key => $properties) {
            $parametres = [];
            $inputType[$key]['type'] = $this->aspect($parametres, $properties['type']??'text');

            $parametres['id'] = $key . '_id';
            $parametres['ph'] = $this->translate($key);
            $parametres['class'] = 'col-md-7 col-xs-12 ' . $inputType[$key]['type'];

            $inputType[$key]['default'] = $properties['default'] ?? null;


            if (isset($properties['disabled'])) {
                $parametres = array_merge($parametres, ['disabled' => 'disabled']);
            }
            if (isset($properties['disableAll'])) {
                $parametres = array_merge($parametres, ['disabled' => 'on']);
            }
            if ($this->elemento->isRequired($key)) {
                $parametres = array_merge($parametres, ['required']);
            }
            if (isset($properties['inline'])) {
                $parametres = array_merge($parametres, ['inline' => 'inline']);
            }

            $inputType[$key]['params'] = $parametres;
        }
        return($inputType);

    }

    private function translate($key)
    {
        return !strpos(trans('validation.attributes.' . $key), 'alidation.')
            ? trans('validation.attributes.' . $key)
            : ucwords($key);
    }

    private function aspect(&$parametres, $originalType)
    {
        switch ($originalType) {
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
     * @return array
     */
    public function fillDefaultOptionsFromModel()
    {
        $inputType = [];
        $model = getClase($this->elemento);
        foreach ($this->fillable as $property) {
            $parametres = [];
            $inputTpe = $this->elemento->getInputType($property);
            $inputType[$property]['type'] = $this->aspect($parametres, $inputTpe['type'] ?? 'text');
            $label = existsTranslate('models.'.$model.'.'.$property) ? trans('models.'.$model.'.'.$property):null;
            $ph = !strpos(trans('validation.attributes.' . $property), 'alidation.')
                ? trans('validation.attributes.' . $property)
                : ucwords($property);

            $parametres['id'] = $property . '_id';
            $parametres['ph'] = $ph;
            if ($label) {
                $parametres['label'] = $label;
            }
            $parametres['class'] = 'col-md-7 col-xs-12 ' . $inputType[$property]['type'];

            $inputType[$property]['default'] = $inputTpe['default'] ?? null;

            if (isset($inputTpe['disabled'])) {
                $parametres = array_merge($parametres, ['disabled' => 'disabled']);
            }
            if (isset($inputTpe['disableAll'])) {
                $parametres = array_merge($parametres, ['disabled' => 'on']);
            }
            if ($this->elemento->isRequired($property)) {
                $parametres = array_merge($parametres, ['required']);
            }
            if (isset($inputTpe['inline'])) {
                $parametres = array_merge($parametres, ['inline' => 'inline']);
            }

            $inputType[$property]['params'] = $parametres;
        }
        return($inputType);
    }
}

