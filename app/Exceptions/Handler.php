<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
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
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function render($request, Exception $e)
    {   

        //Manual abort calls
        if( $e instanceof HttpException ){
            $error = array( "message" => $e->getMessage(), "code" => $e->getStatusCode() );
            return response()->json( $error )->setStatusCode( $e->getStatusCode() );
        }


        //Display validation exceptions
        if( $e instanceof ValidationException ){
            $response = $e->getResponse();
            $message_data = json_decode( json_encode( $response->getData() ), true );

            if( $message_data ){
                $fields = array();
                foreach( $message_data as $key => $value )
                {
                    $fields['message'] = $value[0];
                    $fields['code'] = 400;
                }

                return response()->json( $fields )->setStatusCode( 400 );
            }
       }

        if ($this->shouldReport($e) && (env("APP_ENV") == "production" || env("APP_ENV") == "staging")) {
            return response()->json(
                       $this->getJsonMessage($e), 
                       $this->getExceptionHTTPStatusCode($e)
                        );
        }

        return parent::render($request, $e);
    }

    protected function getJsonMessage($e){
        return [
            'status' => 'false',
            'message' => $e->getMessage()
        ];
    }

    protected function getExceptionHTTPStatusCode($e){
        return method_exists($e, 'getStatusCode') ? 
                        $e->getStatusCode() : 500;
    }
}
