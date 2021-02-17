<?php

namespace App\Http\Controllers;

    use App\Models\User;
    use Illuminate\Support\Str;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Validator;
    use JWTAuth;
    use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{


	//User login
    public function authenticate(Request $request)
    { 
        $credentials = $request->only('username', 'password');
    try {
        if (! $token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'invalid_credentials'], 400);
        }
    } catch (JWTException $e) {
        return response()->json(['error' => 'could_not_create_token'], 500);
    }
    return response()->json(compact('token'));
    }



    //User profile
    public function getAuthenticatedUser()
    {
    try {
        if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
        }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
                return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
                return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
                return response()->json(['token_absent'], $e->getStatusCode());
        }

        
        return response()->json(compact('user'));
    }


    //User register
    public function register(Request $request)
    {
        $response = "";

        $data = $request->getContent();

        $data = json_decode($data);


        if($data){
            $user = new User();
            $user->username = $data->username;
            $user->email = $data->email;
            $user->password = Hash::make($data->password);
            $user->role = $data->role;

            try{
                $user->save();
                $response = "Registro completado";
            }catch(\Exception $e){
                $fail=$e->getMessage();
                $response = "Registro erroneo " . $fail;
            }

        }else{
            $response = "No has introducido datos";
        }


        return $response;
    }

    //Recuperar contraseña 
    public function resetPass(Request $request){

        $response="";

        //Recogiendo los datos escritos por el usuario
        $data = $request->getContent();
        // Decodificar el json
        $data = json_decode($data);
       

        if($data){
             //Buscando usuario por email
            $user = User::where('email', $data)->get()->first();
            //Si existe el usuario
            if($user) {
                
                $password= "";
                //Generar nueva contraseña
                $password = Str::random(15);
                //Reseteando contraseña
                $user->password = Hash::make($password);
                $user->password = $password;
                
                try{
                    //Guardando contraseña
                    $user->save();
                    $response="Nueva contraseña: ".$password;
                } catch(\Exception $e){
                    $response=$e->getMessage();
                    }

                }else{
                    $response="No se encuentra user";
                }

        }else{
            $response = "No hay datos";
        }


        return $response;

    }
}
