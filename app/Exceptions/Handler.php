<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
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
  protected $dontFlash = ["password", "password_confirmation"];

  /**
   * Report or log an exception.
   *
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
    $previous = $exception->getPrevious();
    if (isset($previous)) {
      switch (get_class($exception->getPrevious())) {
        case TokenExpiredException::class:
          return response()->json(
            [
              "metadata" => [
                "message" => "Token has expired",
                "code" => 201,
              ],
            ],
            $exception->getStatusCode()
          );
        case TokenInvalidException::class:
        case TokenBlacklistedException::class:
          return response()->json(
            [
              "metadata" => [
                "message" => "Token is invalid",
                "code" => 201,
              ],
            ],
            $exception->getStatusCode()
          );
        default:
          break;
      }
    }

    return parent::render($request, $exception);
  }
}
