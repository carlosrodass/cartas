<?php

namespace App\Http\Helpers;
use \Firebase\JWT\JWT;

class MyJWT{

    private const KEY = 'ksahdisbfoudsbsdwdwf56556·$%··%&&/&/&gfggfjfkjggfjrbhiuvnsdvkndv55695';
    

    public static function generatePayload($user){
        $payload = array(
            'username' => $user->username,
            'email' => $user->email,
            'password' => $user->password,
            'role' => $user->role,
        
        );

        return $payload;
    }

    public static function getKey(){
        return self::KEY;
    }

  
}