<?php
namespace Intranet\Services;

use Intranet\Componentes\Pdf;
use Intranet\Finders\Finder;
use Jenssegers\Date\Date;
use Styde\Html\Facades\Alert;

/**
 * Servei PrintReportService.
 */
class PrintReportService
{
    /** @var mixed */
    private $finder;
    /** @var mixed */
    private $document;
    /** @var mixed */
    private $initialState;
    /** @var mixed */
    private $finalState;
    /** @var mixed */
    private $linked;
    /** @var mixed */
    private $elements;
    private string $class;

    /**
     * PrintReportService constructor.
     *
     * @param Finder $finder
     * @param mixed $initialState
     * @param mixed $finalState
     * @param bool  $linked
     */
    public function __construct(Finder $finder, $initialState = null, $finalState = '_print', $linked = true)
    {
        $this->finder = $finder;
        $this->document = $finder->getDocument();
        $this->class = "Intranet\\Entities\\" . $this->document->modelo;
        $this->initialState = $initialState;
        $this->finalState = $finalState;
        $this->linked = $linked;
        $this->elements = $finder->exec();
    }

    public function printAndSaveGestor()
    {
        if ($this->elements->Count()) {
            $pdf = Pdf::hazPdf($this->document->getView(), $this->elements, null, $this->document->orientation);
            $nom = $this->document->modelo . new Date() . '.pdf';
            $nomComplet = 'gestor/' . curso() . '/informes/' . $nom;
            $gestor = new GestorService();
            $doc = $gestor->save([
                'fichero' => $nomComplet,
                'tags' => $this->document->tags
            ]);
            $this->changeState();
            if ($this->linked) {
                $this->linking($doc);
            }
            return $pdf->save(storage_path('/app/' . $nomComplet))->download($nom);
        }
        Alert::info(trans('messages.generic.empty'));
        return back();
    }

    private function linking($doc)
    {
        foreach ($this->elements as $element) {
            $element->idDocumento = $doc;
            $element->save();
        }
    }

    private function changeState()
    {
        if (is_string($this->finalState)) {
            $accion = $this->finalState;
            foreach ($this->elements as $elements) {
                $this->$accion($elements->id, false);
            }
        } else {
            foreach ($this->elements as $element) {
                $this->class::putEstado($element->id, $this->finalState);
            }
        }
    }
}
