<?php
namespace Intranet\Services;

use Illuminate\Support\Facades\Log;
use Intranet\Entities\Documento;
use Styde\Html\Facades\Alert;
use function config, getClass, getClase;

/**
 * Servei per gestionar canvis d'estat d'un model i accions associades.
 */
class StateService
{
    private $element;
    private $statesElement;

    /**
     * Crea el servei amb un model o una classe.
     *
     * @param object|string $class Instancia de model o classe del model.
     * @param int|null $id Id del model si es passa una classe.
     */
    public function __construct($class, $id = null)
    {
        if (is_string($class)) {
            $this->element = $id ? $class::find($id) : new $class;
            $this->statesElement = config('modelos.' . getClass($class));
        } else {
            $this->element = $class;
            $this->statesElement = config('modelos.' . getClase($class));
        }

        $this->normalizeStatesElement();
    }

    /**
     * Canvia l'estat i executa accions associades.
     *
     * @param int $estado Nou estat a establir.
     * @param string|null $mensaje Missatge opcional per guardar i avisar.
     * @param string|null $fecha Data de resolucio (format Y-m-d).
     * @return int|false Estat resultant o false si falla.
     */
    public function putEstado($estado, $mensaje = null, $fecha = null)
    {
        if (!$this->element) {
            Log::warning('StateService: element no trobat per posar estat.');
            return false; // Retornem false en lloc de JSON per facilitar la gestió d'errors
        }

        if (!is_numeric($estado)) {
            Log::warning('StateService: estat invalid.', ['estado' => $estado]);
            return false;
        }

        if ($fecha !== null) {
            $this->makeDocument();
            $this->dateResolve($fecha, $mensaje);
        }

        $this->element->estado = $estado;

        if (!$this->element->save()) {
            return false; // Retornem false si hi ha un error en el save()
        }

        AdviseService::exec($this->element, $mensaje);

        return $this->element->estado;
    }

    /**
     * Guarda el document associat si hi ha fitxer.
     */
    private function makeDocument()
    {
        if (!isset($this->element->fichero) || empty($this->element->fichero)) {
            return;
        }

        $gestor = new GestorService($this->element);
        $gestor->save([
            'tipoDocumento' => getClase($this->element),
            'rol' => '2',
        ]);
    }

    /**
     * Assigna la data de resolucio i el missatge al camp configurat.
     *
     * @param string $fecha Data en format Y-m-d.
     * @param string|null $mensaje Missatge a guardar.
     */
    private function dateResolve($fecha, $mensaje)
    {
        if (isset($this->element->fechasolucion)) {
            $this->element->fechasolucion = $fecha;
        }
        if (isset($this->statesElement['mensaje']) && $mensaje) {
            $field = $this->statesElement['mensaje'];
            $this->element->$field = $mensaje;
        }
    }

    /**
     * Resol l'element segons la configuracio del model.
     *
     * @param string|null $mensaje Missatge opcional.
     * @return int|false Estat resultant o false si no hi ha configuracio.
     */
    public function resolve($mensaje = null)
    {
        $estado = $this->getConfiguredState('resolve');
        if ($estado === null) {
            return false;
        }
        return $this->putEstado($estado, $mensaje, hoy());
    }

    /**
     * Rebutja l'element segons la configuracio del model.
     *
     * @param string|null $mensaje Missatge opcional.
     * @return int|false Estat resultant o false si no hi ha configuracio.
     */
    public function refuse($mensaje = null)
    {

        $estado = $this->getConfiguredState('refuse');
        if ($estado === null) {
            return false;
        }
        return $this->putEstado($estado, $mensaje);
    }

    /**
     * Marca l'element com a imprimit segons la configuracio del model.
     *
     * @return int|false Estat resultant o false si no hi ha configuracio.
     */
    public function _print()
    {
        $printState = $this->getConfiguredState('print');
        if ($printState === null) {
            return false;
        }

        $resolveState = $this->getConfiguredState('resolve');

        if ($resolveState !== null && $printState == $resolveState) {
            return $this->putEstado($printState, '', hoy());
        }

        return $this->putEstado($printState);
    }

    /**
     * Retorna l'estat actual de l'element.
     *
     * @return int|null
     */
    public function getEstado()
    {
        return $this->element ? $this->element->estado : null;
    }

    /**
     * Normalitza la configuracio del model.
     */
    private function normalizeStatesElement(): void
    {
        $config = is_array($this->statesElement) ? $this->statesElement : [];

        foreach (['resolve', 'refuse', 'print', 'completa'] as $key) {
            if (isset($config[$key]) && is_numeric($config[$key])) {
                $config[$key] = (int) $config[$key];
            }
        }

        if (isset($config['estados']) && is_array($config['estados'])) {
            $normalized = [];
            foreach ($config['estados'] as $estado => $label) {
                $normalized[(int) $estado] = $label;
            }
            $config['estados'] = $normalized;
        }

        $this->statesElement = $config;
    }

    /**
     * Retorna un estat configurat o null si falta.
     *
     * @param string $key
     * @return int|null
     */
    private function getConfiguredState(string $key): ?int
    {
        if (!isset($this->statesElement[$key])) {
            Log::info('StateService: accio sense estat configurat.', [
                'accio' => $key,
                'model' => is_object($this->element) ? getClase($this->element) : null,
            ]);
            return null;
        }

        $estado = $this->statesElement[$key];
        if (!is_numeric($estado)) {
            Log::warning('StateService: estat configurat invalid.', [
                'accio' => $key,
                'estado' => $estado,
            ]);
            return null;
        }

        return (int) $estado;
    }

    /**
     * Modifica l'estat d'un conjunt d'elements
     *
     * @param mixed $todos Col·lecció d'elements
     * @param mixed $accio Pot ser un estat (int) o una funció de StateService
     */
    public static function makeAll($todos, $accio)
    {
        if (!$todos || $todos->count() === 0) {
            return;
        }

        foreach ($todos as $element) {
            $stateService = new self(get_class($element), $element->id);
            $result = is_string($accio) ? $stateService->$accio(false) : $stateService->putEstado($accio);

            if ($result === false) {
                Alert::danger("Error en processar {$element->id}.");
            }
        }
    }

    /**
     * Enllaça múltiples elements a un document.
     *
     * @param mixed $todos Col·lecció d'elements
     * @param Documento $doc Document a enllaçar
     */
    public static function makeLink($todos, $doc)
    {
        if (!$todos || !$doc) {
            return;
        }

        foreach ($todos as $element) {
            try {
                $element->idDocumento = $doc;
                $element->save();
            } catch (\Exception $e) {
                Log::error("Error guardant element: " . $e->getMessage());
                continue;
            }
        }
    }

}
