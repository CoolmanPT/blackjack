<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Resources\User as UserResource;
use App\Http\Resources\TotalGamesPerDate;

use App\User;
use App\Game;


class StatisticControllerAPI extends Controller
{
    public function getPublicStatistics()
    {
        $totalPlayers = User::player()->count();
        $totalGames = Game::terminated()->count();

        return response()->json(['totalPlayers' => $totalPlayers,'totalGames' => $totalGames]);
    }

    public function getTop5Wins()
    {
        $users = User::withCount('gameWins')
            ->where('blocked', '0')
            ->where('admin', 0)
            ->orderBy('game_wins_count', 'desc')
            ->take(5)->get();

        return UserResource::collection($users);
    }


    public function getTop5Score()
    {
        $users = User::where('blocked',  '0')
            ->where('admin', 0)
            ->orderBy('total_points', 'desc')
            ->take(5)->get();

        return UserResource::collection($users);
    }


    public function getTop5AVG()
    {
        $users = User::where('blocked',  '0')
            ->where('admin', 0)
            ->orderByRaw('(total_points / total_games_played) desc')
            ->take(5)->get();

        return UserResource::collection($users);
    }

    /**
     * Return User Specific Statistics.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getUserStatistics(Request $request){

        $user = User::withCount('gameWins')
            ->withCount('gameTies')
            ->withCount('gameLost')
            ->where('id', $request->user()->id)
            ->first();

        return response()->json(
            ['gamesPlayed' => $user->total_games_played,
                'gamesWon' => $user->game_wins_count,
                'gamesTied' => $user->game_ties_count,
                'gamesLost' => $user->game_lost_count,
                'score' => $user->total_points,
                'average' => $user->total_points === 0 ? 0 : round($user->total_points/$user->total_games_played,2)]
        );
    }

    /**
     * Return User Specific Statistics.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getTotalGamesPerDate(Request $request)
    {
        if ($request->wantsJson()) {
            $query = Game::select(DB::raw('DATE(created_at) as date, count(*) as games_count'))
                ->terminated()
                ->groupBy('date')
                ->get();

            if(count($query) != 0){
                return response()->json(['statList' => TotalGamesPerDate::collection($query)],200);
            } else {
                return response()->json(['message' => 'Ainda não foram criados jogos na Plataforma.','code' => 2], 400);
            }
        } else {
            return response()->json(['message' => 'Request de Estatisticas por dia inválido.','code' => 1], 400);
        }

    }

}
