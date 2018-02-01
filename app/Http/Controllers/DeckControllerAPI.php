<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Deck;
use App\Http\Resources\DeckResource;
use Illuminate\Support\Facades\DB;
use Validator;
use Intervention\Image\Facades\Image;

class DeckControllerAPI extends Controller
{
        //GET USERS
    public function getDecks(Request $request)
    {
        if ($request->wantsJson()) {

            $decks = Deck::all();
            return DeckResource::collection($decks);
        } else {
            return response()->json(['msg' => 'Invalid Request.'], 400);
        }
    }

        public function delete($id)
    {

        try {

            $deck = Deck::findOrFail($id);

            $deck->delete();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            print_r($e);
            exit();
            return response()->json(['msg' => 'Problem sending email'], 400);
        }

    }

      public function update(Request $request)
    {
    	$id = $request->id;
    	$name = $request->deckName;
        $image = $request->image;
    	$exploded = explode(',', $image);

        $decoded = base64_decode($exploded[1]);



        if (str_contains($exploded[0], 'jpeg')) {
            $extension = 'jpg';

        } else {
            $extension = 'png';
        }

        $filename = str_random().'.'.$extension;

        $path = public_path().'/img/'.$filename;

        file_put_contents($path, $decoded); //sas

        


	DB::table('decks')->where('id', $id)->update(['name' => $name, 'hidden_face_image_path' => 'img/'.$filename]);
	return response()->json("Success", 200);
        
    }

        
    public function store(Request $request)
    {
        $exploded = explode(',', $request->image);

        $decoded = base64_decode($exploded[1]);

        if (str_contains($exploded[0], 'jpeg')) {
            $extension = 'jpg';

        } else {
            $extension = 'png';
        }

        $filename = str_random().'.'.$extension;

        $path = public_path().'/img/'.$filename;

        file_put_contents($path, $decoded);

        $deck = Deck::create($request->except('image') + ['name' => $request->name, 'hidden_face_image_path' => 'img/'.$filename]);
        

    }
}
