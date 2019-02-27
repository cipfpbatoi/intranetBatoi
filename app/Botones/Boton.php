<?php

namespace Intranet\Botones;

use Illuminate\Database\Eloquent\Model;


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
   
    /*
     * __construct(
     * $href = modelo.accion
     * $atributos 
     *      text => texto boton
     *      clase => clase boton
     *      id => clau per formar el id   
     *      where => condicio per mostrar el boto (sols en el descendents de BotonElemento 
     * $relative => false ruta absoluta || true ruta relativa || prefijo
     * $postUrl => final de la ruta
     */

    private function translateText(){

        if (isset($this->atributos['text']))
            return trans("models." . ucwords($this->modelo) . "." .$this->atributos['text']) != "models." . ucwords($this->modelo) . "." . $this->atributos['text']
                ?  trans("models." . ucwords($this->modelo) . "." . $this->atributos['text'])
                : $this->atributos['text'];

        if (trans("models." . ucwords($this->modelo) . "." . $this->accion) != "models." . ucwords($this->modelo) . "." . $this->accion)
                return trans("models." . ucwords($this->modelo) . "." . $this->accion);

        if (trans("models." . ucwords($this->modelo) . ".default") != "models." . ucwords($this->modelo) . ".default" )
               return trans("models." . ucwords($this->modelo) . ".default");

        return trans("messages.buttons.$this->accion");



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
        if (array_key_exists($name, $this->atributos)) return ($this->atributos[$name]);

        return null;
    }

    // mostra el boto
    // utilitza la funcio html que esta definida en les filles
    public function show($elemento = null)
    {
        if (UserisAllow($this->roles)) {
            if ($elemento == null)
                echo $this->html();
            else if (isset($elemento))
                echo $this->html($elemento->getKey());
        }
    }
    
    protected abstract function html($key = null);

    protected function split()
    {
        if ($this->href != '#') {
            $a = explode(".", $this->href);
            $this->modelo = $a[0];
            $this->accion = $a[1];
            if (isset($a[2])) $this->postUrl = $a[2];
        }
    }

    // torna clase del boton en format html
    protected function clase()
    {
        $clase = $this->class != '' ? $this->class : $this->defaultClase;
        return " class='$clase " . $this->permanentClase . "'";
    }

    // torna id del boto en format html
    protected function id($key = null)
    {
        if ($key == null)
            return $this->id != '' ? " id='" . $this->id . "'" : '';
        return $this->id != '' ? " id='" . $this->id . $key . "'" : '';

    }

    // forma el text de l'enllaç amb la clau ($key)
    protected function href($key = null)
    {
        if ($this->href == '#') return '#';
        if ($this->relative===true)
            $prefix = '';
        else
            $prefix =  is_bool($this->relative) ? config('app.url') . '/' : config('app.url') . '/'.$this->relative.'/';
        if (!isset($this->postUrl)) return $key == null ? "href='" . $prefix . strtolower($this->modelo) . "/" . $this->accion . "'" : "href='" . $prefix . strtolower($this->modelo) . "/" . $key . "/" . $this->accion . "'";

        return $key == null ? "href='" . $prefix . strtolower($this->modelo) . "/" . $this->accion . "/" . $this->postUrl. "'"  : "href='" . $prefix . strtolower($this->modelo) . "/" . $key . "/" . $this->accion . "/" . $this->postUrl. "'";
    }

}
