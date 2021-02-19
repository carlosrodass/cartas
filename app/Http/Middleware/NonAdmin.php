<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use App\Http\Helpers\MyJWT;
use \Firebase\JWT\JWT;

class AuthNonAdmin
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
        define("ADMIN","Administrator");

        $key = MyJWT::getKey();

        $headers = getallheaders();

        $decoded = JWT::decode($headers['token'], $key, array('HS256'));

        if($decoded){

            if($decoded->role !== ADMIN){
                return $next($request);
            }else{
                abort(403, "Acceso permitido");
            }

        }else{
            abort(403, "¡Token vacío!");
        }
    }
}