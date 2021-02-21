<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sell;

use \Firebase\JWT\JWT;
use App\Http\Helpers\MyJWT;

class SellController extends Controller
{
    public function sellCard(Request $request){

		$response = "";
		$data = $request->getContent();
		$data = json_decode($data);

		$key = MyJWT::getKey();
		$headers = getallheaders();
		$decoded = JWT::decode($headers['token'], $key, array('HS256'));
        
        //si el usuario esta logueado
        if($decoded->id){

            if($data){

                $sell = new Sell();
                $sell->price = $data->price;
                $sell->quantity = $data->quantity;

                $sell->card_id = $data->card_id;
                $sell->user_id = $decoded->id;    

                try{
                    $sell->save();
                    $response = response()->json(['Success' => 'Carta/s en venta']);
                }catch(\Exception $e){
                    $response = response()->json(['Error' => $e]);
                }
            }
            else{
                $response = response()->json(['Failure' => 'No hay datos']);
            }
        }
        else{
            $response = response()->json(['Login' => 'No estas logueado']);
        }
            

		return response($response);

	}
}
