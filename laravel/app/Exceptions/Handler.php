<?php

namespace App\Exceptions;

use App\Utils\StatusCode;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

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
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    public function render($request, Throwable $e): \Illuminate\Http\Response|JsonResponse|Response
    {
        $response = [
            'errors' => 'Sorry, something went wrong.'
        ];

        // Default response of 500
        $status = StatusCode::INTERNAL_SERVER_ERROR;

        if($e instanceof ValidationException){
            /** @var ValidationException $e */
            $response['errors'] = $e->errors();
            $status = StatusCode::UNPROCESSABLE_ENTITY;
        } elseif ($e instanceof ModelNotFoundException){
            /** @var ModelNotFoundException $e */
            $response['errors'] = [
                'message' => $e->getMessage(),
                'model' => $e->getModel()
            ];
            $status = StatusCode::NOT_FOUND;
        } elseif ($e instanceof AuthenticationException) {
            /** @var AuthenticationException $e */
            $response['errors'] = [
                'message' => $e->getMessage(),
            ];
            $status = StatusCode::UNAUTHORIZED;
        }

        if (config('app.debug')) {
            $response['exception'] = get_class($e);
            $response['message'] = $e->getMessage();
            $response['trace'] = $e->getTrace();
        }


        // If this exception is an instance of HttpException
        if ($this->isHttpException($e)) {
            // Grab the HTTP status code from the Exception
            /** @noinspection PhpPossiblePolymorphicInvocationInspection */
            $status = $e->getStatusCode();
        }

        // Return a JSON response with the response array and status code
        return response()->json($response, $status);
    }
}
