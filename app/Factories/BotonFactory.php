<?php

namespace Intranet\Factories;

class BotonFactory
{
    public static function create($tipo, $href, $atributos = [], $relative = false, $postUrl = null)
    {
        return new Boton($tipo, $href, $atributos, $relative, $postUrl);
    }
}

class Boton
{
    protected $tipo;
    protected $href;
    protected $atributos;
    protected $relative;
    protected $postUrl;
    protected $defaultClase;
    protected $permanentClase;

    private $clases = [
        'basico' => ['btn-primary', 'btn btn-round txtButton'],
        'confirmacion' => ['btn-primary', 'btn txtButton confirm'],
        'icon' => ['btn-primary', 'btn btn-xs iconButton'],
        'img' => ['', 'imgButton'],
        'post' => ['btn-success', 'btn btn-xs txtButton'],
    ];

    public function __construct($tipo, $href, $atributos = [], $relative = false, $postUrl = null)
    {
        $this->tipo = $tipo;
        $this->href = $href;
        $this->atributos = $atributos;
        $this->relative = $relative;
        $this->postUrl = $postUrl;

        $this->defaultClase = $this->clases[$tipo][0] ?? 'btn-default';
        $this->permanentClase = $this->clases[$tipo][1] ?? 'btn';
    }

    public function show($key = null)
    {
        return "<x-boton href='{$this->href}' class='{$this->clase()}' id='{$this->id()}' 
        icon='{$this->icon()}' text='{$this->text()}' 
        img='{$this->img()}' onclick='{$this->onclick()}' />";
    }

    protected function icon(): string
    {
        return $this->atributos['icon'] ?? '';
    }
    protected function onclick(): string
    {
        return $this->atributos['onclick'] ?? '';
    }

    protected function img(): string
    {
        return $this->atributos['img'] ?? '';
    }

    protected function text(): string
    {
        return $this->atributos['text'] ?? 'Botó';
    }


    protected function clase(): string
    {
        return $this->defaultClase . ' ' . $this->permanentClase;
    }

    protected function href($key = null): string
    {
        return $key ? $this->href . '/' . $key : $this->href;
    }

    protected function id($key = null): ?string
    {
        return $key ? ($this->atributos['id'] ?? '') . $key : ($this->atributos['id'] ?? null);
    }
}