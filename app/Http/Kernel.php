<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
  /**
   * The application's global HTTP middleware stack.
   *
   * These middleware are run during every request to your application.
   *
   * @var array
   */
  protected $middleware = [
    \Fruitcake\Cors\HandleCors::class,
    \App\Http\Middleware\CheckForMaintenanceMode::class,
    \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
    \App\Http\Middleware\TrimStrings::class,
    \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    \App\Http\Middleware\TrustProxies::class,
  ];

  /**
   * The application's route middleware groups.
   *
   * @var array
   */
  protected $middlewareGroups = [
    "web" => [
      \App\Http\Middleware\EncryptCookies::class,
      \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
      \Illuminate\Session\Middleware\StartSession::class,
      // \Illuminate\Session\Middleware\AuthenticateSession::class,
      \Illuminate\View\Middleware\ShareErrorsFromSession::class,
      \App\Http\Middleware\VerifyCsrfToken::class,
      \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ],

    "api" => [
      "throttle:60,1", 
      "bindings",
      \Fruitcake\Cors\HandleCors::class,
    ],

    "channel" => [
      "throttle:60,1",
      "bindings",
      \App\Http\Middleware\SetHeaderForChannel::class,
      \Tymon\JWTAuth\Http\Middleware\Authenticate::class,
    ],

    "mobile" => [
      "throttle:60,1",
      "bindings",
      \App\Http\Middleware\SetHeaderForMobile::class,
    ],

    "migrate" => [
      "throttle:60,1",
      "bindings",
      \App\Http\Middleware\SetHeaderForMigrate::class,
      \Tymon\JWTAuth\Http\Middleware\Authenticate::class,
    ],

    "role" => [\App\Http\Middleware\EnsureUserHasRole::class],
  ];

  /**
   * The application's route middleware.
   *
   * These middleware may be assigned to groups or used individually.
   *
   * @var array
   */
  protected $routeMiddleware = [
    "auth" => \App\Http\Middleware\Authenticate::class,
    "auth.basic" =>
      \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
    "bindings" => \Illuminate\Routing\Middleware\SubstituteBindings::class,
    "cache.headers" => \Illuminate\Http\Middleware\SetCacheHeaders::class,
    "can" => \Illuminate\Auth\Middleware\Authorize::class,
    "guest" => \App\Http\Middleware\RedirectIfAuthenticated::class,
    "signed" => \Illuminate\Routing\Middleware\ValidateSignature::class,
    "throttle" => \Illuminate\Routing\Middleware\ThrottleRequests::class,
    "verified" => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
    "role" => \App\Http\Middleware\EnsureUserHasRole::class,
    "ticket" => \App\Http\Middleware\CheckHeaderForTicket::class,
    "device.code" => \App\Http\Middleware\CheckDeviceCodePermission::class,
    "device.group" => \App\Http\Middleware\CheckDeviceGroupPermission::class,
  ];

  /**
   * The priority-sorted list of middleware.
   *
   * This forces non-global middleware to always be in the given order.
   *
   * @var array
   */
  protected $middlewarePriority = [
    \Illuminate\Session\Middleware\StartSession::class,
    \Illuminate\View\Middleware\ShareErrorsFromSession::class,
    \App\Http\Middleware\Authenticate::class,
    \Illuminate\Session\Middleware\AuthenticateSession::class,
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
    \Illuminate\Auth\Middleware\Authorize::class,
  ];
}
