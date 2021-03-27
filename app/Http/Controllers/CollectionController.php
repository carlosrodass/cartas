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

class CollectionController extends Controller
{   
    /*
    *Creando colecciones
    */
    public function createCollection(Request $request){
        
        $response= "";
        $data = $request->getContent();    
        $data = json_decode($data);
        

        $key = MyJWT::getKey();
        $headers = getallheaders();
        $decoded = JWT::decode($headers['token'], $key, array('HS256'));

        //Si el usuario es admin
        if($decoded->role == "admin"){
            //Si existen datos
            if($data){

                $collectionDB = DB::table('collections')
                    ->where('collection_name',$data->collection_name)
                    ->get()->first();

                //Si la coleccion existe
                if($collectionDB){

                    $response = response()->json(['Failure' => 'La coleccion con el nombre dado ya existe']);
                
                }
                else{//Si la coleccion NO existe
                    
                    // creando y rellenando los datos de la coleccion
                    $collection = new Collection();
                    $collection->collection_name = $data->collection_name; //collection_name
                    $collection->image = $data->image; //image
                    $collection->publish_date = $data->publish_date; //publish_date
                    $collection->user_id = $decoded->id; //ID usuario creador

                    // creando y rellenando los datos de la carta
                    $card = new Card();
                    $card->card_name = 'default';
                    $card->description='default';
                    $card->collection = $collection->collection_name;
                    $card->user_id = $decoded->id;

                    try{
                        $collection->save(); //Guardando coleccion creada
                        $card->save();//Guardando carta creada
                        $response = response()->json(['Success' => 'Carta y coleccion creadas']);

                    } catch(\Exception $e){
                        $response = response()->json(['Error' => $e]);
                    }

                    //Creando y rellenando tabla intermedia
                    $cardCollection = new cardCollection();
                    $cardCollection->card_id = $card->id;
                    $cardCollection->collection_id =$collection->id;

                    try{
                        $cardCollection->save();
                        $response = response()->json(['Success' => 'tabla intermedia completa']);

                    }catch(\Exception $e){
                        $response = response()->json(['Error' => $e]);
                    }
                   $response = response()->json(['Success' => 'Coleccion creada']);
                }
            }else{
                $response = response()->json(['Error' => 'No has introducido datos']);
            }
        }else{
            $response = response()->json(['ADMIN' => 'No eres administrador']);
        }
        return $response;
    }

    /*
    *Editando colecciones
    */
    public function updateCollection(Request $request){

        $response = "";
        $data = $request->getContent();
        $data=json_decode($data);

        //Autenticando el usuario
        $key = MyJWT::getKey();
        $headers = getallheaders();                
        $decoded = JWT::decode($headers['token'], $key, array('HS256'));

        if($decoded->role == "admin"){

            if($data){
                $collection = Collection::find($data->id);

                if($collection){
                    $collection->collection_name = $data->new_collection_name;
                    $collection->image = $data->new_image;
                    $collection->publish_date = $data->new_publish_date;
                    $collection->user_id = $decoded->id;

                    try{
                        $collection->save();
                        $response = response()->json(['Success' => 'Coleccion renombrada']);
                    } catch(\Exception $e){
                        $response = response()->json(['Error' => $e->getMessage()]);
                    }
                }
                else{
                    $response = response()->json(['Failure' => 'La coleccion no existe']);
                }
            }
            else{
                $response = response()->json(['Failure' => 'No hay datos']);
            }  
        }else{
            $response = response()->json(['ADMIN' => 'No eres administrador']);
        }
        
        return $response;
    }
}
