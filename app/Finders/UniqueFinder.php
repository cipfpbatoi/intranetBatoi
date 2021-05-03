<?php
namespace Intranet\Finders;


class UniqueFinder extends Finder
{
    private $id;

    public function __construct($document)
    {
        $this->document = $document['document'];
        $this->id = $document['id'];
    }

    public function exec(){
        $modelo = "Intranet\\Entities\\".$this->document->modelo;
        return $modelo::where('id',$this->id)->get();
    }
}