<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Styde\Html\Facades\Alert;

class Adjunto extends Model
{
    use HasFactory;

    const CARPETA = "/app/public/adjuntos/";

    protected $fillable = [
        'name',
        'owner',
        'referencesTo',
        'title',
        'size',
        'extension',
        'route'
    ];
    public function Profesor()
    {
        return $this->belongsTo(Profesor::class, 'owner', 'dni');
    }

    public function scopeFindByName($query, $path, $name)
    {
        return $query->where('route', $path)->where('name', $name);
    }

    public function scopeGetByPath($query, $path)
    {
        return $query->where('route', $path);
    }

    public function getPathAttribute()
    {
        return storage_path().self::CARPETA.$this->route.'/'.$this->title.'.'.$this->extension;
    }

    public function getFileAttribute()
    {
        $this->route.'/'.$this->title.'.'.$this->extension;
    }

    public function getDirectoryAttribute()
    {
        return storage_path().self::CARPETA.$this->route;
    }

    public function getModeloAttribute()
    {
        return explode('/', $this->path)[0];
    }

    public function getModelo_idAttribute()
    {
        return explode('/', $this->path)[1];
    }

    public static function moveAndPreserveDualFiles()
    {
        $alumnesDual = AlumnoFct::esDual()->get();
        $dualRoutes = array();

        foreach ($alumnesDual as $alumne) {
            if ($alumne->Alumno->Grupos->first()->curso == 1){
                $route = 'alumnofctaval/' . $alumne->id;
                $newRoute = 'dual/' . $alumne->Alumno->dni;
                $adjuntos = Adjunto::where('route', $route)->get();

                foreach ($adjuntos as $adjunto) {
                    $file = $adjunto->title.'.'.$adjunto->extension;

                    if (is_null($adjunto->referencesTo)) {
                        $oldPath = 'adjuntos/'.$route.'/'.$file;
                        $newPath = 'adjuntos/'.$newRoute.'/'.$file;

                        // Verificar existència del fitxer original
                        if (!Storage::disk('public')->exists($oldPath)) {
                            Alert::warning("El fitxer no existeix: $oldPath");
                            continue;
                        }

                        // Crear el nou directori si no existeix
                        if (!Storage::disk('public')->exists(dirname($newPath))) {
                            Storage::disk('public')->makeDirectory(dirname($newPath));
                        }

                        // Moure el fitxer
                        if (Storage::disk('public')->move($oldPath, $newPath)) {
                            $adjunto->route = $newRoute;
                            $dualRoutes[] = $newRoute;
                            $adjunto->save();
                            Alert::info("De $oldPath a $newPath");
                        } else {
                            Alert::warning("No he pogut moure el fitxer de $oldPath a $newPath");
                        }
                    }
                }
            }
        }

        // Esborra els fitxers no modificats
        self::deleteNonDualFiles($dualRoutes);
    }

    private static function deleteNonDualFiles($dualRoutes)
    {
        $allAdjuntos = Adjunto::all();
        $directoriesToCheck = [];

        foreach ($allAdjuntos as $adjunto) {
            if (!in_array($adjunto->route, $dualRoutes)) {
                $oldPath = 'adjuntos/' . $adjunto->route . '/' . $adjunto->title . '.' . $adjunto->extension;
                $directory = dirname($oldPath);
                $directoriesToCheck[] = $directory;

                if (Storage::disk('public')->delete($oldPath)) {
                    $adjunto->delete();
                    Alert::info("Esborrat $oldPath");
                } else {
                    Alert::warning("No esborrat $oldPath");
                }
            }
        }

        // Eliminar directoris buits
        $directoriesToCheck = array_unique($directoriesToCheck);
        foreach ($directoriesToCheck as $directory) {
            if (Storage::disk('public')->exists($directory) && empty(Storage::disk('public')->files($directory))) {
                Storage::disk('public')->deleteDirectory($directory);
                Alert::info("Directori esborrat: $directory");
            }
        }
    }

}
