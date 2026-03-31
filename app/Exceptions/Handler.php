<?php

namespace Intranet\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Intranet\Services\Notifications\NotificationService;
use Styde\Html\Facades\Alert;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use function config;

/**
 * Gestor centralitzat d'excepcions de l'aplicació.
 */
class Handler extends ExceptionHandler
{
    protected $dontReport = [
        AuthenticationException::class,
        AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        ValidationException::class,
    ];

    protected $dontFlash = ['password','password_confirmation'];

    /**
     * Reporta una excepció i registra-la en el log d'excepcions.
     *
     * @param \Throwable $exception
     * @return void
     */
    public function report(Throwable $exception): void
    {
        $this->logException($exception);

        parent::report($exception);
    }

    /**
     * Renderitza la resposta HTTP per a una excepció.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $exception)
    {
        // Envia avís només si el missatge és “informatiu” i no és SRF
        $msg = (string) $exception->getMessage();
        $isValidation = $exception instanceof ValidationException;
        $isAuthorization = $exception instanceof AuthorizationException;
        $statusCode = ($exception instanceof HttpExceptionInterface)
            ? $exception->getStatusCode()
            : null;
        $isForbidden = $statusCode === 403;
        $isNotFound = $exception instanceof ModelNotFoundException
            || $exception instanceof NotFoundHttpException
            || $statusCode === 404;

        if ($this->shouldNotify($exception, $msg, $isValidation, $isAuthorization, $isForbidden, $isNotFound)) {
            app(NotificationService::class)->send(
                config('avisos.errores'),
                $this->buildNotificationSummary($exception)
            );
        }

        if ($exception instanceof IntranetException) {
            return $this->renderIntranetException($request, $exception);
        }

        // Missatge visual per a errors de BD (en respostes HTML)
        if ($exception instanceof \PDOException) {
            Alert::danger("Error en la base de dades. No s'ha pogut completar l'operació degut a: "
                . $exception->getMessage()
                . ". Si no ho entens, posa't en contacte amb l'administrador.");
        }

        // JSON / API
        if ($request->expectsJson()) {
            // 1) Tria codi si és una HttpException
            $status = ($exception instanceof HttpExceptionInterface)
                ? $exception->getStatusCode()
                : null;

            // 2) Mapejos habituals
            if ($exception instanceof ValidationException) {
                $status = 422;
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors'  => $exception->errors(),
                ], $status);
            }

            if ($exception instanceof AuthenticationException) {
                $status = 401;
            } elseif ($exception instanceof AuthorizationException) {
                $status = 403;
            } elseif ($exception instanceof ModelNotFoundException || $exception instanceof NotFoundHttpException) {
                $status = 404;
            } elseif ($exception instanceof MethodNotAllowedHttpException) {
                $status = 405;
            }

            // 3) Fallback segur
            if (!is_int($status) || $status < 100 || $status > 599) {
                $status = 500;
            }

            // 4) Missatge segons entorn
            $payloadMsg = config('app.debug')
                ? ($msg ?: (HttpResponse::$statusTexts[$status] ?? 'Server Error'))
                : (HttpResponse::$statusTexts[$status] ?? 'Server Error');

            return response()->json([
                'message' => $payloadMsg,
            ], $status);
        }

        // HTML: redirecció login per a auth
        if ($exception instanceof AuthenticationException) {
            return redirect()->guest('login');
        }

        return parent::render($request, $exception);
    }

    /**
     * Determina si una excepció ha d'enviar avís al responsable.
     *
     * @param \Throwable $exception
     * @param string $msg
     * @param bool $isValidation
     * @param bool $isAuthorization
     * @param bool $isForbidden
     * @param bool $isNotFound
     * @return bool
     */
    private function shouldNotify(
        Throwable $exception,
        string $msg,
        bool $isValidation,
        bool $isAuthorization,
        bool $isForbidden,
        bool $isNotFound
    ): bool {
        if ($exception instanceof IntranetException && !$exception->shouldNotify()) {
            return false;
        }

        if (
            $isValidation ||
            $isAuthorization ||
            $isForbidden ||
            $isNotFound ||
            $msg === 'The given data was invalid.' ||
            $msg === 'Unauthenticated.' ||
            $msg === '' ||
            strpos($msg, 'SRF') !== false ||
            app()->environment('testing')
        ) {
            return false;
        }

        return true;
    }

    /**
     * Genera un resum curt i estable per a la notificació interna.
     *
     * @param \Throwable $exception
     * @return string
     */
    private function buildNotificationSummary(Throwable $exception): string
    {
        $summary = class_basename($exception);
        $message = preg_replace('/\s+/', ' ', trim((string) $exception->getMessage())) ?: 'Sense missatge';
        $summary .= ': ' . $message;
        $summary .= ' [' . basename($exception->getFile()) . ':' . $exception->getLine() . ']';

        if (!app()->runningInConsole()) {
            $route = optional(request()->route())->getName();
            if ($route) {
                $summary .= ' route=' . $route;
            }
        }

        return mb_substr($summary, 0, 1000);
    }

    /**
     * Registra totes les excepcions en un canal dedicat.
     * Compacta el log per a autenticació i errors NotFound de domini.
     * Evita traça en avisos per reduir soroll en producció.
     *
     * @param \Throwable $exception
     * @return void
     */
    private function logException(Throwable $exception): void
    {
        if (app()->environment('testing')) {
            return;
        }

        if ($exception instanceof ValidationException) {
            $context = [
                'exception' => get_class($exception),
                'code' => $exception->getCode(),
                'status' => 422,
            ];

            $errors = $exception->errors();
            $firstField = $errors ? array_key_first($errors) : null;
            $context['validation'] = [
                'field' => $firstField,
                'first_error' => $firstField ? ($errors[$firstField][0] ?? null) : null,
                'field_count' => count($errors),
            ];

            if (!app()->runningInConsole()) {
                $request = request();
                $context['url'] = $request->fullUrl();
                $context['method'] = $request->method();
                $context['ip'] = $request->ip();
                $context['route'] = optional($request->route())->getName();
                $context['user_agent'] = (string) $request->header('User-Agent', '');
                $context['referer'] = (string) $request->header('Referer', '');

                $user = authUser();
                $context['user'] = $user
                    ? [
                        'id' => $user->dni ?? $user->nia ?? $user->id ?? null,
                        'rol' => $user->rol ?? null,
                        'email' => $user->email ?? null,
                    ]
                    : ['guest' => true];
            }

            Log::channel('exceptions')->info($exception->getMessage() ?: 'Validation error', $context);
            return;
        }

        $statusCode = ($exception instanceof HttpExceptionInterface)
            ? $exception->getStatusCode()
            : null;
        if ($exception instanceof TokenMismatchException) {
            $statusCode = 419;
        }
        $level = ($statusCode !== null && $statusCode < 500) ? 'warning' : 'error';
        $isUnauthenticated = $exception instanceof AuthenticationException;
        $isNotFoundDomain = $exception instanceof NotFoundDomainException;
        $isTokenMismatch = $exception instanceof TokenMismatchException;

        if ($isUnauthenticated) {
            $level = 'info';
        }
        if ($isTokenMismatch) {
            $level = 'warning';
        }

        $context = [
            'exception' => get_class($exception),
            'code' => $exception->getCode(),
            'status' => $statusCode,
        ];

        if (!$isUnauthenticated) {
            $context['file'] = $exception->getFile();
            $context['line'] = $exception->getLine();
        }

        $includeTrace = !$isUnauthenticated
            && !$isNotFoundDomain
            && ($level === 'error' || config('app.debug'));

        if ($includeTrace) {
            $context['trace'] = $exception->getTraceAsString();
        }

        if ($exception instanceof IntranetException) {
            $context['context'] = $exception->getContext();
        }

        if (!app()->runningInConsole()) {
            $request = request();
            $context['url'] = $request->fullUrl();
            $context['method'] = $request->method();
            $context['ip'] = $request->ip();
            $context['route'] = optional($request->route())->getName();
            $context['user_agent'] = (string) $request->header('User-Agent', '');
            $context['referer'] = (string) $request->header('Referer', '');

            $user = authUser();
            $context['user'] = $user
                ? [
                    'id' => $user->dni ?? $user->nia ?? $user->id ?? null,
                    'rol' => $user->rol ?? null,
                    'email' => $user->email ?? null,
                ]
                : ['guest' => true];
        }

        Log::channel('exceptions')->log($level, (string) $exception->getMessage(), $context);
    }

    /**
     * Renderitza una excepció de domini amb resposta coherent.
     *
     * @param \Illuminate\Http\Request $request
     * @param IntranetException $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function renderIntranetException($request, IntranetException $exception)
    {
        $status = $exception->getStatus();
        $message = $exception->getUserMessage();

        if ($exception instanceof NotFoundDomainException) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 404);
            }

            return response()->view('errors.404', [], 404);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => $message], $status);
        }

        Alert::danger($message);
        $back = url()->previous() ?: route('home');
        return redirect($back)->setStatusCode($status);
    }
}
