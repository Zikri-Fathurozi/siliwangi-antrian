<?php

namespace App\Http\Middleware;

use Closure;
use App\Device;

class CheckDeviceCodePermission
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle($request, Closure $next, $device_code)
  {
    // // jika langsung diakses oleh server maka boleh
    // if ($this->getIp() == "127.0.0.1") {
    //   return $next($request);
    // }

    // if ($this->getIp() == env("APP_IP")) {
    //   return $next($request);
    // }
    // // end

    // $device = Device::find($device_code);

    // if ($device->device_ip != $this->getIp()) {
    //   return abort(403, "Unauthorized action.");
    // }

    return $next($request);
  }

  public function getIp()
  {
    foreach (
      [
        "HTTP_CLIENT_IP",
        "HTTP_X_FORWARDED_FOR",
        "HTTP_X_FORWARDED",
        "HTTP_X_CLUSTER_CLIENT_IP",
        "HTTP_FORWARDED_FOR",
        "HTTP_FORWARDED",
        "REMOTE_ADDR",
      ]
      as $key
    ) {
      if (array_key_exists($key, $_SERVER) === true) {
        foreach (explode(",", $_SERVER[$key]) as $ip) {
          $ip = trim($ip); // just to be safe
          if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return $ip;
          }
        }
      }
    }
  }
}
