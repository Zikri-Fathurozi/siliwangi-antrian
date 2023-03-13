<?php

namespace App\Http\Middleware;

use Closure;
use App\Device;

class CheckDeviceGroupPermission
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle($request, Closure $next, $device_group)
  {
    // // jika langsung diakses oleh server maka boleh
    // if ($this->getIp() == "127.0.0.1") {
    //   return $next($request);
    // }
    // if ($this->getIp() == env("APP_IP")) {
    //   return $next($request);
    // }
    // // end

    // $devices = Device::where("device_group", $device_group)
    //   ->pluck("device_ip")
    //   ->toArray();
    // if (!in_array($this->getIp(), $devices)) {
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
