<?php

namespace App\Exceptions;

use App\Traits\ApiResponseTrait;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponseTrait;
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (\Exception $exception, $request) {
            if ($request->wantsJson()) { //add Accept: application/json in request
                if ($exception instanceof InvalidArgumentException) {
                    return $this->fail(message: 'Not Found', status: 404);
                }
                return $this->handleApiException($request, $exception);
            }
        });

    }

    private function handleApiException($request, \Exception $exception)
    {

        $exception = $this->prepareException($exception);

        if ($exception instanceof HttpResponseException) {
            $exception = $exception->getResponse();
        } elseif ($exception instanceof AuthenticationException) {
            $exception = $this->unauthenticated($request, $exception);
            $data = $exception->getData();
        } elseif ($exception instanceof ValidationException) {
            $exception = $this->convertValidationExceptionToResponse($exception, $request);
            $data = $exception->getData();
        }

        if (isset($data)) {
            return $this->fail($data->message, $data->errors ?? array(), $exception->status());
        }

        return $this->customApiResponse($exception);
    }

    private function customApiResponse($exception)
    {
        $data = array();
        if (method_exists($exception, 'getStatusCode')) {
            $statusCode = $exception->getStatusCode();
        } else {
            $statusCode = 500;
        }

        $statusMessage = $this->getMessageForStatus($statusCode);
        if (($statusMessage && !env('APP_DEBUG')) || (empty($exception->getMessage()) and $statusMessage)) {
            $message = $statusMessage;
        } elseif (!empty($exception->getMessage()) and env('APP_DEBUG')) {
            $message = $exception->getMessage();
        } else {
            $message = 'something is wrong';
        }

        if (config('app.debug')) {
            $data['trace'] = $exception->getTrace();
            $data['code'] = $exception->getCode();
        }

        return $this->fail(status: $statusCode, message: $message, data: $data);
    }

    private function getMessageForStatus($statusCode)
    {
        $messages = [
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            419 => 'CSRF token mismatch',
            422 => 'Unprocessable Entity',
            500 => 'Internal Server Error',
        ];

        return $messages[$statusCode] ?? null;
    }

}
