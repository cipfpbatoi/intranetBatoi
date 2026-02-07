<?php

namespace Intranet\UI\Panels;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Lang;
use Illuminate\View\View;
use Intranet\UI\Botones\Boton;
use Intranet\UI\Botones\BotonBasico;
use Intranet\UI\Botones\BotonIcon;
use Intranet\UI\Botones\BotonImg;
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
    public const BOTON_NOFCT = 'nofct';
    public const BOTON_PENDIENTE = 'pendiente';
    public const BOTON_COLABORA = 'colabora';

    private const BOTON_TYPES = [
        self::BOTON_INDEX,
        self::BOTON_GRID,
        self::BOTON_PROFILE,
        self::BOTON_INFILE,
        self::BOTON_FCT,
        self::BOTON_NOFCT,
        self::BOTON_PENDIENTE,
        self::BOTON_COLABORA,
    ];

    /** @var array<string, array<int, Boton>> */
    private array $botones = [   // botons del panel
        'index' => [],
        'grid' => [],
        'profile' => [],
        'infile' => [],
        'fct' => [],
        'nofct' => [],
        'pendiente' => [],
        'colabora' => [],
    ];
    private string $model;     // model de dades
    /** @var array<int, Pestana> */
    private array $pestanas = [];  // pestanyes
    /** @var array<string, mixed> */
    private array $titulo = [];    // titol
    /** @var mixed */
    private $elementos = null; // elements
    /** @var array<string, mixed> */
    private array $data = [];   // array de més dades
    private ?Paginator $paginator = null; // paginador opcional
    public array $items = [];

    /**
     * @param string $modelo Nom base del model (p. ex. "Profesor")
     * @param mixed $rejilla Definició de columnes de la pestanya principal
     * @param string|null $vista Vista opcional de la pestanya principal
     * @param bool $creaPestana Si és true crea la pestanya grid inicial
     * @param array|null $include Includes de la pestanya (modals, etc.)
     */
    /**
     * @param string $modelo Nom base del model (p. ex. "Profesor")
     * @param array|null $rejilla Definició de columnes de la pestanya principal
     * @param string|null $vista Vista opcional de la pestanya principal
     * @param bool $creaPestana Si és true crea la pestanya grid inicial
     * @param array $include Includes de la pestanya (modals, etc.)
     */
    public function __construct(
        string $modelo,
        ?array $rejilla = null,
        ?string $vista = null,
        bool $creaPestana = true,
        ?array $include = []
    )
    {
        $this->model = $modelo;
        if ($creaPestana) {
            $this->setPestana('grid', true, $vista, null, $rejilla, null, $include ?? []);
        }
        
    }


    /**
     * Ompli el panell i retorna la vista final.
     */
    /**
     * Ompli el panell i retorna la vista final.
     *
     * @param mixed $todos
     * @param mixed $titulo
     * @param string $vista
     * @param mixed $formulario
     * @return View|RedirectResponse
     */
    public function render($todos, $titulo, $vista, $formulario = null): View|RedirectResponse
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
    /**
     * Crea una botonera estàndard a partir de noms d'accions.
     *
     * @param array<int, string> $index
     * @param array<int, string> $grid
     * @param array<int, string> $profile
     */
    public function setBotonera(array $index = [], array $grid = [], array $profile = []): void
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
    public function setBothBoton(string $href, array $atributos = [], bool $relative = false): void
    {
        $this->setBoton(self::BOTON_GRID, new BotonImg($href, $atributos, $relative));
        $this->setBoton(self::BOTON_PROFILE, new BotonIcon($href, $atributos, $relative));
    }

    /**
     * Afig una pestanya o substituïx la primera.
     */
    public function setPestana(
        string $nombre,
        bool $activo = false,
        ?string $vista = null,
        ?array $filtro = null,
        ?array $rejilla = null,
        ?bool $sustituye = null,
        array $include = []
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
    /**
     * @param array<string, mixed> $titulo
     */
    public function setTitulo(array $titulo): void
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

    public function getRejilla(): ?array
    {
        return $this->pestanas[0]->getRejilla();
    }
    public function setRejilla(?array $grid): void
    {
        $this->pestanas[0]->setRejilla($grid);
    }

    /**
     * @param string|null $tipo Si es passa retorna només eixe grup.
     */
    /**
     * @param string|null $tipo Si es passa retorna només eixe grup.
     * @return array<int, Boton>|array<string, array<int, Boton>>
     */
    public function getBotones(?string $tipo = null): array
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
    public function getTitulo(string $que = 'index'): string
    {
        $key = "models." . ucwords(strtolower($this->getModel())) . ".$que";

        return Lang::has($key)
            ? trans($key, $this->titulo)
            : $this->getModel();
    }

    /**
     * Assigna la col·lecció d'elements a mostrar.
     */
    /**
     * @param mixed $elementos
     */
    public function setElementos($elementos): void
    {
        $this->elementos = $elementos;
    }

    /**
     * @return mixed
     */
    public function getElemento()
    {
        return $this->elementos;
    }

   
    // Filtra els elements d'una pestanya amb condicions.
    public function getElementos(Pestana $pestana)
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
    public function activaPestana(string $nombre): void
    {
        foreach ($this->pestanas as $pestana) {
            if ($pestana->getNombre() == $nombre) {
                $pestana->setActiva(true);
            } else {
                $pestana->setActiva(false);
            }
        }
    }

    private function getView(string $nombre, ?string $vista): string
    {
        if ($vista == null) {
            return 'intranet.partials.' . $nombre . "." . strtolower($this->model);
        }

        if (substr($vista, 0, 1)=='.') {
            return substr($vista, 1);
        }
        return 'intranet.partials.' . $vista;
    }

    public function __set(string $name, $value): void
    {
        $this->data[$name] = $value;
    }

    /**
     * @return mixed
     */
    public function __get(string $name)
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
    /**
     * @param mixed $todos
     * @param array<string, mixed> $titulo
     */
    private function feedPanel($todos, array $titulo): Panel
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
