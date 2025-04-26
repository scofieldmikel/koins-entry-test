<?php

namespace App\Exceptions;

use App\Http\Traits\HasApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Log;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Response;

class BuildResponse
{
    use HasApiResponse;

    protected Exception|\Throwable $exception;

    protected ?Request $request;

    public function __construct(Exception|\Throwable $e, ?Request $request = null)
    {
        $this->exception = $e;

        $this->request = $request;
    }

    public function handle()
    {
        $method = (new ReflectionClass($this->exception))->getShortName();

        //dd($method);
        if (method_exists($this, $handler = 'handle'.$method)) {
            return $this->{$handler}();
        }

        Log::error('Unhandled Exception '.$method.': '.$this->exception);

        $code = $this->exception->getCode();

        // Check if the code is an integer and a valid HTTP response code
        if (is_numeric($code) && http_response_code($code) !== false) {
            $responseCode = $code;
        } else {
            $responseCode = 500;
        }

        if (config('app.env') === 'production' || config('app.debug') === false) {
            $message = 'Something went wrong';
        } else {
            $message = $this->exception->getMessage();
        }

        // This would send a generic message to the client
        return $this->serverErrorResponse($message, $responseCode, $this->exception);
    }

    protected function handleValidationException(): Response
    {
        return (new ValidationResponseException($this->exception->validator, $this->request))->getResponse();
    }

    protected function handleAuthenticationException(): JsonResponse
    {
        return $this->unauthenticatedResponse($this->exception->getMessage());
    }

    protected function handleModelNotFoundException(): JsonResponse
    {
        return $this->notFoundResponse('Model cannot be found');
    }

    protected function handleHttpResponseException()
    {
        return $this->exception->getResponse();
    }

    protected function handleHttpException(): JsonResponse
    {
        return $this->jsonResponse($this->exception->getMessage(), $this->exception->getStatusCode());
    }
}
