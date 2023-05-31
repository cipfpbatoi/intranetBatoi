<?php
namespace Intranet\Finders;

use Intranet\Entities\Activity;

abstract class Finder {

    protected $dni;
    protected $document;

    public function __construct($document)
    {
        $this->dni = authUser()->dni??apiAuthUser()->dni;
        $this->document = $document;

    }

    protected function existsActivity($id)
    {
        if ($this->document->unique) {
            $modelo = "Intranet\\Entities\\".$this->document->modelo;
            return Activity::where('model_class', $modelo)
                ->where('model_id', $id)
                ->where('document', '=', $this->document->subject)
                ->count();
        } else {
            return 0;
        }
    }

    public function getDocument()
    {
        return $this->document;
    }

    public function getZip()
    {
        return false;
    }
}