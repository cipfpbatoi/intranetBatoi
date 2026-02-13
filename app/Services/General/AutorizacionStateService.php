<?php

namespace Intranet\Services\General;

/**
 * Servei d'aplicació per a transicions d'estat en fluxos d'autorització.
 *
 * Encapsula l'ús de `StateService` i retorna un resultat homogeni per a
 * transicions que necessiten redirecció contextual (`initial` i `final`).
 */
class AutorizacionStateService
{
    private string $class;

    public function __construct(string $class)
    {
        $this->class = $class;
    }

    /**
     * Mou l'element a estat de cancel·lació.
     */
    public function cancel(int|string $id): bool
    {
        return $this->setState($id, -1);
    }

    /**
     * Inicialitza l'element a l'estat configurat pel caller.
     */
    public function init(int|string $id, int $initState = 1): bool
    {
        return $this->setState($id, $initState);
    }

    /**
     * Aplica l'acció `_print` configurada en `StateService`.
     */
    public function print(int|string $id): bool
    {
        $stSrv = new StateService($this->class, $id);

        return $stSrv->_print() !== false;
    }

    /**
     * @return array{initial:int|null,final:int}|false
     */
    public function resolve(int|string $id, ?string $explicacion = null): array|false
    {
        return $this->transitionWithResult($id, function (StateService $stSrv) use ($explicacion) {
            return $stSrv->resolve($explicacion);
        });
    }

    /**
     * @return array{initial:int|null,final:int}|false
     */
    public function accept(int|string $id): array|false
    {
        return $this->transitionWithResult($id, function (StateService $stSrv, $initialState) {
            return $stSrv->putEstado($initialState + 1);
        });
    }

    /**
     * @return array{initial:int|null,final:int}|false
     */
    public function resign(int|string $id): array|false
    {
        return $this->transitionWithResult($id, function (StateService $stSrv, $initialState) {
            return $stSrv->putEstado($initialState - 1);
        });
    }

    /**
     * @return array{initial:int|null,final:int}|false
     */
    public function refuse(int|string $id, ?string $explicacion = null): array|false
    {
        return $this->transitionWithResult($id, function (StateService $stSrv) use ($explicacion) {
            return $stSrv->refuse($explicacion);
        });
    }

    /**
     * Assigna un estat concret i retorna si l'operació és correcta.
     */
    private function setState(int|string $id, int $state): bool
    {
        $stSrv = new StateService($this->class, $id);

        return $stSrv->putEstado($state) !== false;
    }

    /**
     * Executa una transició i retorna els estats per a la capa de presentació.
     *
     * @return array{initial:int|null,final:int}|false
     */
    private function transitionWithResult(int|string $id, callable $resolver): array|false
    {
        $stSrv = new StateService($this->class, $id);
        $initialState = $stSrv->getEstado();
        $finalState = $resolver($stSrv, $initialState);

        if ($finalState === false) {
            return false;
        }

        return [
            'initial' => $initialState,
            'final' => $finalState,
        ];
    }
}
