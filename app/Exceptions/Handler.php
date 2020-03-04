<?php

namespace Intranet\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Styde\Html\Facades\Alert;

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
     Report or log an exception.

     * @param  \Exception  $exception
     * @return void
     */

    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        
        if ($exception->getMessage()!='The given data was invalid.'&&
               $exception->getMessage()!='Unauthenticated.'&&
               $exception->getMessage()!='')
            avisa(config('contacto.avisos.errores'),$exception->getMessage());
        if ($exception instanceof ModelNotFoundException) {
            $e = new NotFoundHttpException($exception->getMessage(), $exception);
            //return response()->view('errors.404',[],404);
        }
        if ($exception instanceof \PDOException){
            
            Alert::danger("Error en la base de dades. No s'ha pogut completar l'operació degut a :".$exception->getMessage().". Si no ho entens possat en contacte amb l'administrador");
            //return response()->view('errors.200',['mensaje'=>$exception->getMessage()],200);
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
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest('login');
    }

}
