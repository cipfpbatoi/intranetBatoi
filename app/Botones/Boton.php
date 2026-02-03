<?php

namespace Intranet\Botones;

use function existsTranslate;

/**
 * Classe base per a la generació i renderitzat de botons.
 */
abstract class Boton
{
    protected ?string $modelo = null;
    protected ?string $accion = null;
    protected ?string $href = null;
    protected ?string $text = null;
    protected ?string $postUrl = null; // final de la ruta
    protected array $atributos = [];
    protected ?string $defaultClase = null;   //clase que s'aplica si no es passa classe
    protected ?string $permanentClase = null; //clase que s'aplica sempre
    protected ?string $permanetClase = null; // compat amb propietat antiga
    protected bool|string $relative = false; // false ruta absoluta || true ruta relativa || prefijo

    protected int|array|null $roles = null;
    protected ?string $class = null;
    protected ?string $id = null;
    protected ?string $icon = null;
    protected ?string $title = null;
    protected ?string $onclick = null;
    protected ?string $img = null;
    protected array|string|null $where = null;
    protected array|string|null $orWhere = null;
    protected bool $disabled = false;

    /**
     * Resol el text del botó amb traduccions i textos per defecte.
     */
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

    /**
     * Tradueix un text ja proporcionat si hi ha clau existent.
     */
    private function translateExistingText()
    {
        if ($text = existsTranslate("models." . ucwords($this->modelo) . "." .$this->atributos['text'])) {
            return ($text);
        }
        return $this->atributos['text'];
    }


    /**
     * @param string $href Ruta base del botó (model.accio).
     * @param array $atributos Atributs i configuració del botó.
     * @param bool|string $relative Mode de ruta relativa o prefix.
     * @param string|null $postUrl Sufix opcional de ruta.
     */
    public function __construct($href, $atributos = [], $relative = false, $postUrl = null)
    {
        $this->postUrl = $postUrl;
        $this->split($this->href = $href);
        $this->atributos = $atributos;
        $this->relative = $relative;
        $this->roles = $atributos['roles'] ?? null;
        $this->class = $atributos['class'] ?? null;
        $this->id = $atributos['id'] ?? null;
        $this->icon = $atributos['icon'] ?? null;
        $this->title = $atributos['title'] ?? null;
        $this->onclick = $atributos['onclick'] ?? null;
        $this->img = $atributos['img'] ?? null;
        $this->where = $atributos['where'] ?? null;
        $this->orWhere = $atributos['orWhere'] ?? null;
        $this->disabled = (bool) ($atributos['disabled'] ?? false);
        $this->text = $this->translateText();
      }

    /**
     * Assigna atributs dinàmics.
     */
    public function __set($name, $value)
    {
        $this->atributos[$name] = $value;
    }

    /**
     * Llig atributs dinàmics.
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->atributos)) {
            return ($this->atributos[$name]);
        }

        return null;
    }

    /**
     * Retorna el botó renderitzat perquè el caller el puga imprimir.
     */
    public function show($elemento = null): string
    {
        return $this->render($elemento);
    }

    /**
     * Retorna el botó renderitzat si l'usuari té permís.
     */
    public function render($elemento = null)
    {
        if (userIsAllow($this->roles)) {
            $view = $elemento === null
                ? $this->html()
                : $this->html($elemento->getKey());

            return is_string($view) ? $view : $view->render();
        }
        return '';
    }
    
    /**
     * Genera el HTML del botó.
     */
    abstract protected function html($key = null);

    /**
     * Separa model/acció a partir del `href`.
     */
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

    /**
     * Neteja valors per a atributs HTML (classes, id, etc.).
     */
    protected function cleanAttr(?string $value): string
    {
        $value = strip_tags((string) $value);
        return trim(preg_replace('/\\s+/', ' ', $value));
    }

    /**
     * Indica si el botó està deshabilitat.
     */
    protected function isDisabled(): bool
    {
        return $this->disabled === true;
    }

    /**
     * Retorna la classe CSS final del botó.
     */
    protected function clase(): string
    {
        $clase = $this->class !== null && $this->class !== '' ? $this->class : ($this->defaultClase ?? '');
        $permanent = $this->permanentClase ?? $this->permanetClase ?? '';
        $disabled = $this->isDisabled() ? ' disabled' : '';
        return $this->cleanAttr(trim($clase.' '.$permanent.$disabled));
    }

    /**
     * Retorna l'ID HTML del botó.
     */
    protected function id($key = null): string|null
    {
        if ($key == null) {
            $id = $this->id ?? null;
        } else {
            $id = ($this->id ?? '') !== '' ? $this->id . $key : null;
        }

        if ($id === null) {
            return null;
        }

        $id = $this->cleanAttr($id);

        return $id === '' ? null : $id;

    }

    /**
     * Retorna atributs per a desactivar el botó segons el tipus.
     */
    protected function disabledAttr(string $type = 'link'): string
    {
        if (!$this->isDisabled()) {
            return '';
        }

        if ($type === 'button') {
            return " disabled='disabled' aria-disabled='true'";
        }

        return " aria-disabled='true' tabindex='-1'";
    }

    // torna data del boto en format html
    /**
     * Retorna els atributs `data-*` en format HTML.
     */
    protected function data(): string
    {
        $cadena = "";
        foreach ($this->atributos as $key => $value) {
            if (substr($key, 0, 5)=='data-') {
                $cadena .= " ".$key."='".$value."'";
            }
        }
        return $cadena;
    }

    // forma el text de l'enllaç amb la clau ($key)
    /**
     * Construeix l'URL final del botó.
     */
    protected function href($key = null): string
    {
        if ($this->href == '#') {
            return '#';
        }
        if ($this->isDisabled()) {
            return '#';
        }
        return $this->getAdress($key, $this->getPrefix(), $this->getPostfix());
    }

    /**
     * @return string
     */
    /**
     * Obté el prefix de ruta segons el mode `relative`.
     */
    private function getPrefix(): string
    {
        if ($this->relative === true) {
            return '';
        }

        $base = rtrim(config('app.url'), '/') . '/';

        if (is_bool($this->relative)) {
            return $base;
        }

        $relative = trim((string) $this->relative);
        if ($relative === '') {
            return $base;
        }

        if (preg_match('#^https?://#i', $relative) === 1) {
            return rtrim($relative, '/') . '/';
        }

        $relative = trim($relative, '/');

        return $base . $relative . '/';

    }

    /**
     * Obté el sufix de ruta si està definit.
     */
    private function getPostfix():string
    {
        return (isset($this->postUrl))?"/".$this->postUrl:"";
    }

    /**
     * Construeix l'adreça final a partir de prefix, clau i sufix.
     */
    private function getAdress($key, $prefix, $close):string
    {
        return $key == null
            ? $prefix . strtolower($this->modelo) . "/" . $this->accion . $close
            : $prefix . strtolower($this->modelo) . "/" . $key . "/" . $this->accion . $close;
    }

}
