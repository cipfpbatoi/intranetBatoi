<?php


namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use App;

class TipoIncidencia extends Model
{

    protected $table = 'tipoincidencias';
    public $timestamps = false;

    public function getLiteralAttribute()
    {
        return App::getLocale(session('lang')) == 'es' ? $this->nombre : $this->nom;
    }

}
