<?php

	namespace App\Helpers;
	use Firebase\JWT\JWT;
	use Illuminate\Suport\Facades\DB;
	use App\User;

	class JwtAuth{

		public $key;
	
		public function __construct(){
			$this->key = 'clave de api jajaja';
		}


		public function signup($email, $password, $getToken = null)
		{
			//buscar usuario con credenciales
			$user = User::where([
					'email' 	=> $email,
					'password' 	=> $password	])->first();

			//comprobar si son correctas
			$signup = false;
			if(is_object($user) && $user->condicion==1){
				$signup = true;
			}
			
			//generar token con los datos de usuario identificado
			if($signup)
			{
				$token = array(
					'sub' 		=> $user->id,
					'email' 	=> $user->email,
					'name' 		=> $user->name,
					'surname' 	=> $user->surname,
					'identificador' => $user->identificador,
					'role'		=> $user->role,
					'image'		=> $user->image,
					'description'=>$user->description,
					'iat' 		=> time(),
					'exp' 		=> time()+(130)
//					'exp' 		=> time()+(1*24*60*60)
				);
				//encripta datos  jkjkjghghjg77676t76rr^%^%^YTYT&^%&^Y
				$jwt 	 = JWT::encode($token, $this->key, 'HS256');
				//desencripta datos como objeto {"name"=>"juan","surname"=>"piguabe"}
				$decoded = JWT::decode($jwt, $this->key, ['HS256']);
	
				if(is_null($getToken) ){
					//devuelve token
					$data =  $jwt;
				}else{
					//devuelve datos del usuario logueado
					$data = $decoded;
				}
			}else{
				$data = array(
					'status' =>  'error' ,
					'message' => 'Login incorrecto');
			}
			return $data;	
		}

		////////////////////////////////////
		///PARA ACTUALIZAR USUARIO LOGUEADO
		////////////////////////////////////
		///////TRUE O FALSE SI ESTA LOGUEADO
		////////////////////////TOKEN///////
	public function checkToken($jwt, $getIdentity = false)
	{
		$auth = false;
		try{
		    $jwt = str_replace('"', '', $jwt);
			$decoded = JWT::decode($jwt, $this->key, ['HS256']);
			if(!empty($decoded) && is_object($decoded) && isset($decoded->sub) ){
				$auth = true;
			}else{
				$auth = false;
			}
				
		}catch(\UnexpectedValueException $e){
			$auth = false;
		}catch(\DomainException $e){
			$auth = false;
		}catch(\Firebase\JWT\ExpiredException $e){
         	$auth = false;

        }

        if($getIdentity){
			return $decoded;
		}

		return $auth;
	}



}
	