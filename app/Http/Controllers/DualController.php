<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Intranet\Entities\Dual;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Fct;
use mikehaertl\pdftk\Pdf;
use Jenssegers\Date\Date;

/**
 * Class DualController
 * @package Intranet\Http\Controllers
 */
class DualController extends IntranetController
{

    use traitImprimir;

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Dual';
    /**
     * @var bool
     */
    protected $modal = false;

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $alumno = AlumnoFct::findOrFail($id);
        $elemento = $alumno->Dual;
        $elemento->setInputType('idAlumno', ['type' => 'hidden', 'disableAll' => 'disableAll']);
        $elemento->setInputType('idColaboracion', ['disabled' => 'disabled']);
        $elemento->desde = $alumno->desde;
        $elemento->hasta = $alumno->hasta;
        $elemento->horas = $alumno->horas;
        $default = $elemento->fillDefautOptions();
        $modelo = $this->model;

        return view($this->chooseView('edit'), compact('elemento', 'default', 'modelo'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $idFct = DB::transaction(function() use ($request, $id) {
                    $alumno = AlumnoFct::findOrFail($id);
                    $elemento = $alumno->Dual;

                    $alumno->desde = FechaInglesa($request['desde']);
                    $alumno->hasta = FechaInglesa($request['hasta']);
                    $alumno->horas = $request['horas'];
                    $alumno->save();
                    $elemento->idInstructor = $request['idInstructor'];
                    $elemento->save();

                    return $elemento->id;
                });

        return $this->redirect();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $idFct = DB::transaction(function() use ($request) {
                    $idAlumno = $request['idAlumno'];
                    $hasta = $request['hasta'];
                    $elemento = Dual::where('idColaboracion', $request->idColaboracion)
                            ->where('asociacion', 3)
                            ->first();

                    if (!$elemento) {
                        $elemento = new Dual();
                        $this->validateAll($request, $elemento);
                        $id = $elemento->fillAll($request);
                    }
                    $elemento->Alumnos()->attach($idAlumno, ['desde' => FechaInglesa($request->desde), 'hasta' => FechaInglesa($hasta), 'horas' => $request->horas]);

                    return $elemento->id;
                });

        return $this->redirect();
    }


    public function printAnexeVI(){
        $pdf = new Pdf('fdf/ANEXO_VI.pdf');
        $pdf->fillform($this->makeArrayPdfAnexoVI())
            ->send('dualVI'.AuthUser()->dni.'.pdf');
        return back();
    }



    /**
     * @param $array
     * @return mixed
     */
    private function makeArrayPdfAnexoVI()
    {
        $empresas = Fct::misFcts(null, true)->esDual()->count();
        $duales = AlumnoFct::misDual()->orderBy('idAlumno')->get();
        $primero = $duales->first();
        $grupo = $primero->Alumno->Grupo->first();
        $ciclo = $primero->Fct->Colaboracion->Ciclo;
        $alumnos = $grupo->Alumnos->where('sexo', 'H')->count();
        $alumnas = $grupo->Alumnos->where('sexo', 'M')->count();
        $dualH = 0;
        $fctH = 0;
        $OKH = 0;
        $NOH = 0;
        $exeH = 0;
        $dualM = 0;
        $fctM = 0;
        $OKM = 0;
        $NOM = 0;
        $exeM = 0;
        $totalHoras = 0;
        foreach ($duales as $index => $dual) {
            if ($dual->Alumno->sexo == 'H') $dualH++; else $dualM++;
            $array[66 + $index * 6] = $index + 1;
            $array[67 + $index * 6] = $dual->Alumno->FullName;
            $array[68 + $index * 6] = $dual->Fct->Colaboracion->Centro->Empresa->nombre;
            $array[69 + $index * 6] = $dual->horas;
            $totalHoras += $dual->horas;
        }
        $fcts = AlumnoFct::misFcts($grupo->tutor)
            ->esAval()
            ->whereIn('idAlumno', hazArray($duales, 'idAlumno'))
            ->orderBy('idAlumno')
            ->get();
        $totalHorasFct = 0;
        $europa = 0;
        foreach ($fcts as $index => $fct) {
            if ($fct->Alumno->sexo == 'H') {
                if ($fct->FCT->asociacion == 2) $exeH++;
                else {
                    $fctH++;
                    if ($dual->calificacion) $OKH++; else $NOH++;
                }
            } else {
                if ($fct->FCT->asociacion == 2) $exeM++;
                else {
                    $fctM++;
                    if ($dual->calificacion) $OKM++; else $NOM++;
                }
            }
            $array[70 + $index * 6] = $fct->Fct->Colaboracion->Centro->Empresa->nombre;
            $array[71 + $index * 6] = $fct->horas;
            $totalHorasFct += $fct->horas;
            if ($fct->Fct->Colaboracion->Centro->Empresa->europa) $europa++;
        }
        $iguales = 0;
        $diferentes = 0;
        $europa = 0;
        foreach ($duales as $index => $dual) {
            if ($array[68 + $index * 6] == $array[70 + $index * 6]) $iguales++; else $diferentes++;
        }
        $array[0] = 'Anexo_VI';
        $array[1] = config('contacto.nombre');
        $array[2] = config('contacto.codi');
        $array[3] = 'Sí';
        $array[5] = 'Sí';
        $array[7] = AuthUser()->Departamento->literal;
        $array[8] = $ciclo->literal;
        if ($ciclo->tipo == 1) $array[9] = 'Sí'; else $array[10] = 'Sí';
        $array[12] = 'Sí';
        $array[14] = AuthUser()->fullName;
        $array[15] = AuthUser()->Departamento->literal;
        $array[16] = $alumnos;
        $array[17] = $alumnas;
        $array[18] = $dualH;
        $array[19] = $dualM;
        $array[20] = $empresas;
        $array[23] = $dualH;
        $array[24] = $dualM;
        $array[39] = $dualH;
        $array[40] = $dualM;
        $array[41] = $dualH;
        $array[42] = $dualM;
        $array[43] = $fctH;
        $array[44] = $fctM;
        $array[43] = $dualH - $fctH;
        $array[44] = $dualM - $fctM;
        $array[47] = $dualH + $dualM;
        $array[48] = $dualH + $dualM;
        $array[49] = $fctH + $fctM;
        $array[50] = $dualH + $dualM - $fctH - $fctM;
        $array[51] = $fctH;
        $array[52] = $fctM;
        $array[53] = $OKH;
        $array[54] = $OKM;
        $array[55] = $NOH;
        $array[56] = $NOM;
        $array[57] = $exeH;
        $array[58] = $exeM;
        $array[61] = $fctH + $fctM;
        $array[62] = $OKH + $OKM;
        $array[63] = $NOH + $NOM;
        $array[64] = $exeH + $exeM;
        $array[218] = $totalHoras;
        $array[219] = $totalHorasFct;
        $array[226] = $iguales;
        $array[227] = $diferentes;
        $array[230] = $europa;
        $array[236] = config('contacto.poblacion');
        $fc1 = new Date();
        Date::setlocale('ca');
        $array[237] = $fc1->format('d');
        $array[238] = $fc1->format('F');
        $array[239] = $fc1->format('Y');
        $array[240] = AuthUser()->fullName;
        return $array;
    }


    public function printAnexeXIV(){
        $pdf = new Pdf('fdf/ANEXO_XIV.pdf');
        //dd($pdf->getDataFields());
        $pdf->fillform($this->makeArrayPdfAnexoXIV())
            ->send('dualXIV'.AuthUser()->dni.'.pdf');
        return back();
    }

    /**
     * @param $array
     * @return mixed
     */
    private function makeArrayPdfAnexoXIV()
    {
        $empresas = Fct::misFcts(null, true)->esDual()->count();
        $duales = AlumnoFct::misDual()->orderBy('idAlumno')->get();
        $primero = $duales->first();
        $grupo = $primero->Alumno->Grupo->first();
        $ciclo = $primero->Fct->Colaboracion->Ciclo;
        $array['form1[0].Pagina1[0].Interior[0].seccion\.a[0].A_TEXT1[0]'] = config('contacto.nombre');
        $array['form1[0].Pagina1[0].Interior[0].seccion\.a[0].A_TEXT2[0]'] = config('contacto.poblacion');
        $array['form1[0].Pagina1[0].Interior[0].seccion\.a[0].A_TEXT3[0]'] = config('contacto.codi');
        $array['form1[0].Pagina1[0].Interior[0].seccion\.a[0].A_TEXT4[0]'] = AuthUser()->Departamento->literal;
        $array['form1[0].Pagina1[0].Interior[0].seccion\.a[0].A_TEXT5[0]'] = $ciclo->literal;

        $array['form1[0].Pagina1[1].Interior[0].seccion\.a[0].A_TEXT1[0]'] = config('contacto.nombre');
        $array['form1[0].Pagina1[1].Interior[0].seccion\.a[0].A_TEXT2[0]'] = config('contacto.poblacion');
        $array['form1[0].Pagina1[1].Interior[0].seccion\.a[0].A_TEXT3[0]'] = config('contacto.codi');
        $array['form1[0].Pagina1[1].Interior[0].seccion\.a[0].A_TEXT4[0]'] = AuthUser()->Departamento->literal;
        $array['form1[0].Pagina1[1].Interior[0].seccion\.a[0].A_TEXT5[0]'] = $ciclo->literal;


        $array['form1[0].Pagina1[0].Interior[0].seccion\.a[0].A_TEXT6[0]'] = AuthUser()->fullName;
        $array['form1[0].Pagina1[0].Interior[0].seccion\.a[0].A_TEXT7[0]'] = AuthUser()->Departamento->literal;
        $array['form1[0].Pagina1[1].Interior[0].seccion\.a[0].A_TEXT6[0]'] = AuthUser()->fullName;
        $array['form1[0].Pagina1[1].Interior[0].seccion\.a[0].A_TEXT7[0]'] = AuthUser()->Departamento->literal;


        foreach ($duales as $index => $dual) {
            $indice = $index+1;
            $array["form1[0].Pagina1[0].Interior[0].seccion\.b[0].B\.B_DNI[0].B_DNI$indice"."[0]"] = $dual->Alumno->dni;
            $array["form1[0].Pagina1[0].Interior[0].seccion\.b[0].B\.B_ALUMNE[0].B_ALUMNO$indice"."[0]"] = $dual->Alumno->FullName;
            $array["form1[0].Pagina1[0].Interior[0].seccion\.b[0].B\.B_HORAS[0].B_HORAS$indice"."[0]"] = $dual->horas;
            $array["form1[0].Pagina1[0].Interior[0].seccion\.b[0].B\.B_LLOC[0].B_LLOC$indice"."[0]"] = "Programador Web";

            $array["form1[0].Pagina1[1].Interior[0].seccion\.b[0].B\.B_DNI[0].B_DNI$indice"."[0]"] = $dual->Alumno->dni;
            $array["form1[0].Pagina1[1].Interior[0].seccion\.b[0].B\.B_ALUMNE[0].B_ALUMNO$indice"."[0]"] = $dual->Alumno->FullName;
            $array["form1[0].Pagina1[1].Interior[0].seccion\.b[0].B\.B_HORAS[0].B_HORAS$indice"."[0]"] = $dual->horas;
            $array["form1[0].Pagina1[1].Interior[0].seccion\.b[0].B\.B_LLOC[0].B_LLOC$indice"."[0]"] = "Programador Web";
        }

        $fc1 = new Date();
        Date::setlocale('ca');
        $array["form1[0].Pagina1[0].Interior[0].seccion\.c[0].C_TEXTFIRMA1[0]"] = AuthUser()->fullName;
        $array["form1[0].Pagina1[0].Interior[0].seccion\.c[0].C_MES[0]"] = $fc1->format('F');
        $array["form1[0].Pagina1[0].Interior[0].seccion\.c[0].C_LLOC[0]"] = config('contacto.poblacion');
        $array["form1[0].Pagina1[0].Interior[0].seccion\.c[0].C_DIA[0]"] = $fc1->format('d');
        $array["form1[0].Pagina1[0].Interior[0].seccion\.c[0].C_ANY[0]"] = $fc1->format('Y');

        $array["form1[0].Pagina1[1].Interior[0].seccion\.c[0].C_TEXTFIRMA1[0]"] = AuthUser()->fullName;
        $array["form1[0].Pagina1[1].Interior[0].seccion\.c[0].C_MES[0]"] = $fc1->format('F');
        $array["form1[0].Pagina1[1].Interior[0].seccion\.c[0].C_LLOC[0]"] = config('contacto.poblacion');
        $array["form1[0].Pagina1[1].Interior[0].seccion\.c[0].C_DIA[0]"] = $fc1->format('d');
        $array["form1[0].Pagina1[1].Interior[0].seccion\.c[0].C_ANY[0]"] = $fc1->format('Y');
        //dd($array);
        return $array;
    }

}
