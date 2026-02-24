<?php

namespace Intranet\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

/**
 * Event d'enviament de correu.
 */
class EmailSended
{

    use Dispatchable,
        InteractsWithSockets,
        SerializesModels;

    /**
     * @var mixed
     */
    public $elemento;

    /**
     * @var string
     */
    public $modelo;

    /**
     * @var string
     */
    public $correo;

    /**
     * @param mixed $elemento
     * @return string
     */
    private function getModel($elemento)
    {
        return class_basename($elemento);
    }

    /**
     * @param mixed $elemento
     * @param string $correo
     */
    public function __construct($elemento, $correo)
    {
        $this->elemento = $elemento;
        $this->modelo = $this->getModel($elemento);
        $this->correo = $correo;
    }

}
