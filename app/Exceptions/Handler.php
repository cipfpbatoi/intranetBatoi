<?php

namespace Intranet\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Intranet\Componentes\Mensaje;
use Intranet\Services\AdviseService;
use Styde\Html\Facades\Alert;
use Throwable;


class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */

    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];


    /**
      Render an exception into an HTTP response.

      @param  \Illuminate\Http\Request  $request
      @param  \Exception  $exception
      @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        if ($exception->getMessage()!='The given data was invalid.'&&
               $exception->getMessage()!='Unauthenticated.'&&
               $exception->getMessage()!='') {
            Mensaje::send('avisos.errores',$exception->getMessage());
        }

        if ($exception instanceof ModelNotFoundException) {
            if ($request->wantsJson())
            {
                return response()->json(['message' => $exception->getMessage()], $exception->getCode());
            }
            else
            {
                $e = new NotFoundHttpException($exception->getMessage(), $exception);
            }
        }
        if ($exception instanceof \PDOException){
            
            Alert::danger("Error en la base de dades. No s'ha pogut completar l'operació degut a :".$exception->getMessage().". Si no ho entens possat en contacte amb l'administrador");
        }
        if ($exception instanceof AuthenticationException){
            return $this->unauthenticated($request,$exception);
        }
        if ($request->wantsJson())
        {
            return response()->json(['message' => $exception->getMessage()], 404);
        }
        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {

        if ($request->wantsJson()) {
            return response()->json(['error' => $exception->getMessage(),'linea'=>$exception->getFile().'->'.$exception->getLine()], 401);
        }

        return redirect()->guest('login');
    }

}
