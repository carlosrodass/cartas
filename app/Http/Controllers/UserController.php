<?php

namespace App\Http\Controllers;

    use App\Models\User;
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
            $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string'
        ]);

        if($validator->fails()){
                return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create([
            'username' => $request->get('username'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'role' => $request->get('role')
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user','token'),201);
    }

    //Recuperar contraseña 
    public function resetPass(Request $request){

        $response="";

        //Recogiendo los datos escritos por el usuario
        $data = $request->get('email');
        // Decodificar el json
        // $data = json_decode($data);
        //Buscando usuario por email

        if($data){
            $user = User::where('email', $data-> email)->get()->first();
                 //Si existe el usuario
            if($user) {
                
                $password= "";
                //Generar nueva contraseña
                $password= Str::random(15);
                //Reseteando contraseña
                // $user->password = Hash::make($password);
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


    // public function createCard(){

    // 	$response = "empty";

    // 	//-->si el usuario tiene rol de admin
    // 	//crear carta

    // 	//Validacion de token de usuario
    // 	try {

    //     if (!$user = JWTAuth::parseToken()->authenticate()) {
    //             return response()->json(['user_not_found'], 404);
    //     }
    //     } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
    //             return response()->json(['token_expired'], $e->getStatusCode());
    //     } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
    //             return response()->json(['token_invalid'], $e->getStatusCode());
    //     } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
    //             return response()->json(['token_absent'], $e->getStatusCode());
    //     }


    // 	$role = DB::table('users')
    //     ->get('role');
    	

    // 	return $response;


    // 	//--> si el usuario no tiene rol de admin
    // 	//No puedes crear una carta
    // }



}
