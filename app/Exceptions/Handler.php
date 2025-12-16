<?php

namespace Intranet\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Intranet\Componentes\Mensaje;
use Styde\Html\Facades\Alert;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use function config;

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

    public function render($request, Throwable $exception)
    {
        // Envia avís només si el missatge és “informatiu” i no és SRF
        $msg = (string) $exception->getMessage();
        $isValidation = $exception instanceof ValidationException;

        if (
            !$isValidation &&
            $msg !== 'The given data was invalid.' &&
            $msg !== 'Unauthenticated.' &&
            $msg !== '' &&
            strpos($msg, 'SRF') === false   // <-- correcció important
        ) {
            // Pots limitar trace en prod si vols: substr($exception->getTraceAsString(), 0, 2000)
            Mensaje::send(config('avisos.errores'), $msg . $exception->getTraceAsString());
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
}
