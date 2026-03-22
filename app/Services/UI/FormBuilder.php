<?php

namespace Intranet\Services\UI;

use Illuminate\View\View;

/**
 * Construeix la configuració de camps per als formularis dinàmics del projecte.
 *
 * Manté compatibilitat amb el sistema legacy de templates, però prioritza
 * tipus HTML natius per a dates i hores quan el contracte del camp ja és clar.
 */
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
            $declaredType = $properties['type'] ?? 'text';
            $inputType[$key]['type'] = $this->aspect($parametres, $declaredType);

            $parametres['id'] = $key . '_id';
            $parametres['ph'] = $this->translate($key);
            $parametres['class'] = 'col-md-7 col-xs-12 ' . $this->resolveCssInputClass($declaredType, $inputType[$key]['type']);

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
        return !strpos(__('validation.attributes.' . $key), 'alidation.')
            ? __('validation.attributes.' . $key)
            : ucwords($key);
    }

    /**
     * Resol el tipus final de control i el template visual a emprar.
     *
     * @param array<int|string, mixed> $parametres
     * @param string $originalType
     * @return string
     */
    private function aspect(&$parametres, $originalType)
    {
        switch ($originalType) {
            case 'multiple' :
                $finalType='select';
                $parametres[] = 'multiple';
                break;
            case 'name':
            case 'card':
                $parametres['template'] = 'themes/bootstrap/fields/'.$originalType;
                $finalType='text';
                break;
            case 'time':
            case 'date':
                $parametres['template'] = 'themes/bootstrap/fields/'.$originalType;
                $finalType = $originalType;
                break;
            case 'datetime':
                $parametres['template'] = 'themes/bootstrap/fields/datetime';
                $finalType='datetimeLocal';
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
            $declaredType = $inputTpe['type'] ?? 'text';
            $inputType[$property]['type'] = $this->aspect($parametres, $declaredType);
            $label = existsTranslate('models.'.$model.'.'.$property) ? __('models.'.$model.'.'.$property):null;
            $ph = !strpos(__('validation.attributes.' . $property), 'alidation.')
                ? __('validation.attributes.' . $property)
                : ucwords($property);

            $parametres['id'] = $property . '_id';
            $parametres['ph'] = $ph;
            if ($label) {
                $parametres['label'] = $label;
            }
            $parametres['class'] = 'col-md-7 col-xs-12 ' . $this->resolveCssInputClass($declaredType, $inputType[$property]['type']);

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

    /**
     * Conserva la classe funcional del tipus declarat per a JS/CSS legacy residual.
     *
     * Encara que alguns camps ja es renderitzen com a inputs natius,
     * la classe declarativa (`date/time/datetime`) continua sent útil
     * en plantilles o scripts que inspeccionen el camp.
     *
     * @param string $declaredType
     * @param string $renderType
     * @return string
     */
    private function resolveCssInputClass(string $declaredType, string $renderType): string
    {
        return in_array($declaredType, ['date', 'time', 'datetime'], true)
            ? $declaredType
            : $renderType;
    }
}
