<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\models\User;
use function GuzzleHttp\Psr7\str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    //

    public function  login(Request $request){
        $phone    = $request->input("phone");
        $password = $request->input("password");

        $response = [];

        if($phone=="" || $password==""){
            $response["response"] = false;
            $response["error_code"] = 1;
            $response["message"] = "username and password can't be empty";

        }else{

            $user  = User::where("phone",$phone)->first();

            if(empty($user)){

                $response["response"] = false;
                $response["error_code"] = 2;
                $response["message"] = "phone and password wrong";
            }else{

                if(password_verify($password,$user->password)){

                    if($user->status=="active"){
                        $orders  = Order::where("user_id",$user->id)->count();
                        $response["response"] = true;
                        $response["error_code"] = 0;
                        $response["message"] = "login success";
                        $response["user"] = $user;
                        $response["orders"] = $orders;
                    }else{
                        $response["response"] = false;
                        $response["error_code"] = 3;
                        $response["message"] = "this user has been blocked ";

                    }



                }else{
                    $response["response"] = false;
                    $response["error_code"] = 2;
                   // $response["password"] =password_hash("12345678",1);
                    $response["message"] = "phone and password wrong";

                }






            }

        }




        return \response()->json($response);
    }

    public function admins_list(Request $request){
	$user_id = $request->input("user_id");

	$admins = User::where([["id","!=",$user_id],["isAdmin","yes"]])->get()->toArray();



	$response["response"] = true;
        $response["error_code"] = 0;
        $response["message"] = "success";
	$response["data"] = $admins;



        return \response()->json($response);
    }


    public function  register(Request $request){
        $username = $request->input("username");
        $phone = $request->input("phone");
        $password = $request->input("password");

        $response = [];

        if($username=="" || $password=="" || $phone=="" || strlen($phone)<=6 || strlen($password)<6){
            $response["response"] = false;
            $response["error_code"] = 1;
            $response["message"] = "تاكد من البيانات المدخلة";

        }else{

            $user  = User::where("phone",$phone)->first();

            if(empty($user)){

                $user_accout = new User();
                $user_accout->username = $username;
                $user_accout->password = password_hash($password,PASSWORD_DEFAULT);
                $user_accout->phone = $phone;
                $user_accout->save();


                $response["response"] = true;
                $response["error_code"] = 0;
                $response["message"] = "success";
                $response["user_id"] = $user_accout->id;
                $response["orders"] = 0;

            }else{

                $response["response"] = false;
                $response["error_code"] = 2;
                $response["message"] = "this phone is already used";




            }

        }




        return \response()->json($response);
    }




}
