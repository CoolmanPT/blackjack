<?php

namespace App\Http\Controllers;

use App\Http\Resources\DeckResourse;
use Illuminate\Http\Request;

use App\Deck;

class DeckControllerAPI extends Controller
{
    /**
     * Return a list Decks.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getDecks(Request $request){
        if ($request->wantsJson()){
            $decks = Deck::where('complete',1)->where('active',1)->get();

            return DeckResourse::collection($decks);
        }else{
            return response()->json(['message' => 'Request inválido.'], 400);
        }
    }
}
