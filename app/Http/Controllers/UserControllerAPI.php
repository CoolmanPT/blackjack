<?php

namespace App\Http\Controllers;

use App\Mail\ActivateAccount;
use Illuminate\Http\Request;
use Validator;
use App\User;
use App\Activation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserControllerAPI extends Controller
{
    //REGISTER A USER + MAIL
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'nickname' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($request->wantsJson() && !$validator->fails()) {

            try {

                $checkEmailExists = User::where('email', $request->input('email'))->first();
                if ($checkEmailExists) {
                    return response()->json(
                        ['errorCode' => 1, 'msg' => 'Email já se encontra a ser utilizado.'], 400);
                }

                $checkNicknameExists = User::where('nickname', $request->input('nickname'))->first();
                if ($checkNicknameExists) {
                    return response()->json(
                        ['errorCode' => 2, 'msg' => 'Nome utilizador já se encontra a ser utilizado.'], 400);
                }

                $userData = array('name' => $request->name, 'nickname' => $request->nickname, 'email' => $request->email, 'password' => Hash::make($request->password), 'admin' => '0', 'blocked' => '0', 'activated' => '0');
                $user = User::create($userData);

                //CREATE A ACTIVATION LINK
                $activation = new Activation();
                $activation->user_id = $user->id;
                $activation->token = str_random(64);
                $activation->save();

                Mail::to($user->email)->queue(new ActivateAccount($activation->token, $user->email));

                return response()->json(['msg' => 'Utilizador registado.']);
            } catch (\Exception $e) {
                print_r($e);
                exit();
                return response()->json(['errorCode' => -1, 'msg' => 'Problema ao enviar email. Tente novamente.'], 400);
            }
        } else {
            return response()->json(['errorCode' => -1, 'msg' => 'Request inválido.'], 400);
        }
    }

    //ACTIVATE USER
    public function activate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($request->wantsJson() && !$validator->fails()) {
            $activation = Activation::where('token', $request->token)
                ->first();

            if (empty($activation)) {
                return response()->json(['msg' => 'Token inválido.'], 400);
            }

            $user = User::find($activation->user_id);
            $user->activated = true;
            $user->save();

            $activation->delete();

            return response()->json(['msg' => 'Utilizador activado.']);
        } else {
            return response()->json(['msg' => 'Request inválido.'], 400);
        }
    }


}
