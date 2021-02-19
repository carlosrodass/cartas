<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Card;
use App\Models\Collection;
use App\Models\cardCollection;
use \Firebase\JWT\JWT;
use App\Http\Helpers\MyJWT;

class CardController extends Controller
{
   
   /*
    *Creando cartas
    */
    public function createCard(Request $request)
    {
    
        $response= "";
        $data = $request->getContent();
        $data = json_decode($data);
        
        //Si existend datos
        if($data){

            $card = new Card();
            
            $key = MyJWT::getKey();
            $headers = getallheaders();                
            $decoded = JWT::decode($headers['token'], $key, array('HS256'));

            //Relleno los datos de la carta
            $card->card_name = $data->card_name; 
            $card->description = $data->description; 
            $card->collection = $collection->collection_name;
            $card->user_id = $decoded->id; //Id del usuario creador

            try{
                $card->save();
                $response = response()->json(['Success' => 'Carta registrada'], 200);
            } catch(\Exception $e){
                // $response=$e->getMessage();
                $response = response()->json(['Error' => $e]);
            }

            //Obteniendo la coleccion que tenga el mismo nombre dado por el usuario en el campo coleccion
            $collection = Collection::where('collection_name', $data->collection)->get()->first();
            
            //Si la coleccion existe
            if($collection){
            
                $cardCollection = new CardCollection();
                //Rellenando la tabla intermedia
                $cardCollection->card_id =$card->id; //ID de la carta creada
                $cardCollection->collection_id =$collection->id; //ID de la coleccion a la que pertenece dicha carta
                try{
                    $cardCollection->save();
                    $response = response()->json(['Success' => 'Completado']);
                }catch(\Exception $e){
                    // $response = $e->getMessage();
                    $response = response()->json(['Error' => $e]);
                }
                
            }
            //Si NO existe la coleccion
            else{
                //Creando la coleccion
                $collection= new Collection();
                $collection->collection_name = 'default';
                $collection->image = 'default';
                $collection->publish_date = 'default';
                $collection->user_id = $decoded->id;
                
                try{
                    //Guardando la coleccion recien creada
                    $collection->save();
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

        //Si no existen datos
        }else{
            
            $response = response()->json(['Error' => 'Introduce datos'], 400); 
        }

        return $response;
      
    }


    public function updateCard()
    {

    }
}
