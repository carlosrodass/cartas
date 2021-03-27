<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\Card;
use App\Models\Collection;
use App\Models\cardCollection;

use \Firebase\JWT\JWT;
use App\Http\Helpers\MyJWT;

class CardController extends Controller
{
   
   /** 
    *Creando cartas
    */
    public function createCard(Request $request)
    {
        $response= "";
        $data=$request->getContent();
        $data=json_decode($data);

        //Autenticando el usuario
        $key = MyJWT::getKey();
        $headers = getallheaders();                
        $decoded = JWT::decode($headers['token'], $key, array('HS256'));

        //Si el usuario es admin
        if($decoded->role == "admin"){
            
            //Si existen datos 
            if($data){
                //Obteniendo la coleccion que tenga el mismo nombre dado por el usuario en el campo coleccion
                $collection = DB::table('collections')
                ->where('collection_name', $data->collection_name)
                ->get()->first();

                //Si la coleccion existe
                if($collection){
                    //creando y rellenando los datos de la carta
                    $card = new Card();
                    $card->card_name = $data->card_name; 
                    $card->description = $data->description; 

                    $card->collection = $collection->collection_name;
                    $card->user_id = $decoded->id; //Id del usuario creador

                    try{
                        $card->save(); //Guardando la carta recien creada
                        $response = response()->json(['Success' => 'Carta registrada'], 200);
                    } catch(\Exception $e){
                        // $response=$e->getMessage();
                        $response = response()->json(['Error' => $e->getMessage()]);
                    }

                    //Rellenando la tabla intermedia
                    $cardCollection = new CardCollection();
                    $cardCollection->card_id=$card->id; //ID de la carta creada
                    $cardCollection->collection_id=$collection->id; //ID de la coleccion a la que pertenece dicha carta
                    try{
                        $cardCollection->save();
                        $response = response()->json(['Success' => 'Completado']);
                    }catch(\Exception $e){
                            
                        $response = response()->json(['Error' => $e]);
                    }

                }else{//Si NO existe la coleccion
                     //Creando y rellenando la coleccion
                    $collection = new Collection();
                    $collection->collection_name = 'default';
                    $collection->image = 'default';
                    $collection->publish_date = 'default';
                    $collection->user_id = $decoded->id;

                    //Creando y rellenando la carta
                    $card = new Card();
                    $card->card_name = $data->card_name; 
                    $card->description = $data->description; 
                    $card->collection = $collection->collection_name;
                    $card->user_id = $decoded->id; //Id del usuario creador
                        
                    try{
                        //Guardando la coleccion recien creada
                        $collection->save();
                        $card->save();
                        $response = response()->json(['Success' => 'Coleccion creada'], 200); 

                    } catch(\Exception $e){
                        $response = response()->json(['Error' => $e]);
                    }

                    $cardCollection = new cardCollection();
                    $cardCollection->card_id = $card->id;
                    $cardCollection->collection_id = $collection->id;

                    try{
                        //Guardando la coleccion recien creada
                        $cardCollection->save();
                        $response = response()->json(['Success' => 'tabla intermedia creada'], 200); 

                    }catch(\Exception $e){
                        $response = response()->json(['Error' => $e]);
                    }     
                }
            }else{//Si NO existen datos
                $response = response()->json(['Error' => 'Introduce datos'], 200); 
            }
        
        }else{//Si no es ADMIN
            $response = response()->json(['Admin' => 'No eres administrador'], 200); 
        }

        return $response;
    }

    /**
     * Editando cartas
     */
    public function updateCard(Request $request)
    {
        $response = "";
        $data = $request->getContent();
        $data=json_decode($data);

        //Autenticando el usuario
        $key = MyJWT::getKey();
        $headers = getallheaders();                
        $decoded = JWT::decode($headers['token'], $key, array('HS256'));


        if($decoded->role == "admin"){
            
            if($data){                
                $cardDB = Card::find($data->id);

                if($cardDB){
                    $cardDB->card_name = $data->card_name;
                    $cardDB->description = $data->description;
                    $cardDB->collection = $cardDB->collection;
                    $cardDB->user_id = $decoded->id;

                    try{
                        $cardDB->save();
                        $response = response()->json(['Success' => 'Carta renombrada'], 200);
                    }catch(\Exception $e){
                        $response = response()->json(['Error' => $e->getMessage()]);
                    }
                }else{
                    $response = response()->json(['Error' => 'Carta no encontrada']);
                }
            }else{
                $response = response()->json(['Failure' => 'No hay datos introduce']);
            }
        }else{
            $response = response()->json(['ADMIN' => 'No eres administrador']);
        }
        return $response;
    }

    /**
     * Mostrando cartas
     */
    public function allCard(){
        $response = [];

        //Autenticando el usuario
        $key = MyJWT::getKey();
        $headers = getallheaders();                
        $decoded = JWT::decode($headers['token'], $key, array('HS256'));

        $user = DB::table('users')
        ->where('id', $decoded->id)
        ->get();

        if($user){

            $cards =  DB::table('cards')
            ->get();
    
            for ($i=0 ;$i < sizeof($cards); $i++){
                
                $response[$i] = [
                    'card_name' => $cards[$i]-> card_name,
                    'description' => $cards[$i]-> description,
                    'collection' => $cards[$i]-> collection 
                ];

            }
        }
        else{
            $response = response()->json(['Error' => 'No te has logueado']);
        }
        return $response;
    }

    /**
     * Mostrando cartas a la venta por nombre
     */
    public function findCardByName($name){

        $key = MyJWT::getKey();
        $headers = getallheaders();
        $decoded = JWT::decode($headers['token'], $key, array('HS256'));

        if($decoded->id){

            if($name){

                $card = DB::table('cards')
                ->where('card_name', $name)
                ->get();

                // $sellingsCards = DB::table('sellings')
                // ->where('card_id',$card->id)
                // ->get()->first();

                if($card){
                    
                    for($i = 0; $i< sizeof($card); $i++){

                        $response[$i] = [
                            'card_name' => $card[$i]-> card_name,
                            'description' => $card[$i]-> description,
                            'collection' => $card[$i]-> collection 
                        ];
                    }
                }else{
                    $response = response()->json(['Error' => 'No existe tal carta a la venta']);   
                }
            }else{
                $response = response()->json(['Error' => 'No hay datos']);    
            }
        }else{
            $response = response()->json(['Error' => 'No estas logueado']);
        }

        return $response;
    }
}
