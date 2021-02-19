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
    *creando colecciones
    */
    public function createCollection(Request $request){
        
        $response= "";
        //Recibiendo informacion dada por el usuario
        $data = $request->getContent();    
        //Decoficando el json recibido
        $data = json_decode($data);

        if($data){

            $collection = new Collection();
            $key = MyJWT::getKey();
            $headers = getallheaders();
            $decoded = JWT::decode($headers['token'], $key, array('HS256'));

            $collectionDB = DB::table('collections')
                ->where('collection_name',$data->collection_name)
                ->get()->first();


            if($collectionDB){

                $response = response()->json(['Failure' => 'La coleccion con el nombre dado ya existe']);

            }else{
                
                //Relleno los datos de la coleccion
                $collection->collection_name = $data->collection_name; //collection_name
                $collection->image = $data->image; //image
                $collection->publish_date = $data->publish_date; //publish_date
                $collection->user_id = $decoded->id; //ID usuario creador

                $card = new Card();
                $card->card_name = 'default';
                $card->description='default';
                $card->collection = $collection->collection_name;
                $card->user_id = $decoded->id;

                try{
                    $collection->save();
                    $card->save();
                    $response = response()->json(['Success' => 'Carta y coleccion creadas'], 200);

                } catch(\Exception $e){
                    $response = response()->json(['Error' => $e]);
                }

                //Rellenando tabla intermedia
                $cardCollection = new cardCollection();
                $cardCollection->card_id = $card->id;
                $cardCollection->collection_id =$collection->id;

                try{
                    $cardCollection->save();
                    $response = response()->json(['Success' => 'tabla intermedia completa'], 200);

                }catch(\Exception $e){
                    $response = response()->json(['Error' => $e]);
                }
                

               $response = response()->json(['Success' => 'Coleccion creada'], 200);
            }
            
            

        }else{
            $response = response()->json(['Error' => 'No has introducido datos']);
        }

        return $response;
    }

    /*
    *editando colecciones
    */
    public function updateCollection(Request $request){

        
        $response = "";
        $data = $request->getContent();
        $data=json_decode($data);

        //Autenticando el usuario
        $key = MyJWT::getKey();
        $headers = getallheaders();                
        $decoded = JWT::decode($headers['token'], $key, array('HS256'));
        
        
        if($data){
            
            $collection = DB::table('collections')->where('collection_name', $data->collection_name)->get()->first();

            if($collection){
                
                $collection->collection_name = $data->new_collection_name;
                $collection->image = $data->new_image;
                $collection->publish_date = $data->new_publish_date;
                $collection->user_id = $decoded->id;

                try{
                    $collection->save();
                    $response = response()->json(['Success' => 'Carta renombrada'], 200);
                } catch(\Exception $e){
                    $response = response()->json(['Error' => $e->getMessage()]);
                }
            }
            else{
                $response = response()->json(['Failure' => 'La coleccion no existe'], 200);
            }

          

        }
        else{
            $response = response()->json(['Failure' => 'No hay datos'], 200);
        }

        return $response;

    }
}
