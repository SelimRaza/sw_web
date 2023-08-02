<?php

namespace App\Exceptions;

//use Exception;
Use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
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
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    //public function report(Exception $exception)
    public function report(Throwable $exception)
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
    //public function render($request, Exception $exception)
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ModelNotFoundException or $exception instanceof NotFoundHttpException) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Not Found'], 404);
            }
            return response()->view('theme.404', [], 404);
        }
        if ($exception instanceof TokenMismatchException) {
            return redirect(route('login'))->with('message', 'You page session expired. Please login again');
        }
        return parent::render($request, $exception);
    }
    protected function whoopsHandler()
    {
        try {
            return app(\Whoops\Handler\HandlerInterface::class);
        } catch (\Illuminate\Contracts\Container\BindingResolutionException $e) {
            return parent::whoopsHandler();
        }
    }
}
