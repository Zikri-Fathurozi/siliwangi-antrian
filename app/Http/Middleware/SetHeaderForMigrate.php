<?php

namespace App\Http\Middleware;

use Closure;

class SetHeaderForMigrate
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
    if ($request->hasHeader("X-Token-Migrate")) {
      $request->headers->set(
        "Authorization",
        "Bearer " . $request->header("X-Token-Migrate")
      );
    }
    return $next($request);
  }
}
