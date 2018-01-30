<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Swift_Mailer;
use Validator;
use Illuminate\Support\Facades\App;
use Illuminate\Mail\TransportManager;
use App\Http\Resources\ConfigurationResource;

class ConfigControllerAPI extends Controller
{
    //Get Platform data
        public function getPlatformData()
    {
        $config = DB::table('config')->first();
        return new ConfigurationResource($config);
    }

    //Update Platform Data
        public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'host' => 'required',
            'port' => 'required|integer|min:1',
            'encryption' => 'required'
        ]);

        if ($request->wantsJson() && !$validator->fails()) {

            try{

                $email = $request->input('email');

                
                $platform_email_properties = json_encode(array_except($request->all(), ['email']));
                //MAKES UPDATE
                DB::table('config')->where('id', 1)->update(['platform_email' => $email, 'platform_email_properties' => $platform_email_properties]);

                return response()->json(['msg' => 'Alterações guardadas com sucesso.']);

            }
            catch(\Exception $e){
                return response()->json(['msg' => 'Configuração do email não é valida. Tente de envio falhou!'], 400);
            }

        } else {
            return response()->json(['msg' => 'Request inválido.'], 400);
        }
    }
}
