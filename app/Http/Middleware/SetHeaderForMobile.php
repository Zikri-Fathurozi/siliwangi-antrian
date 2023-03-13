<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Http\Request;

class SetHeaderForMobile
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle(Request $request, Closure $next)
  {
    if ($request->hasHeader("X-Token")) {
      $token = $request->header("X-Token");
      $user = User::where("password", $token)->first();
      if ($user) {
        $request->request->add(["email" => $user->email]);
        $request->request->add(["uid" => $token]);

        return $next($request);
      }
    }
    return abort(403, "Unauthorized action.");
  }
}
