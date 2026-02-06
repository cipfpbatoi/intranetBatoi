<?php

namespace Intranet\Botones;

use Illuminate\Contracts\Pagination\Paginator;
use InvalidArgumentException;

/**
 * Contenidor de pestanyes, botons i dades de vista per als panells CRUD.
 */
class Panel
{
    public const BOTON_INDEX = 'index';
    public const BOTON_GRID = 'grid';
    public const BOTON_PROFILE = 'profile';
    public const BOTON_INFILE = 'infile';
    public const BOTON_FCT = 'fct';

    private const BOTON_TYPES = [
        self::BOTON_INDEX,
        self::BOTON_GRID,
        self::BOTON_PROFILE,
        self::BOTON_INFILE,
        self::BOTON_FCT,
    ];

    private array $botones = [   // botons del panel
        'index' => [],
        'grid' => [],
        'profile' => [],
        'infile' => [],
        'fct' => [],
    ];
    private string $model;     // model de dades
    private array $pestanas = [];  // pestanyes
    private array $titulo = [];    // titol
    private $elementos = null; // elements
    private array $data = [];   // array de més dades
    private ?Paginator $paginator = null; // paginador opcional
    public array $items = [];

    /**
     * @param string $modelo Nom base del model (p. ex. "Profesor")
     * @param mixed $rejilla Definició de columnes de la pestanya principal
     * @param string|null $vista Vista opcional de la pestanya principal
     * @param bool $creaPestana Si és true crea la pestanya grid inicial
     * @param array $include Includes de la pestanya (modals, etc.)
     */
    public function __construct($modelo, $rejilla = null, $vista = null, $creaPestana=true, $include=[])
    {
        $this->model = $modelo;
        if ($creaPestana) {
            $this->setPestana('grid', true, $vista, null, $rejilla, null, $include);
        }
        
    }


    /**
     * Ompli el panell i retorna la vista final.
     */
    public function render($todos, $titulo, $vista, $formulario=null)
    {
        if (!$this->countPestana()) {
            return redirect()->route('home');
        }

        $panel = $this->feedPanel($todos, $titulo);

        return view($vista, compact('panel', 'formulario'));

    }


    /**
     * Crea una botonera estàndard a partir de noms d'accions.
     */
    public function setBotonera($index = [], $grid = [], $profile = []): void
    {
        if ($index != []) {
            foreach ($index as $btn) {
                $this->setBoton(self::BOTON_INDEX, new BotonBasico("$this->model.$btn"));
            }
        }
        if ($grid != []) {
            foreach ($grid as $btn) {
                $this->setBoton(self::BOTON_GRID, new BotonImg($this->model . "." . $btn));
            }
        }
        if ($profile != []) {
            foreach ($profile as $btn) {
                $this->setBoton(self::BOTON_PROFILE, new BotonIcon("$this->model.$btn"));
            }
        }
    }

    /**
     * Afig un botó al grup indicat.
     *
     * @throws InvalidArgumentException
     */
    public function setBoton(string $tipo, Boton $boton): void
    {
        $this->ensureValidBotonType($tipo);
        $this->botones[$tipo][] = $boton;
    }

    /**
     * Afig el mateix botó a `grid` i `profile`.
     */
    public function setBothBoton($href, $atributos = [], $relative = false): void
    {
        $this->setBoton(self::BOTON_GRID, new BotonImg($href, $atributos, $relative));
        $this->setBoton(self::BOTON_PROFILE, new BotonIcon($href, $atributos, $relative));
    }

    /**
     * Afig una pestanya o substituïx la primera.
     */
    public function setPestana(
        $nombre,
        $activo = false,
        $vista = null,
        $filtro = null,
        $rejilla = null,
        $sustituye = null,
        $include=[]
    ): void
    {
        if ($activo) {
            $this->desactivaAll();
        }
        if ($sustituye) {
            $this->pestanas[0] = new Pestana(
                $nombre,
                $activo,
                $this->getView($nombre, $vista),
                $filtro,
                $rejilla,
                $include
            );
        } else {
            $this->pestanas[] = new Pestana(
                $nombre,
                $activo,
                $this->getView($nombre, $vista),
                $filtro,
                $rejilla,
                $include
            );
        }
    }

    /**
     * Retorna el nombre de pestanyes disponibles.
     */
    public function countPestana(): int
    {
        return count($this->pestanas);
    }

    /**
     * Guarda els placeholders de títol per a traduccions.
     */
    public function setTitulo($titulo): void
    {
        $this->titulo = $titulo;
    }

    // Para que només hi haja una pestanya activa, desactiva la resta.
    private function desactivaAll(): void
    {
        if ($this->pestanas) {
            foreach ($this->pestanas as $pestana) {
                $pestana->setActiva(false);
            }
        }
    }
    

    /**
     * Retorna el nom del model associat al panell.
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * Retorna totes les pestanyes del panell.
     */
    public function getPestanas(): array
    {
        return $this->pestanas;
    }

    public function getRejilla()
    {
        return $this->pestanas[0]->getRejilla();
    }
    public function setRejilla($grid): void
    {
        $this->pestanas[0]->setRejilla($grid);
    }

    /**
     * @param string|null $tipo Si es passa retorna només eixe grup.
     */
    public function getBotones($tipo = null): array
    {
        if (isset($tipo)) {
            return (isset($this->botones[$tipo]))?$this->botones[$tipo]:[];
        }
        return $this->botones;
    }

    /**
     * Retorna quants botons hi ha en un grup.
     */
    public function countBotones(string $tipo): int
    {
        return count($this->getBotones($tipo));
    }

    /**
     * Resol el títol traduït segons el model i l'acció.
     */
    public function getTitulo($que = 'index'): string
    {
        return trans("models." . ucwords(strtolower($this->getModel())) . ".$que", $this->titulo);
    }

    /**
     * Assigna la col·lecció d'elements a mostrar.
     */
    public function setElementos($elementos): void
    {
        $this->elementos = $elementos;
    }

    public function getElemento()
    {
        return $this->elementos;
    }

   
    // Filtra els elements d'una pestanya amb condicions.
    public function getElementos($pestana)
    {
        $elementos = $this->elementos;
        if ($filtro = $pestana->getFiltro()) {
            for ($i = 0; $i < count($filtro); $i = $i + 2) {
                $elementos = $elementos->where($filtro[$i], '=', $filtro[$i+1]);
            }
        }
        return $elementos;
    }

    /**
     * Retorna el paginador si la cerca original era paginada.
     */
    public function getPaginator(): ?Paginator
    {
        return $this->paginator;
    }


    /**
     * Activa una pestanya pel nom i desactiva la resta.
     */
    public function activaPestana($nombre): void
    {
        foreach ($this->pestanas as $pestana) {
            if ($pestana->getNombre() == $nombre) {
                $pestana->setActiva(true);
            } else {
                $pestana->setActiva(false);
            }
        }
    }

    private function getView($nombre, $vista): string
    {
        if ($vista == null) {
            return 'intranet.partials.' . $nombre . "." . strtolower($this->model);
        }

        if (substr($vista, 0, 1)=='.') {
            return substr($vista, 1);
        }
        return 'intranet.partials.' . $vista;
    }

    public function __set($name, $value): void
    {
        $this->data[$name] = $value;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
    }

    /**
     * Valida que el tipus pertany a la botonera coneguda.
     *
     * @throws InvalidArgumentException
     */
    private function ensureValidBotonType(string $tipo): void
    {
        if (in_array($tipo, self::BOTON_TYPES, true)) {
            return;
        }

        throw new InvalidArgumentException(
            "Tipus de botó no vàlid '{$tipo}'. Tipus admesos: ".implode(', ', self::BOTON_TYPES)
        );
    }


    /**
     * @param $todos
     * @param $titulo
     * @return Panel
     */
    private function feedPanel($todos, $titulo): Panel
    {
        if ($todos instanceof Paginator) {
            $this->paginator = $todos;
            $todos = collect($todos->items());
        } else {
            $this->paginator = null;
        }

        $this->setElementos($todos);
        $this->setTitulo($titulo);
        return $this;
    }

    public function getLastPestanaWithModals(): array
    {
        $pestanas = $this->getPestanas();
        $last = is_array($pestanas) ? end($pestanas) : collect($pestanas)->last();

        return $last?->getInclude('modal') ?? [];
    }
}
