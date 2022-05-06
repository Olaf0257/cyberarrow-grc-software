<?php

namespace App\Exceptions;

use Throwable;
use ErrorException;
use Inertia\Inertia;
use Illuminate\Support\Arr;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use App\Http\Middleware\PreventRequestsDuringMaintenance;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Foundation\Http\Exceptions\MaintenanceModeException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as MiddlewarePreventRequestsDuringMaintenance;

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

    public function handleException( Throwable $exception){
        // MAIL SENDING EXCEPTIONS

        
        if ($exception instanceof \Swift_TransportException) {
            if (request()->ajax() && !request()->hasHeader('x-inertia')) {
                return response()->json(['exception' => 'Failed to process request. Please check SMTP authentication connection.']);
            } else {
                return redirect()->back()->with('exception', 'Failed to process request. Please check SMTP authentication connection.');
            }
        }

        if ($exception instanceof \Illuminate\Session\TokenMismatchException) {
            if (request()->ajax()) {
                return response()->json(['exception' => 'Sorry, your Page has been expired. Please try again']);
            } else {
                return redirect()->back()->with(['exception' => 'Sorry, your Page has been expired. Please try again']);
            }
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            if (request()->ajax()) {
                return response()->json(['exception' => $exception->getMessage()]);
            } else {
                return redirect()->back()->with(['exception' => $exception->getMessage()]);
            }
        }

        if ($exception instanceof AuthenticationException) {
            if (request()->expectsJson()) {
                return response()->json(['exception' => 'Unauthenticated.'], 401);
            }

            $guard = Arr::get($exception->guards(), 0);
            
            

            switch ($guard) {
                case 'admin':
                    $login = 'login';
                    break;

                default:
                    $login = 'login';
                    break;
            }

            request()->session()->flash('session_expired', 'Your session has expired. Please try again later');

            return redirect()->guest(route($login));
        }

        if($exception instanceof \Illuminate\Http\Exceptions\PostTooLargeException){
            if (request()->ajax() && !request()->hasHeader('x-inertia')) {
                return response()->json(['exception' => "The Upload Max Filesize is ".ini_get("upload_max_filesize")."B on the server. Please increase Upload Max Filesize limit on the server." ], 413 );
            } else {
                return redirect()->back()->with(['exception' => "The Upload Max Filesize is ".ini_get("upload_max_filesize")."B on the server. Please increase Upload Max Filesize limit on the server." ]);
            }
        }


        if($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException){
            if (request()->ajax()) {
                return response()->json(['exception' => 'Record not found.' ]);
            } else {
                return redirect()->back()->with(['exception' => 'Record not found.' ]);
            }
        }

        // FILE NOT FOUNT EXCEPTION HANDLING
        if ($exception instanceof \Illuminate\Contracts\Filesystem\FileNotFoundException) {
            if (request()->ajax()) {
                return response()->json(['exception' => 'File not found.']);
            } else {
                return redirect()->back()->with(['exception' => 'File not found.']);
            }
        }
    }

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e){

        });

        $this->renderable(function (Throwable $e) {
            return $this->handleException($e);
        });
    }

    /**
     * Prepare exception for rendering.
     *
     * @param  \Throwable  $e
     * @return \Throwable
     */
    public function render($request, Throwable $e)
    {
        $response = parent::render($request, $e);

        if(env('APP_DEBUG') === true && !$e instanceof ValidationException){
            return $response;
        }
        if(!$e instanceof ValidationException && !$e instanceof AuthenticationException){
            if (in_array($e->getStatusCode(), [500, 503, 404, 403])) {
                return Inertia::render('errors/ErrorPage', ['status' => $e->getStatusCode(),'prev_url'=>url()->previous()])
                    ->toResponse($request)
                    ->setStatusCode($e->getStatusCode());
            } else if ($e->getStatusCode() === 419) {
                return back()->with([
                    'message' => 'The page expired, please try again.',
                ]);
            }
        }
        

        return $response;
    }
}
