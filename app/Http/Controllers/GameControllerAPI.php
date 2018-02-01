<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;

use App\Game;
use App\User;

class GameControllerAPI extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'deckId' => 'required'
        ]);

        if ($request->wantsJson() && !$validator->fails()) {

            $gameData = array('created_by' => $request->user()->id,
                            'deck_used' => $request->deckId);

            $game = Game::create($gameData);

            $game->players()->attach($request->user()->id);

            return response()->json(['id' => $game->id,'date' => $game->created_at, 'message' => 'Jogo criado com sucesso.']);
        } else {
            return response()->json(['message' => 'Request inválido.'], 400);
        }
    }

    public function joinGame(Request $request){
        $validator = Validator::make($request->all(), [
            'gameId' => 'required',
            'username' => 'required'
        ]);

        if ($request->wantsJson() && !$validator->fails()) {
            $user = User::Where('nickname', $request->username)->first();
            if(!$user){
                return response()->json(['message' => 'User not found'], 400);
            };
            $game = Game::findOrFail($request->gameId);
            $game->players()->attach($user->id);
            return response()->json(['message' => 'Jogador adicionado com sucesso.']);
        } else {
            return response()->json(['message' => 'Request inválido.'], 400);
        }
    }

    public function setWinner(Request $request){
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'gameId' => 'required',
            'playerPoints' => 'required'
        ]);

        if ($request->wantsJson() && !$validator->fails()) {
            $game = Game::findOrFail($request->gameId);
            $user = User::where('nickname', $request->username)->where('admin', 0)->first();
            if(count($user)===0){
                return response()->json(['message' => 'Utilizador não existe.'], 400);
            }
            $user->total_games_played++;
            $user->total_points += $request->playerPoints;

            $user->games()->updateExistingPivot($game->id,['game_result'=>'win']);

            $user->save();
            return response()->json(['message' => 'Vitoria gravada com sucesso.']);
        } else {
            return response()->json(['message' => 'Request inválido.'], 400);
        }
    }

    public function setTied(Request $request){
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'gameId' => 'required',
            'playerPoints' => 'required'
        ]);

        if ($request->wantsJson() && !$validator->fails()) {
            $game = Game::findOrFail($request->gameId);
            $user = User::where('nickname', $request->username)->where('admin', 0)->first();
            if(count($user)===0){
                return response()->json(['message' => 'Utilizador não existe.'], 400);
            }

            $user->total_games_played++;
            $user->total_points += $request->playerPoints;

            $user->games()->updateExistingPivot($game->id,['game_result'=>'tie']);

            $user->save();
            return response()->json(['message' => 'Empate gravado com sucesso.']);
        } else {
            return response()->json(['message' => 'Request inválido.'], 400);
        }
    }

    public function setLoser(Request $request){
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'gameId' => 'required',
            'playerPoints' => 'required'
        ]);

        if ($request->wantsJson() && !$validator->fails()) {
            $game = Game::findOrFail($request->gameId);
            $user = User::where('nickname', $request->username)->where('admin', 0)->first();
            if(count($user)===0){
                return response()->json(['message' => 'Utilizador não existe.'], 400);
            }

            $user->total_games_played++;
            $user->total_points+= $request->playerPoints;

            $user->games()->updateExistingPivot($game->id,['game_result'=>'lost']);

            $user->save();
            return response()->json(['message' => 'Derrota gravada com sucesso.']);
        } else {
            return response()->json(['message' => 'Request inválido.'], 400);
        }
    }

    public function changeStatus(Request $request){
        $validator = Validator::make($request->all(), [
            'status' => 'required',
            'gameId' => 'required'
        ]);

        if ($request->wantsJson() && !$validator->fails()) {
            $game = Game::findOrFail($request->gameId);
            switch($request->status){
                case 1:
                    $game->status = 'active';
                    break;
                case 2:
                    $game->status = 'pending';
                    break;
                case 3:
                    $game->status = 'terminated';
                    break;
                case 4:
                    $game->status = 'canceled';
                    break;
            }

            $game->save();
            return response()->json(['message' => 'Estado do jogo alterado com sucesso.']);
        } else {
            return response()->json(['message' => 'Request inválido.'], 400);
        }
    }
}
