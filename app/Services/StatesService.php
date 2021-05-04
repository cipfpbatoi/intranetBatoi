<?php


namespace Intranet\Services;


use Jenssegers\Date\Date;
use Styde\Html\Facades\Alert;

class StatesService
{
    private $model;
    private $inicialState;
    private $finalState;
    private

    public function __construct()
}


public function imprimir($modelo = '', $inicial = null, $final = null, $orientacion='portrait', $link=true)
{
    $modelo = $modelo ? $modelo : strtolower($this->model) . 's';
    $final = $final ? $final : '_print';
    $todos = $this->class::listos($inicial);
    if ($todos->Count()) {
        $pdf = $this->hazPdf("pdf.$modelo", $todos,null,$orientacion);
        $nom = $this->model . new Date() . '.pdf';
        $nomComplet = 'gestor/' . Curso() . '/informes/' . $nom;
        $tags = config("modelos.$this->model.documento");
        $gestor = new Gestor();
        $doc = $gestor->save(['fichero' => $nomComplet, 'tags' => $tags ]);
        $this->makeAll($todos, $final);
        if ($link) {
            $this->makeLink($todos,$doc);
        }
        return $pdf->save(storage_path('/app/' . $nomComplet))->download($nom);
    }
    Alert::info(trans('messages.generic.empty'));
    return back();

}