<?php

namespace Intranet\Http\Resources;

abstract class PrintResource
{
    const RESOURCE = 'Intranet\\Http\\Resources\\';
    protected $elements;
    protected $file;
    protected $stamp;
    protected $flatten;

    public static function build($pdf, $elements)
    {
        $resource= self::RESOURCE.$pdf['resource'];
        $stamp = $pdf['stamp']??null;
        $flatten = $pdf['flatten']??true;
        return new $resource($elements, $pdf['fdf'], $flatten, $stamp);
    }

    public function __construct($elements, $file, $flatten=true, $stamp=null)
    {
        $this->elements = $elements;
        $this->file = $file;
        $this->flatten =$flatten;
        $this->stamp = $stamp;
    }

    /**
     * @return mixed
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * @return bool|mixed
     */
    public function getFlatten()
    {
        return $this->flatten;
    }

    /**
     * @return mixed
     */
    public function getStamp()
    {
        return $this->stamp;
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }


}
