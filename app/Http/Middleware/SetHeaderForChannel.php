<?php

namespace App\Http\Middleware;

use Closure;

class SetHeaderForChannel
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
    if ($request->hasHeader("X-Token")) {
      $request->headers->set(
        "Authorization",
        "Bearer " . $request->header("X-Token")
      );
    }
    return $next($request);
  }
}
