<?php

namespace App\Http\Controllers;
use App\Mail\PasswordChanged;
use App\Mail\RecoverPassword;
use App\User;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use App\Mail\LoginMade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Validator;

define('YOUR_SERVER_URL', 'http://blackjack.lol');
// Check "oauth_clients" table for next 2 values:
define('CLIENT_ID', '2');
<<<<<<< HEAD
define('CLIENT_SECRET','0r3Mzy1KC43qon9gAx14Elng9wN2qSUzlMThShFK');
=======
define('CLIENT_SECRET','dvkyddgAPJUezMpkI4qyFQHWjs784IFe0kCl1iLi');
>>>>>>> 5369ead1abe12eec13b55e05873f98c77f1890ff
class LoginControllerAPI extends Controller
{
    use SendsPasswordResetEmails;

    //LOGIN METHOD + MAIL
    public function login(Request $request)
    {

        //Search user by email or nickname
        $user = User::orWhere('email', $request->email)->orWhere('nickname', $request->email)->first();
        if(!$user){
            return response()->json(['msg'=>'Utilizador/email não existe.'], 400);
        }

        if($user->activated == 0){
            return response()->json(['msg'=>'Utilizador não activo.'], 400);
        }

        if($user->blocked == 1){
            return response()->json(['msg'=>'Utilizador bloqueado.'], 400);
        }
        $http = new \GuzzleHttp\Client;
        $response = $http->post(YOUR_SERVER_URL.'/oauth/token', [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => CLIENT_ID,
                'client_secret' => CLIENT_SECRET,
                'username' => $request->email,
                'password' => $request->password,
                'scope' => ''
            ],
            'exceptions' => false,
        ]);
        $errorCode= $response->getStatusCode();

        if ($errorCode=='200') {
            //Mail::to($request->email)->queue(new LoginMade());
            return json_decode((string) $response->getBody(), true);
        } else {
            return response()->json(
                ['msg'=>'User credentials are invalid'], $errorCode);
        }
    }

    //LOGOUT METHOD
    public function logout()
    {
        \Auth::guard('api')->user()->token()->revoke();
        \Auth::guard('api')->user()->token()->delete();
        return response()->json(['msg'=>'Token revoked'], 200);
    }

    //SEND RECOVER PASSWORD MAIL
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);
        if ($request->wantsJson() && !$validator->fails()) {
            $user = User::where('email', $request->input('email'))->first();
            if (!$user) {
                return response()->json(['msg' => trans('passwords.user')], 400);
            }
            $token = $this->broker()->createToken($user);
            Mail::to($user->email)->queue(new RecoverPassword($token, $user->email));

        } else {
            return response()->json(['msg' => 'Request inválido.'], 400);
        }
    }

    //RESET PASSWORD + MAIL
    public function resetPassword(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'password' => 'required'
        ]);
        if ($request->wantsJson() && !$validator->fails()) {
            //CHECK IF USER EXISTs
            $user = User::where('email', $request->input('email'))->first();
            if (!$user) {
                return response()->json(['msg' => trans('passwords.user')], 400);
            }

            //CHECK IF TOKEN IS VALID
            $reminder = DB::table('password_resets')->where('email', $user->email)->first();
            if (!$reminder or !Hash::check($request->input('token'), $reminder->token)) {
                return response()->json(['msg' => 'Token inválido.'], 400);
            }

            //CHANGE PASSWORD
            $user->password = Hash::make($request->input('password'));
            $user->save();

            DB::table('password_resets')->where('email', $user->email)->delete();
            Mail::to($user->email)->queue(new PasswordChanged());
            return response()->json(['msg' => 'Password alterada com sucesso.'], 200);
        } else {
            return response()->json(['msg' => 'Request inválido.'], 400);
        }
    }
}
