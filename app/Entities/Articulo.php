<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Styde\Html\Facades\Alert;



class Articulo extends Model
{

    use BatoiModels;

    protected   $table = 'articulos';
    public      $timestamps = false;
    protected   $fillable = [ 'descripcion', 'fichero'];
    protected   $inputTypes = [
        'fichero' => ['type' => 'file'],
    ];

    public function Lote()
    {
        return $this->hasManyThrough(Lote::class, ArticuloLote::class, 'articulo_id', 'lote_id','id','registre');
    }

    public function getMiniaturaAttribute()
    {
        // Ruta on busquem fitxers que comencen per l'id del model i tinga qualsevol extensió
        $pattern = storage_path('app/public/Articulos/' . $this->id . '.*');

        // Llista de fitxers que coincideixen
        $files = glob($pattern);

        if (!empty($files)) {
            // Ens quedem amb el primer fitxer trobat
            $fileFullPath = $files[0];
            // Nom del fitxer (sense directori)
            $fileName = basename($fileFullPath);

            // Retornem el codi HTML de la miniatura
            return "<img src='" . asset('storage/Articulos/' . $fileName) . "' height='40' width='60' />";
        }

        // Si no hi ha cap fitxer
        return "Sense imatge";
    }

    public function fillFile($file)
    {
        // 1) Primer validem
        if (!$file->isValid()) {
            Alert::danger(trans('messages.generic.invalidFormat'));
            return;
        }


        // 3) Definim el nom del fitxer. Suposant que 'id' sempre existeix ja.
        $filename = $this->id . '.' . $file->getClientOriginalExtension();

        // 4) Emmagatzemem el fitxer en 'storage/app/public/Articulos'
        $file->storeAs('Articulos', $filename, 'public');

    }

    public function setDescripcionAttribute($value){
        $this->attributes['descripcion'] =ucwords(strtolower($value));
    }

}
