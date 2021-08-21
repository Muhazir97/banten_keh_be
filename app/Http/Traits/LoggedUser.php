<?php 

namespace App\Http\Traits;

use App\User;
use DB;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

trait LoggedUser
{
	  public static function get()
    {
      // $token             = $request->bearerToken();
      $headers           = getallheaders(); // pakai ini karena agar tidak menggunakan $request
      $token             = $headers['Authorization'];
      $replace           = str_replace('Bearer ', '', $token);

      $jwt               = JWTAuth::setToken($replace);
      $credentials       = JWTAuth::getPayload($jwt);
      $user              = User::find($credentials['sub']);

      $data = [
          'user'           => $user,
      ];

      return $data;
    }
}