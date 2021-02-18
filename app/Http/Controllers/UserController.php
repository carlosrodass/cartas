<?php

namespace App\Http\Controllers;

    use App\Models\User;
    use Illuminate\Support\Str;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Validator;
    use JWTAuth;
    use \Firebase\JWT\JWT;
    use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
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
    public function signUp(Request $request)
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

            //Encodificando los datos del usuario para enviarlos
            // $jwt = JWT::encode($payload, $key);

            try{
                $user->save();
                $response = "Usuario registrado";
                // $response = array('token'->$jwt);

            }catch(\Exception $e){
                $fail=$e->getMessage();
                $response = "Registro erroneo " . $fail;
            }

        }else{
            $response = "No has introducido datos";
        }


        return $response;
    }


    	//User login
    public function SignIn(Request $request)
    { 
        $response = "";
        $data = $request->getContent();
        
        $data = json_decode($data);

        //Si hay datos
        if($data){
            //Comprobando que no exista un usuario con el mismo nombre
            $userEmail = $data->email;
            //Buscando si existe email en la base de datos
            $user = User::where('email', $userEmail)->get()->first();
            //Si el usuario existe
            if($user){
                
                //Comprobando la contraseña del usuario sea igual que la introducida
                if(Hash::check($data->password,$user->password)){

                    $key = "dfhdjfdshhfuhs894uu48r5et/*/9+6+fdsfshiushkudsh2y2838urfdsjkcj";
            
                    $payload = array(
                        // "username" => $request->username,
                        "email" => $request->email,
                        "password" => $request->password,
                        // "role" => $request->role,      
                    );
                    $jwt = JWT::encode($payload, $key);

                    try{
                        $user->save(); 
                        $response = response()->json(['token' => $jwt], 200);  

                    } catch(\Exception $e){
                        $response=$e->getMessage();
                    }
                }else{
                    $response = response()->json(['error' => 'Credenciales incorrectas'], 400);
                }
                  
            //Si el usuario NO existe
            }else{
                $response = response()->json(['error' => 'Usuario no existe']);

            }


        //Si NO hay datos
        }else{
            // $response = "No hay datos introducidos";
            $response = response()->json(['error' => 'Introduce los datos']);
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
   // $credentials = $request->only('username', 'password');
        // try {
        //     if (! $token = JWTAuth::attempt($credentials)) {
        //         return response()->json(['error' => 'invalid_credentials'], 400);
        //     }
        // } catch (JWTException $e) {
        //     return response()->json(['error' => 'could_not_create_token'], 500);
        // }
        // return response()->json(compact('token'));