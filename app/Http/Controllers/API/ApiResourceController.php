<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Intranet\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class ApiResourceController extends Controller
{

    protected $namespace = 'Intranet\\Entities\\';
    protected $model;
    protected $class;
    protected $resource;
    protected $guard='api';

    public function __construct()
    {
        $this->class = $this->namespace . $this->model;
    }

    public function index()
    {
        $class = $this->resolveClass();
        $data = $class::all();

        // Si tens Resource, el fem servir; si no, tornem el teu JSON clàssic
        return $this->hasResource()
            ? $this->resource::collection($data)
            : $this->sendResponse($data);
    }

    public function destroy($id)
    {
        $class = $this->resolveClass();
        $class::destroy($id);
        return $this->sendResponse(['deleted' => true], 'OK');
    }

    public function store(Request $request)
    {
        try {
            $class = $this->resolveClass();
            $payload = $this->validatedPayloadForStore($request);
            $created = $class::create($payload);

            // Mantinc el teu format tradicional
            return $this->sendResponse(['created' => true, 'id' => $created->id], 'OK');
        } catch (Throwable $e) {
            report($e);
            return $this->sendError('Internal server error', 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $class = $this->resolveClass();
            $registro = $class::find($id);

            if (!$registro) {
                return $this->sendNotFound("Not found: {$class} #{$id}");
            }

            $payload = $this->validatedPayloadForUpdate($request);
            $registro->update($payload);
            $registro->save();

            return $this->sendResponse(['updated' => true], 'OK');
        } catch (Throwable $e) {
            report($e);
            return $this->sendError('Internal server error', 500);
        }
    }

    public function show($id)
    {
        $class = $this->resolveClass();
        $item = $class::find($id);

        if (!$item) {
            return $this->sendNotFound("Not found: {$class} #{$id}");
        }

        return $this->hasResource()
            ? new $this->resource($item)
            : $this->sendResponse($item);
    }

    public function edit($id)
    {
        $class = $this->resolveClass();
        $item = $class::find($id);

        if (!$item) {
            return $this->sendNotFound("Not found: {$class} #{$id}");
        }

        return $this->sendResponse($item);
    }
   
    
    protected function resolveClass(): string
    {
        // Si ja està resolta, usa-la
        if ($this->class && class_exists($this->class)) {
            return $this->class;
        }

        // Si no hi ha model configurat, aturem amb missatge clar
        if (!$this->model) {
            abort(500, 'API misconfigured: $model not set in '.static::class);
        }

        // Accepta FQN o nom curt
        $candidate = ltrim($this->model, '\\');
        if (!class_exists($candidate)) {
            $candidate = $this->namespace.$this->model;
        }
        if (!class_exists($candidate)) {
            abort(500, 'Model class not found: '.$this->model);
        }

        // Guarda i retorna
        return $this->class = $candidate;
    }

    protected function hasResource(): bool
    {
        return $this->resource && class_exists($this->resource);
    }

    /**
     * @return array<string, mixed>
     */
    protected function validatedPayloadForStore(Request $request): array
    {
        $rules = $this->storeRules();
        if (!empty($rules)) {
            return $request->validate($rules);
        }

        return $this->filterMutationPayload($request);
    }

    /**
     * @return array<string, mixed>
     */
    protected function validatedPayloadForUpdate(Request $request): array
    {
        $rules = $this->updateRules();
        if (!empty($rules)) {
            return $request->validate($rules);
        }

        return $this->filterMutationPayload($request);
    }

    /**
     * Sobrescriu en controladors concrets quan necessites validació en create.
     *
     * @return array<string, mixed>
     */
    protected function storeRules(): array
    {
        return [];
    }

    /**
     * Sobrescriu en controladors concrets quan necessites validació en update.
     *
     * @return array<string, mixed>
     */
    protected function updateRules(): array
    {
        return [];
    }

    /**
     * Permet limitar camps mutables per endpoint sense tocar el model.
     * Retorna null per mantindre compatibilitat total.
     *
     * @return array<int, string>|null
     */
    protected function mutableFields(): ?array
    {
        return null;
    }

    /**
     * @return array<string, mixed>
     */
    protected function filterMutationPayload(Request $request): array
    {
        $fields = $this->mutableFields();
        if ($fields === null) {
            return $request->all();
        }

        return $request->only($fields);
    }

 
    protected function sendResponse($result, $message = null)
    {
        return response()->json(['success'=>true,'data'=>$result]);
    }

    protected function sendError($error, $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => is_string($error) ? $error : 'Request error',
        ], $code);
    }

    protected function sendNotFound(string $error = 'Not found')
    {
        return $this->sendError($error, 404);
    }

    protected function sendFail($error, $code = 400)
    {
        if (is_array($error)) {
            $success = (bool) ($error['success'] ?? false);
            $message = (string) ($error['message'] ?? 'Request error');
            $payload = ['success' => $success, 'message' => $message];
            if (array_key_exists('errors', $error)) {
                $payload['errors'] = $error['errors'];
            }

            return response()->json($payload, $code);
        }

        return $this->sendError((string) $error, $code);
    }

    public function ApiUser(Request $request)
    {
        return $request->user('sanctum') ?? $request->user('api');
    }

    /**
     * Marca resposta d'endpoint legacy per facilitar deprecació controlada.
     */
    protected function markLegacyUsage(
        JsonResponse $response,
        string $legacyContract,
        ?string $replacementHint = null
    ): JsonResponse {
        $response->headers->set('Deprecation', 'true');
        $response->headers->set('Sunset', 'Wed, 31 Dec 2026 23:59:59 GMT');
        if ($replacementHint !== null && $replacementHint !== '') {
            $response->headers->set('X-API-Replacement', $replacementHint);
        }

        Log::info('API legacy contract consumed', [
            'contract' => $legacyContract,
            'path' => request()->path(),
            'query' => request()->query(),
            'user' => auth()->guard('api')->id(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return $response;
    }

}
