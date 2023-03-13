<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
  public function login(Request $request)
  {
    if (
      !($request->hasHeader("X-Username") && $request->hasHeader("X-Password"))
    ) {
      return $this->responseWithError("Credentials is not valid", 403);
    }

    $credentials = [
      "email" => $request->header("X-Username"),
      "password" => $request->header("X-Password"),
    ];

    try {
      if (!($token = JWTAuth::attempt($credentials))) {
        return $this->responseWithError(
          "Username atau Password Tidak Sesuai",
          401
        );
      }
    } catch (JWTException $e) {
      return $this->responseWithError("JWT Error", 500);
    }

    return $this->responseWithToken($token);
  }

  public function loginWithGoogle(Request $request)
  {
    $validator = Validator::make($request->all(), [
      "uid" => "required",
      "name" => "required",
      "email" => "required|email:rfc,dns",
    ]);

    if ($validator->fails()) {
      return $this->responseWithError($validator->getMessageBag(), 500);
    }

    $user = User::where("password", $request->get("uid"))
      ->where("email", $request->get("email"))
      ->first();
    if (!$user) {
      User::create([
        "name" => $request->get("name"),
        "email" => $request->get("email"),
        "password" => $request->get("uid"),
        "role" => "mobile",
      ]);
    }
    return $this->responseWithToken($request->get("uid"));
  }

  private function responseWithError($message, $code)
  {
    return response()->json(
      [
        "response" => null,
        "metadata" => [
          "message" => $message,
          "code" => $code,
        ],
      ],
      401
    );
  }

  private function responseWithToken($token)
  {
    return response()->json(
      [
        "response" => [
          "token" => $token,
        ],
        "metadata" => [
          "message" => "OK",
          "code" => "200",
        ],
      ],
      200
    );
  }
}
