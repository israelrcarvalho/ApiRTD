<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

//use App\Http\Requests;
use App\Http\Controllers\Controller;
use JWTAuth;
use App\User;
use JWTAuthException;

class UserController extends Controller
{   

    private $user;
    public function __construct(User $user){
        $this->user = $user;
    }
   

    /**
     * Registrar um novo usuario
     * @param Request $request
     * @return type
     */
    public function register(Request $request){

        $user = $this->user->create([
          'cpf_cnpj' => $request->cpf_cnpj,
          'nome_razaosocial' => $request->nome_razaosocial,
          'tipo_pessoa' => $request->tipo_pessoa,
          'password' => bcrypt($request->password),
          'senha' => md5($request->password)
        ]);
        
        return response()->json(['status'=>true,'messagem'=>'Usuário criado com successo','data'=>$user],200);
    }
    
   
    /**
     * Metodo utilizado para fazer o login e obter o token
     * @param Request $request
     * @return type
     */
    public function login(Request $request){
        $credentials = $request->only('cpf_cnpj', 'password');
        $token = null;
        try {
           if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['login_ou_senha_invalido'], 422);
           }
        } catch (JWTAuthException $e) {
            return response()->json(['falha_ao_criar_token'], 500);
        }
        return response()->json(compact('token'));
    }    

    /**
     * 
     * @param Request $request
     * @return type
     */
    public function getAuthUser(Request $request){
        $user = JWTAuth::toUser($request->token);
        return response()->json(['result' => $user]);
    }
    

}  