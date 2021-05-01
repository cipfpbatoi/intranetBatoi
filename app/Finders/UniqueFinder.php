<?php
namespace Intranet\Finders;


class UniqueFinder extends Finder
{
    private $id;

    public function __construct($document)
    {
        $this->modelo = "Intranet\\Entities\\".$document['modelo'];
        $this->document = $document['document'];
        $this->request = $document['id'];
    }

    public function exec(){
        return $this->modelo::where('id',$this->id)->get();
    }
}