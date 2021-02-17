<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Card;
use App\Models\Collection;
use App\Models\cardCollection;

class CollectionController extends Controller
{
    public function createCollection(Request $request){
        
        $response= "";
    
        //Recibiendo informacion dada por el usuario
        $data = $request->getContent();
        
        //Decoficando el json recibido
        $data = json_decode($data);

        if($data){
             $collection = new Collection();

             $collection = DB::table('collections')
                ->where('collection_name', '=', $data->collection_name)
                ->get();


            if(!$collection){

                 //Relleno los datos de la coleccion
                $collection->collection_name = $data->collection_name; //collection_name
                $collection->image = $data->image; //image
                $collection->publish_date = $data->publish_date; //publish_date
                $collection->user_id = $data->user_id;

                $card = new Card();
                $card->card_name = $data->card_name;
                $card->description=$data->description;
                $card->collection = $collection->collection_name;
                $card->user_id = $data->user_id;

                 try{
                    $collection->save();
                    $card->save();
                    
                    $card_id=$card->id;
                    $collection_id=$collection->id;

                    $response="OK";
                } catch(\Exception $e){
                    $response=$e->getMessage();
                }

                $cardCollection = new cardCollection();
                $cardCollection->card_id = $card_id;
                $cardCollection->collection_id =$collection_id;
                $cardCollection->save();

               $response = "coleccion ha sido creada";

            }else{
                
                $response = "coleccion ya existe";
            }
            
            

        }else{
            $response = "Empty data";
        }
       //pedir nombre, imagen y fecha de edicion

       //save coleccion
        return $response;
    }


    public function updateCollection(){

    }
}
