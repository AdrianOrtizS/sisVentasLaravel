<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\User;

class NotificationController extends Controller
{
    public function get(Request $request){
//    	return Notification::all();
  
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        $token = $params_array;
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);  //TRUE O FALSE

        if($checkToken) 
        {
          $userLog = $jwtAuth->checkToken($token, true);
          $user = User::findOrFail($userLog->sub);

          $unreadNotifications  = $user->unreadNotifications;
          $fechaActual = date('Y-m-d');
          foreach ($unreadNotifications as $notification) {
            
              if($fechaActual != $notification->created_at->toDateString())
              {
                $notification->markAsRead();
              }

          }


          return response()
                        ->json( $user->unreadNotifications );
    	  }

    }

    	
}
