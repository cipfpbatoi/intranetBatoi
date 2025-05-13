<?php

namespace Intranet\Entities;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Styde\Html\Facades\Alert;

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
        return isset($this->rules[$campo]) && strpos($this->rules[$campo], 'equired');
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
    public function deleteInputType($id)
    {
        unset($this->inputTypes[$id]);
        foreach ($this->fillable as $key => $item) {
            if ($item == $id) {
                unset($this->fillable[$key]);
            }
        }
    }

    /**
     * @param $field
     * @param bool $first
     */
    public function addFillable($field, $first=false)
    {
        if ($first) {
            array_unshift($this->fillable, $field);
        } else {
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


    /**
     * @param $campo
     * @return array
     */
    public function getInputType($campo)
    {
        return isset($this->inputTypes[$campo]) ? $this->inputTypes[$campo] : ['type' => 'text'];
    }

    public function getInputTypes()
    {
        return $this->inputTypes;
    }

    /**
     * @return bool
     */
    public function existsDatepicker()
    {
        if (isset($this->inputTypes)) {
            foreach ($this->inputTypes as $type) {
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
    public function isTypeDate($type)
    {
        return isset($type['type']) && (strpos($type['type'], 'ate') || strpos($type['type'],
                    'ime') || strpos($type['type'], 'ag'));
    }


    public function fillAll(Request $request)
    {
        $fillable = $this->notFillable
            ? array_diff($this->fillable, $this->notFillable)
            : $this->fillable;


        foreach ($fillable as $key) {
            if ($request->has($key) || $request->hasFile($key)) {
                $this->$key = $this->fillField($key, $request->$key);
            } else {
                $this->$key = 0;
            }
         }

        $this->save();

        $primaryKey = $this->primaryKey ?? 'id';
        return $this->$primaryKey;
    }

    private function fillField($key, $value)
    {
        $type = $this->inputTypes[$key]['type'] ?? null;

        return match ($type) {
            'date' => Carbon::parse($value)->format('Y-m-d'),
            'datetime' => Carbon::parse($value)->format('Y-m-d H:i'),
            'select' => $value == '' ? null : $value,
            'file' => request()->hasFile($key) ? $this->fillFile(request()->file($key)) : $this->$key,
            'checkbox' => $value==null ? 0 : 1,
            default => $value,
        };
    }

    public function fillFile($file)
    {

        if (!$file->isValid()) {
            Alert::danger(trans('messages.generic.invalidFormat'));
            return;
        }


        // Validar extensió
        $allowedExtensions = ['pdf', 'docx', 'xlsx', 'jpg', 'png'];
        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, $allowedExtensions)) {
            Alert::danger(trans('messages.generic.invalidFileType'));
            return;
        }


        // Obtenir el nom de la classe correctament
        $clase = getClase($this) === 'Documento' ? $this->tipoDocumento : getClase($this);

        // Guardar fitxer
        return $file->storeAs(
            $this->getDirectory($clase),
            $this->getFileName($extension, $clase)
        );

    }

    private function getDirectory($clase)
    {
        return 'gestor/' . curso() . '/' . $clase;
    }

    private function getFileName($extension, $clase)
    {
        $nombre = $this->id ? $this->id . '_' : '';

        if (!empty($this->fileField) && isset($this->{$this->fileField})) {
            $nombre .= $this->{$this->fileField} . '_';
        }

        return $nombre . $clase . '.' . $extension;
    }


    /**
     * @param $field
     * @return bool
     */
    public function has($field)
    {
        return isset($this->$field) || is_null($this->$field);
    }

    /**
     * @return bool
     */
    public function getLinkAttribute()
    {
        return isset($this->fichero) && file_exists(storage_path('app/'.$this->fichero));
    }

    public function saveContact($contacto, $email)
    {
        $this->contacto = $contacto;
        $this->email = $email;
        return $this->save();
    }

    public function showConfirm()
    {
        return $this->toArray();
    }
}
