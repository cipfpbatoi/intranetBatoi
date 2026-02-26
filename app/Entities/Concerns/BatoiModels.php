<?php

namespace Intranet\Entities\Concerns;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Intranet\Services\UI\AppAlert as Alert;

/**
 * Utilitats comunes de model per a formularis, validació i càrrega de fitxers.
 *
 * Aquest trait centralitza:
 * - regles (`rules`) i tipus d'input (`inputTypes`),
 * - emplenat de camps des de Request (`fillAll`),
 * - gestió de fitxers associats (`fillFile`).
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
        return isset($this->rules[$campo]) && strpos($this->rules[$campo], 'required') !== false;
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
        if (in_array($field, $this->fillable, true)) {
            return;
        }

        if (empty($this->fillable)) {
            $this->fillable[] = $field;
            return;
        }

        if ($first) {
            array_unshift($this->fillable, $field);
        } else {
            $ultimo = array_pop($this->fillable);
            $this->fillable[] = $field;
            if ($ultimo !== null) {
                $this->fillable[] = $ultimo;
            }
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

    /**
     * Retorna la definició completa de tipus d'input del model.
     *
     * @return array
     */
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
        if (!isset($type['type']) || !is_string($type['type'])) {
            return false;
        }

        $inputType = strtolower($type['type']);
        return strpos($inputType, 'ate') !== false
            || strpos($inputType, 'ime') !== false
            || strpos($inputType, 'ag') !== false;
    }

    /*
    public function fillAll(Request $request)
    {
        $fillable = $this->notFillable
            ? array_diff($this->fillable, $this->notFillable)
            : $this->fillable;


        foreach ($fillable as $key) {
            if ($request->has($key) && !$request->hasFile($key)) {
                $this->$key = $this->fillField($key, $request->$key);
            } elseif (!$request->hasFile($key)) {
                $this->$key = 0;
            }
         }

        $this->save();

        $primaryKey = $this->primaryKey ?? 'id';
        return $this->$primaryKey;
    }
    */
    /**
     * Emplena i persisteix els camps `fillable` des d'un request.
     *
     * Regles especials:
     * - camps absents no es sobreescriuen (excepte `checkbox`),
     * - `checkbox` absent es normalitza a `0`,
     * - si arriba `fichero`, es valida i guarda.
     *
     * @param Request $request
     * @return mixed Valor de la clau primària del registre.
     */
    public function fillAll(Request $request)
    {
        $fillable = $this->notFillable?array_diff($this->fillable, $this->notFillable):$this->fillable;

        foreach ($fillable as $key) {
            $type = $this->inputTypes[$key]['type'] ?? null;

            if (!$request->exists($key) && $type !== 'checkbox') {
                continue;
            }

            $value = $request->input($key);
            $this->$key = $this->fillField($key, $value);
        }
        $this->save();
        $this->refresh();


        if ($request->hasFile('fichero')) {
            $this->fichero = $this->fillFile($request->file('fichero'));
            $this->save();
        }

        $primaryKey =  $this->primaryKey ?? 'id';
        return $this->$primaryKey;
    }

    /**
     * Normalitza i transforma el valor d'un camp segons el seu tipus d'input.
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    private function fillField($key, $value)
    {
        $type = $this->inputTypes[$key]['type'] ?? null;

        return match ($type) {
            'date' => blank($value) ? null : Carbon::parse($value)->format('Y-m-d'),
            'datetime' => blank($value) ? null : Carbon::parse($value)->format('Y-m-d H:i'),
            'select' => $value == '' ? null : $value,
            'file' => request()->hasFile($key) ? $this->fillFile(request()->file($key)) : $this->$key,
            'checkbox' => $value==null ? 0 : 1,
            default => $value,
        };
    }
    /**
     * Valida i guarda un fitxer annex retornant la ruta final.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return string|null
     */
    public function fillFile($file)
    {

        if (!$file->isValid()) {
            Alert::danger(trans('messages.generic.invalidFormat'));
            return;
        }

        // Validar extensió
        $allowedExtensions = ['pdf', 'docx', 'xlsx', 'jpg', 'png', 'webp', 'heic', 'heif', 'zip'];
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

    /**
     * Construeix el directori de destí del fitxer segons curs i classe.
     *
     * @param string $clase
     * @return string
     */
    private function getDirectory($clase)
    {
        return 'gestor/' . curso() . '/' . $clase;
    }

    /**
     * Construeix el nom final del fitxer pujat.
     *
     * @param string $extension
     * @param string $clase
     * @return string
     */
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
    /**
     * Retorna el model serialitzat per a pantalles de confirmació.
     *
     * @return array
     */
    public function showConfirm()
    {
        return $this->toArray();
    }
}
