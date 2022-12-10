<?php

namespace Intranet\Http\PrintResources;

abstract class PrintResource
{
    const RESOURCE = 'Intranet\\Http\\PrintResources\\';
    protected $elements;
    protected $file;
    protected $stamp;
    protected $flatten;

    public static function build($source, $elements)
    {
        $resource= self::RESOURCE.$source;
        return new $resource($elements);
    }

    public function __construct($elements, $file=null, $flatten=true, $stamp=null)
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
