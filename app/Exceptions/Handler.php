<?php

namespace Intranet\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Intranet\Services\Notifications\NotificationService;
use Intranet\Services\UI\AppAlert as Alert;
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

        if ($this->shouldNotify($exception, $msg, $isValidation, $isAuthorization, $isForbidden)) {
            // Pots limitar trace en prod si vols: substr($exception->getTraceAsString(), 0, 2000)
            app(NotificationService::class)->send(config('avisos.errores'), $msg . $exception->getTraceAsString());
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
     * @return bool
     */
    private function shouldNotify(
        Throwable $exception,
        string $msg,
        bool $isValidation,
        bool $isAuthorization,
        bool $isForbidden
    ): bool {
        if ($exception instanceof IntranetException && !$exception->shouldNotify()) {
            return false;
        }

        if (
            $isValidation ||
            $isAuthorization ||
            $isForbidden ||
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
     * Registra totes les excepcions en un canal dedicat.
     *
     * @param \Throwable $exception
     * @return void
     */
    private function logException(Throwable $exception): void
    {
        if (app()->environment('testing')) {
            return;
        }

        $statusCode = ($exception instanceof HttpExceptionInterface)
            ? $exception->getStatusCode()
            : null;
        $level = ($statusCode !== null && $statusCode < 500) ? 'warning' : 'error';

        $context = [
            'exception' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'code' => $exception->getCode(),
            'status' => $statusCode,
            'trace' => $exception->getTraceAsString(),
        ];

        if ($exception instanceof IntranetException) {
            $context['context'] = $exception->getContext();
        }

        if (!app()->runningInConsole()) {
            $request = request();
            $context['url'] = $request->fullUrl();
            $context['method'] = $request->method();
            $context['ip'] = $request->ip();
            $context['route'] = optional($request->route())->getName();

            $user = authUser();
            if ($user) {
                $context['user'] = [
                    'id' => $user->dni ?? $user->nia ?? $user->id ?? null,
                    'rol' => $user->rol ?? null,
                    'email' => $user->email ?? null,
                ];
            }
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

        if ($request->expectsJson()) {
            return response()->json(['message' => $message], $status);
        }

        Alert::danger($message);
        $back = url()->previous() ?: route('home');
        return redirect($back)->setStatusCode($status);
    }
}
