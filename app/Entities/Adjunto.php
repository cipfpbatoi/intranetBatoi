<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Adjunto extends Model
{
    use HasFactory;

    const CARPETA = "/app/public/adjuntos/";

    public function Profesor()
    {
        return $this->belongsTo(Profesor::class, 'owner', 'dni');
    }

    public function scopeFindByName($query, $path,$name)
    {
        return $query->where('route',$path)->where('name',$name);
    }

    public function scopeGetByPath($query, $path)
    {
        return $query->where('route',$path);
    }

    public function getPathAttribute(){
        return storage_path().self::CARPETA.$this->route.'/'.$this->name;
    }

    public function getDirectoryAttribute(){
        return storage_path().self::CARPETA.$this->route;
    }

    public function getModeloAttribute(){
        return explode('/',$this->path)[0];
    }

    public function getModelo_idAttribute(){
        return explode('/',$this->path)[1];
    }
}
