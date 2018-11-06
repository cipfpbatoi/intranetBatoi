<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use Intranet\Entities\Dual;
use Intranet\Entities\Profesor;
use Illuminate\Support\Facades\Session;
use Intranet\Botones\BotonBasico;
use Intranet\Botones\BotonImg;
use Intranet\Botones\Panel;
use Intranet\Entities\Documento;

class DualController extends Controller
{
    use traitCRUD;

    protected $perfil = 'profesor';
    protected $model = 'Dual';
    
    

    protected $modal = false;

    
    
    
    public function store(Request $request)
    {
        $idFct = DB::transaction(function() use ($request){
            $idAlumno = $request['idAlumno'];
            $hasta = $request['hasta'];
            $elemento = Dual::where('idColaboracion',$request->idColaboracion)
                    ->where('asociacion',3)
                    ->first();
            
            if (!$elemento){ 
                $elemento = new Dual();
                $this->validateAll($request, $elemento);
                $id = $elemento->fillAll($request);
            } 
            $elemento->Alumnos()->attach($idAlumno,['desde'=> FechaInglesa($request->desde),'hasta'=>FechaInglesa($hasta),'horas'=>$request->horas]);
            
            return $elemento->id;
        });
        
        return $this->redirect();
    }

    
    
    public function anexevii($id)
    {
        $fct = Dual::findOrFail($id);
        $secretario = Profesor::find(config('contacto.secretario'));
        $director = Profesor::find(config('contacto.director'));
        $dades = ['date' => FechaString(FechaPosterior($fct->hasta)),
            'consideracion' => $secretario->sexo === 'H' ? 'En' : 'Na',
            'secretario' => $secretario->FullName,
            'centro' => config('contacto.nombre'),
            'codigo' => config('contacto.codi'),
            'poblacion' => config('contacto.poblacion'),
            'provincia' => config('contacto.provincia'),
            'director' => $director->FullName
        ];
        
        $pdf = $this->hazPdf('dual.anexe_vii', $fct,$dades,'landscape','a4',10);
        return $pdf->stream();
    }
        
    
    
   

}
