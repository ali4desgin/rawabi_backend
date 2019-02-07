<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\RoomChat;
use App\models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ChatController extends Controller
{
    //

    public function lsit(Request $request){
        $order_id  = $request->input("order_id");
        $order = Order::find($order_id);
        $chats = RoomChat::where("order_id",$order_id)->orderBy("id","asc")->get()->toArray();
	$phone = "00966550388766";
	if($order->admin_id>=1){
		$user = User::find($order->admin_id);
		if(!empty($user)){
			$phone =  $user->phone;
		}else{
			$phone = "00966550388766";
		}
	}else{
		$phone = "00966550388766";
	}



        $response["response"] = true;
        $response["error_code"] = 0;
        $response["message"] = "success";
        $response["count"] = count($chats);
        $response["data"] = $chats;
        $response["whatsapp"] = $phone;
        return \response()->json($response);
    }



    public function add_message(Request $request){

	$is_Admin = $request->input("isAdmin");
        $order_id  = $request->input("order_id");
        if($request->input("isImage")=="no"){
            $request->validate([
                "message"=>"required",
                "order_id"=>"required"
            ]);
            $chat_room = new RoomChat();
            $chat_room->order_id = $order_id;
            $chat_room->is_image = "no";
            $chat_room->message = $request->input("message");
            $chat_room->isAdmin = $is_Admin;
            $chat_room->save();

            $response["response"] = true;
            $response["error_code"] = 0;
            $response["message"] = "saved";
        }else{


            $image=$request->input("image");
             $decoded=base64_decode($image);
                    
            $img_name=uniqid("chat_").'.jpg';
            $img = 'app/public/chats/images/'.$img_name;
            file_put_contents($img,$decoded);

            $chat_room = new RoomChat();
            $chat_room->order_id = $order_id;
            $chat_room->is_image = "yes";
            $chat_room->image = $img_name;
            $chat_room->isAdmin = "no";
            $chat_room->save();
            $response["response"] = true;
            $response["error_code"] = 0;
            $response["message"] = "saved";

            

        }




        return \response()->json($response);
    }
}

