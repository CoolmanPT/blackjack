<?php

namespace App\Http\Controllers;
use App\Http\Resources\UserResource;
use App\Mail\ActivateAccount;
use Illuminate\Http\Request;
use Validator;
use App\User;
use App\Activation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Swift_Mailer;
use Mail;
use Illuminate\Support\Facades\App;
use Illuminate\Mail\TransportManager;
use App\Mail\ChangedState;

//use App\Http\Resources\User as UserResource;
use App\Http\Resources\UserStatistics;

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

                $config = DB::table('config')->first();
                $mailConfigs = json_decode($config->platform_email_properties);

                config([
                   'mail.host' => $mailConfigs->host,
                   'mail.port' => $mailConfigs->port,
                   'mail.encryption' =>$mailConfigs->encryption,
                   'mail.username' => $config->platform_email,
                   'mail.password' => $mailConfigs->password
                ]);


                $app = App::getInstance();
                $app->singleton('swift.transport', function ($app) {
                    return new TransportManager($app);
                });
                $mailer = new Swift_Mailer($app['swift.transport']->driver());
                Mail::setSwiftMailer($mailer);

                Mail::to($user->email)->queue(new ActivateAccount($activation->token, $config->platform_email, $user->email));

                return response()->json(['msg' => 'Utilizador registado.']);
            } catch (\Exception $e) {
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

    //UPDATE EMAIL
    public function updateEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($request->wantsJson() && !$validator->fails()) {
            $checkEmailExists = User::where('id', '<>', $request->user()->id)
                ->where('email', $request->input('email'))
                ->first();

            if ($checkEmailExists) {
                return response()->json(
                    ['errorCode' => 1, 'msg' => 'Email in Use.'], 400);
            }
            $request->user()->update($request->all());
            return response()->json(['msg' => 'Email Saved.']);
        } else {
            return response()->json(['errorCode' => -1, 'msg' => 'Invalid Request.'], 400);
        }
    }

    /**
     * Update User Password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'currentPassword' => 'required',
            'newPassword' => 'required'
        ]);

        if ($request->wantsJson() && !$validator->fails()) {


            if (!Hash::check($request->input('currentPassword'), $request->user()->password)) {
                return response()->json(
                    ['errorCode' => 1, 'msg' => 'Password incorrecta.'], 400);
            }

            $request->user()->password = Hash::make($request->input('newPassword'));
            $request->user()->save();

            return response()->json(['msg' => 'Password alterada com sucesso.']);
        } else {
            return response()->json(['errorCode' => -1, 'msg' => 'Request inválido.'], 400);
        }
    }
    /**
     * Update User General Info.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'nickname' => 'required',
            'email' => 'required|email',
        ]);

        if ($request->wantsJson() && !$validator->fails()) {
            $checkEmailExists = User::where('id', '<>', $request->user()->id)
                ->where('email', $request->input('email'))->first();

            if ($checkEmailExists) {
                return response()->json(['message' => 'Email já está a ser utilizado na plataforma.','error' => 2], 400);
            }

            $checkNicknameExists = User::where('id', '<>', $request->user()->id)
                ->where('nickname', $request->input('nickname'))->first();

            if ($checkNicknameExists) {
                return response()->json(['message' => 'Username já se encontra a ser utilizado.','error' => 3], 400);
            }

            $user = User::findOrFail($request->user()->id);

            $user->name = $request->input('name');
            $user->nickname = $request->input('nickname');
            $user->email = $request->input('email');

            $user->save();

            return response()->json(['message' => 'Utilizador foi atualizado com sucesso.'], 200);
        } else {
            return response()->json(['message' => 'Request para alteração inválido.','error' => 1], 400);
        }

    }

    /**
     * Remove User Game Account.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteAccount(Request $request)
    {
        $user = User::findOrFail($request->user()->id);
        $user->delete();
        return response()->json(null, 204);
    }

    /**
     * Return a list of Users Statistics.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getUsersStatistics(Request $request){
        if ($request->wantsJson()){
            $users = User::withCount('gameWins')
                ->withCount('gameTies')
                ->withCount('gameLost')
                ->where('blocked', '0')
                ->where('admin', 0)
                ->get();

            return UserStatistics::collection($users);
        }else{
            return response()->json(['message' => 'Request inválido.'], 400);
        }
    }

    //GET USERS
    public function getUsers(Request $request)
    {

        if ($request->wantsJson()) {

            $users = User::where('admin', 0)->get();
            return UserResource::collection($users);
        } else {
            return response()->json(['msg' => 'Request inválido.'], 400);
        }

    }

    //DELETE USER
    public function delete($id, $reason)
    {

        try {

            $user = User::findOrFail($id);

            

            
            $msg = "Your account has been deleted! ";
            if ($reason != null) {
                $msg .= 'Reason:' .$reason;
            }
           $config = DB::table('config')->first();
           $mailConfigs = json_decode($config->platform_email_properties);

           config([
              'mail.host' => $mailConfigs->host,
              'mail.port' => $mailConfigs->port,
              'mail.encryption' =>$mailConfigs->encryption,
              'mail.username' => $config->platform_email,
              'mail.password' => $mailConfigs->password
           ]);


           $app = App::getInstance();
           $app->singleton('swift.transport', function ($app) {
               return new TransportManager($app);
           });
           $mailer = new Swift_Mailer($app['swift.transport']->driver());
           Mail::setSwiftMailer($mailer);


            Mail::to($user->email)->queue(new ChangedState($msg, $config->platform_email, $user->email));

            $user->delete();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            print_r($e);
            exit();
            return response()->json(['msg' => 'Problem sending email'], 400);
        }

    }

    //UPDATE STATE
    public function updateState(Request $request, $id)
    {
        if ($request->wantsJson()) {

            try {

                $user = User::findOrFail($id);

                $oldState = $request->input('state');
                
                if ($oldState == 1) {
                    $newState = 0;
                } else {
                    $newState = 1;
                }

                $reason = nl2br($request->input('reason'));
                $user->blocked = $newState;
                if ($newState == 0) {
                    $msg = "Your account have been unblocked";
                    $user->reason_reactivated = $reason;
                } else {
                    $msg = "Your account have been blocked.";
                    $user->reason_blocked = $reason;
                }

                if ($reason != null) {
                    $msg .= 'Reason: ' . $reason;
                }

                $config = DB::table('config')->first();
                $mailConfigs = json_decode($config->platform_email_properties);
                config([
                    'mail.host' => $mailConfigs->host,
                    'mail.port' => $mailConfigs->port,
                    'mail.encryption' => $mailConfigs->encryption,
                    'mail.username' => $config->platform_email,
                    'mail.password' => $mailConfigs->password
                ]);

                $app = App::getInstance();
                $app->singleton('swift.transport', function ($app) {
                    return new TransportManager($app);
                });
                $mailer = new Swift_Mailer($app['swift.transport']->driver());
                Mail::setSwiftMailer($mailer);

                Mail::to($user->email)->queue(new ChangedState($msg, $config->platform_email, $user->email));

                $user->save();
                return response()->json(['msg' => 'State changed!']);
            } catch (\Exception $e) {
                print_r($e);
                exit();
                return response()->json(['msg' => 'Problem sending email.'], 400);
            }
        } else {
            return response()->json(['msg' => 'Invalid Request.'], 400);
        }

        function getNewState($oldState) {
        if (oldState == 1) {
           return 0;
        }
        else {
        return 1;
        }
    }
    }

    
}
