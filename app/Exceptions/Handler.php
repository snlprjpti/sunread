<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Str;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Modules\Core\Traits\ApiResponseFormat;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
     use ApiResponseFormat;
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
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
     *
     * @throws \Exception
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
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function render($request, Exception $exception)
    {
        //development phase debug:
        if(app()->environment(['local','development'])) {
            dd("Try to handle early : reached handler", $exception);
        }

        if ($exception instanceof ValidationException) {
            $errors = $exception->errors();
            return $this->errorResponse($errors ,422);
        }

        if($exception instanceof MethodNotAllowedHttpException){
            return $this->errorResponse($request->method()." method is not allowed" , 422);
        }

        if ($exception instanceof  NotFoundHttpException ){
            return $this->errorResponse("No such route found.", 404);
        }

        if($exception instanceof ModelNotFoundException) {
            $model = Str::kebab( class_basename($exception->getModel()));
            $message = $model ? str_replace('-', ' ', $model) . "not found" : $exception->getMessage();
            return $this->errorResponse($message , 404);
        }

        if ($exception instanceof TokenMismatchException) {
            return $this->errorResponse('Token mismatch exception' , 400);
        }

        if ($exception instanceof UnauthorizedException){
            $message = 'You are not authorized to access this resource';
            return $this->errorResponse($message, 401);
        }

        if(env('APP_DEBUG', true)) {
            return parent::render($request, $exception);
        }

        return parent::render($request, $exception);
    }
}
