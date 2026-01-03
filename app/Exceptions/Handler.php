<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use Log;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exception.
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
            // Log all exceptions for debugging
            Log::channel('daily')->error('Exception caught', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => auth()->id(),
                'url' => request()->url(),
            ]);
        });

        // Handle ModelNotFoundException
        $this->renderable(function (ModelNotFoundException $e, Request $request) {
            return response()->view('errors.404', [
                'message' => 'Data tidak ditemukan.',
                'type' => 'Model Not Found'
            ], 404);
        });

        // Handle NotFoundHttpException (Route not found)
        $this->renderable(function (NotFoundHttpException $e, Request $request) {
            return response()->view('errors.404', [
                'message' => 'Halaman tidak ditemukan.',
                'type' => 'Page Not Found'
            ], 404);
        });

        // Handle AuthorizationException
        $this->renderable(function (AuthorizationException $e, Request $request) {
            return response()->view('errors.403', [
                'message' => 'Anda tidak memiliki akses ke resource ini.',
                'type' => 'Unauthorized Access'
            ], 403);
        });

        // Handle AuthenticationException
        $this->renderable(function (AuthenticationException $e, Request $request) {
            return response()->view('errors.401', [
                'message' => 'Silakan login terlebih dahulu.',
                'type' => 'Unauthenticated'
            ], 401);
        });

        // Handle ValidationException
        $this->renderable(function (ValidationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $e->validator->errors()
                ], 422);
            }
            
            return back()->withErrors($e->validator)->withInput();
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        // Handle generic server errors
        if ($this->shouldReport($e) && !($e instanceof ValidationException)) {
            // Log detailed error information
            $this->logException($e, $request);
        }

        return parent::render($request, $e);
    }

    /**
     * Log exception details for debugging
     */
    private function logException(Throwable $e, Request $request): void
    {
        Log::channel('daily')->error('Application Exception', [
            'exception' => get_class($e),
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'user_id' => auth()->id(),
            'user_name' => auth()->user()?->name ?? 'Guest',
            'method' => $request->method(),
            'url' => $request->url(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'input' => $this->sanitizeInput($request->all()),
        ]);
    }

    /**
     * Sanitize input for logging (remove sensitive data)
     */
    private function sanitizeInput(array $input): array
    {
        $sensitive = ['password', 'token', 'secret', 'api_key', 'credit_card'];
        
        foreach ($sensitive as $key) {
            if (isset($input[$key])) {
                $input[$key] = '***REDACTED***';
            }
        }
        
        return $input;
    }
}
