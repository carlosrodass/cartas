<?php

namespace App\Http\Controllers;

    use App\Models\User;

    use App\Http\Helpers\MyJWT;
    use JWTAuth;
    use \Firebase\JWT\JWT;
    use Tymon\JWTAuth\Exceptions\JWTException;

    use Illuminate\Http\Request;
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{

    /**
    *Registro de usuario
    */
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

            try{
                $user->save();
                $response = response()->json(['Success' => 'Usuario registrado']);

            }catch(\Exception $e){
                $fail=$e->getMessage();
                $response = response()->json(['Error' => $fail]);
            }

        }else{
            $response = response()->json(['Empty' => 'No has introducido datos']);
        }


        return $response;
    }


    /**
    *Login de usuario
    */
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

                    $payload = MyJWT::generatePayload($user);
                    $key = MyJWT::getKey();
                    //Encodificando los datos del usuario para enviarlos
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

    /**
    *Recuperar contraseña de usuario
    */
    public function resetPass(Request $request){

        $response="";

        //Recogiendo los datos escritos por el usuario
        $data = $request->getContent();
        // Decodificar el json
        $data = json_decode($data);


        if($data){
             //Buscando usuario por email
            $user = User::where('email', $data->email)->get()->first();
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
                    $response = response()->json(['New password' => $password]);
                } catch(\Exception $e){
                    $response=$e->getMessage();
                    }

                }else{
                    $response = response()->json(['Error' => 'Usuario no encontrado']);
                }

        }else{
            $response = response()->json(['Error' => 'No hay datos']);
        }


        return $response;

    }
}
