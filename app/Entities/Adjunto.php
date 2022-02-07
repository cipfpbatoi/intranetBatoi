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

    public function scopeFindByName($query, $model,$model_id,$name)
    {
        return $query->where('model',$model)->where('model_id',$model_id)
            ->where('name',$name);
    }

    public function scopeFindByModel($query, $model,$model_id)
    {
        return $query->where('model',$model)->where('model_id',$model_id);
    }

    public function getPathAttribute(){
        return storage_path().self::CARPETA.$this->model.'/'.$this->model_id.'/'.$this->name;
    }

    public function getDirectoryAttribute(){
        return storage_path().self::CARPETA.$this->model.'/'.$this->model_id;
    }
}
