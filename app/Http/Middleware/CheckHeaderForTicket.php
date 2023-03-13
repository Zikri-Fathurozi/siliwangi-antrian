<?php

namespace App\Http\Middleware;

use Closure;

class CheckHeaderForTicket
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle($request, Closure $next)
  {
    // if (
    //   !$request->hasHeader("X-Service-Key") ||
    //   $request->header("X-Service-Key") != env("APP_SERVICE_KEY")
    // ) {
    //   return abort(403, "Unauthorized action.");
    // }
    return $next($request);
  }
}
