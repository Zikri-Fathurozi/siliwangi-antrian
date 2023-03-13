<?php

namespace App\Http\Middleware;

use Closure;

class EnsureUserHasRole
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */

  public function handle($request, Closure $next, $role)
  {
    if ($request->user()->role == "superadmin") {
      return $next($request);
    }

    if ($request->user()->role != $role) {
      if ($request->hasHeader("Authorization")) {
        return response()->json(
          [
            "metadata" => [
              "message" => "Unauthorized",
              "code" => 201,
            ],
          ],
          403
        );
      } else {
        return abort(403, "Unauthorized action.");
      }
    }

    return $next($request);
  }
}
