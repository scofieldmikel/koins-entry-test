<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Sentry\Laravel\Integration;
use Sentry\State\Scope;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

use function Sentry\configureScope;

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
        'current_password',
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
        if (request()->expectsJson()) {
            $this->renderable(function (Exception $e, $request) {
                return (new BuildResponse($e, $request))->handle();
            });
        }

        $this->reportable(function (Throwable $e) {
            Integration::captureUnhandledException($e);
            if (app()->bound('sentry') && $this->shouldReport($e)) {
                if ($user = request()->user()) {
                    configureScope(function (Scope $scope) use ($user): void {
                        $scope->setUser(['email' => $user->email]);
                    });
                }
                app('sentry')->captureException($e);
            }
        });
    }

    /**
     * Prepare exception for rendering.
     *
     *
     * @throws Throwable
     */
    public function render($request, Throwable $e): \Illuminate\Http\Response|JsonResponse|Response|RedirectResponse
    {
        $response = parent::render($request, $e);

        if (request()->expectsJson()) {
            return (new BuildResponse($e, $request))->handle();
        }

        if (! app()->environment(['local', 'testing', 'production', 'staging']) && in_array($response->status(), [500, 503, 404, 403])) {
            return Inertia::render('Error', ['status' => $response->status()])
                ->toResponse($request)
                ->setStatusCode($response->status());
        } elseif ($response->status() === 419) {
            return back()->with([
                'message' => 'The page expired, please try again.',
            ]);
        }

        return $response;
    }
}
