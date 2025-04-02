<?php

namespace Intranet\Botones;

use function existsTranslate;

abstract class Boton
{
    protected $modelo;
    protected $accion;
    protected $href;
    protected $text;
    protected $postUrl; // final de la ruta
    protected $atributos = [];
    protected $defaultClase;   //clase que s'aplica si no es passa classe
    protected $permanetClase; //clase que s'aplica sempre
    protected $relative; // false ruta absoluta || true ruta relativa || prefijo

    private function translateText()
    {
        if (isset($this->atributos['text'])) {
            return $this->translateExistingText();
        }
        if ($text = existsTranslate("models." . ucwords($this->modelo) . "." . $this->postUrl)) {
            return $text;
        }
        if ($text = existsTranslate("models." . ucwords($this->modelo) . "." . $this->accion)) {
            return $text;
        }
        if ($text = existsTranslate("models." . ucwords($this->modelo) . ".default")) {
            return $text;
        }
        return trans("messages.buttons.$this->accion");
    }

    private function translateExistingText()
    {
        if ($text = existsTranslate("models." . ucwords($this->modelo) . "." .$this->atributos['text'])) {
            return ($text);
        }
        return $this->atributos['text'];
    }


    public function __construct($href, $atributos = [], $relative = false, $postUrl = null)
    {
        $this->postUrl = $postUrl;
        $this->split($this->href = $href);
        $this->atributos = $atributos;
        $this->relative = $relative;
        $this->text = $this->translateText();
      }

    public function __set($name, $value)
    {
        $this->atributos[$name] = $value;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->atributos)) {
            return ($this->atributos[$name]);
        }

        return null;
    }

    // mostra el boto
    // utilitza la funcio html que esta definida en les filles
    public function show($elemento = null)
    {
        if (userIsAllow($this->roles)) {
            if ($elemento == null) {
                echo $this->html();
            } elseif (isset($elemento)) {
                echo $this->html($elemento->getKey());
            }
        }
    }

    public function render($elemento = null)
    {
        if ( userIsAllow($this->roles)) {
            return $elemento === null
                ? $this->html()
                : $this->html($elemento->getKey());
        }
        return null;
    }
    
    abstract protected function html($key = null);

    protected function split()
    {
        if ($this->href != '#') {
            $a = explode(".", $this->href);
            $this->modelo = $a[0];
            $this->accion = isset($a[1])?$a[1]:'';
            if (isset($a[2])) {
                $this->postUrl = $a[2];
            }
        }
    }

    // torna clase del boton en format html
    protected function clase(): string
    {
        $clase = $this->class != '' ? $this->class : $this->defaultClase;
        return $clase.' '.$this->permanentClase;
    }

    // torna id del boto en format html
    protected function id($key = null): string|null
    {
        if ($key == null) {
            return $this->id ?? null;
        }
        return $this->id != '' ? $this->id . $key : null;

    }

    // torna data del boto en format html
    protected function data(): string
    {
        $cadena = "";
        foreach ($this->atributos as $key => $value) {
            if (substr($key, 0, 5)=='data-') {
                $cadena = " ".$key."='".$value."'";
            }
        }
        return $cadena;
    }

    // forma el text de l'enllaç amb la clau ($key)
    protected function href($key = null): string
    {
        if ($this->href == '#') {
            return '#';
        }
        return $this->getAdress($key, $this->getPrefix(), $this->getPostfix());
    }

    /**
     * @return string
     */
    private function getPrefix(): string
    {
        if ($this->relative === true) {
            return '';
        }

        return is_bool($this->relative)?config('app.url').'/':config('app.url').'/'.$this->relative.'/';

    }

    private function getPostfix():string
    {
        return (isset($this->postUrl))?"/".$this->postUrl:"";
    }

    private function getAdress($key, $prefix, $close):string
    {
        return $key == null
            ? $prefix . strtolower($this->modelo) . "/" . $this->accion . $close
            : $prefix . strtolower($this->modelo) . "/" . $key . "/" . $this->accion . $close;
    }

}
