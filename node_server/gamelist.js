/*jshint esversion: 6 */
var Mygame = require('./gamemodel.js');

class GameList {
    constructor() {
        this.games = new Map();
    }

    gameByID(gameID) {
        let game = this.games.get(gameID);
        return game;
    }

    createGame(playerName, socketID, gameID, gameDate, deck) {
        console.log("New game: "+gameID+" created by "+ playerName);
        var game = new Mygame(gameID, playerName, gameDate, deck);
        game.playersSocketID[0] = socketID;
        game.playersPoints[0]=0;
        game.hands[0] = {player: socketID, cards: [], drawAllowed: true};
        //game.roundPlayers+=1;
        this.games.set(game.gameID, game);
        return game;
    }

    joinGame(gameID, playerName, socketID) {
        let game = this.gameByID(gameID);
        if (game===null) {
            return null;
        }
        game.join(playerName);
        conn.joinGame(playerName, gameID);
        game.playersSocketID[game.playersSocketID.length] = socketID;
        game.hands[game.hands.length] = {player: socketID, cards: [], drawAllowed: true};
        //game.roundPlayers+=1;
        return game;
    }

    removePlayer(socketID){
        let games = [];
        for (var [key, value] of this.games) {
            for(var i=0;  i < value.playersSocketID.length; i++){
                if(value.playersSocketID == socketID){
                    if(value.playersSocketID.length == 1){
                        console.log("Game Deleted");
                        this.games.delete(value.gameID);
                        if(!value.gameEnded){
                            conn.setLost(value.gameID, value.players[i],0);
                        }
                    }else{//se nao for o unico no jogo
                        //adiciona nos jogos para returnar
                        games.push(value);
                        //remove dos players
                        value.players.splice(i, 1);
                        // remove das maos
                        value.hands.splice(i, 1);
                        //remover os meus pontos
                        value.playersPoints.splice(i, 1);
                        // remover dos sockets
                        value.playersSocketID.splice(i, 1);
                        // remove o jogador da ronda
                        if(value.roundPlayers>0){
                            value.roundPlayers--;
                        }
                    }
                    //ve se ficou um e é definido como loser
                    if(!value.gameEnded && value.playersSocketID.length === 1){
                        value.gameEnded=true;
                        conn.cancelGame(value.gameID);
                    }else{
                        conn.setLost(value.gameID, value.players[i],0);
                    }
                }
            }
        }
        return games;
    }

    removeGamePlayer(socketID, gameID){
        let games = [];
        for (var [key, value] of this.games) {
            for(var i=0;  i < value.playersSocketID.length; i++){
                //caso esteja no jogo e seja este que quero fechar
                if(value.playersSocketID[i] == socketID && value.gameID == gameID){
                    //caso seja o unico
                    if(value.playersSocketID.length == 1){
                        console.log("Game Deleted");
                        this.games.delete(value.gameID);
                        if(!value.gameEnded){
                            conn.setLost(value.gameID, value.players[i],0);
                        }
                    }else{//se nao for o unico no jogo
                        // adiciona nos jogos para returnar menos a mim
                        games.push(value);
                        // remove dos players
                        value.players.splice(i, 1);
                        // remove das maos
                        value.hands.splice(i, 1);
                        //remover os meus pontos
                        value.playersPoints.splice(i, 1);
                        // remover dos sockets
                        value.playersSocketID.splice(i, 1);
                        // remove o jogador da ronda
                        if(value.roundPlayers>0){
                            value.roundPlayers--;
                        }
                    }
                    //ve se ficou um e é definido como loser
                    if(!value.gameEnded && value.playersSocketID.length === 1){
                        value.gameEnded=true;
                        conn.cancelGame(value.gameID);
                    }else{
                        conn.setLost(value.gameID, value.players[i],0);
                    }
                }
            }
        }
        return games;
    }

    removeGame(socketID, gameID){
        for (var [key, value] of this.games) {
            for(var i=0;  i < value.playersSocketID.length; i++){
                if(value.playersSocketID[i] == socketID && value.gameID == gameID){
                    if(value.playersSocketID.length == 1){
                        console.log("Game Deleted");
                        this.games.delete(value.gameID);
                    }else{
                        value.players.splice(i, 1);
                        value.hands.splice(i, 1);
                        value.playersPoints.splice(i, 1);
                        value.playersSocketID.splice(i, 1);
                        if(value.roundPlayers>0){
                            value.roundPlayers--;
                        }
                    }
                }
            }
        }
    }

    getConnectedGamesOf(socketID) {
        let games = [];
        for (var [key, value] of this.games) {
            for(var i=0;  i < value.playersSocketID.length; i++){
                if (value.playersSocketID[i] == socketID) {
                    games.push(value);
                }
            }
        }
        return games;
    }

    getLobbyGamesOf(socketID) {
        let games = [];
        for (var [key, value] of this.games) {
            if ((!value.gameStarted) && (!value.gameEnded))  {
                if (!value.playersSocketID.includes(socketID)) {
                    games.push(value);
                }
            }
        }
        return games;
    }
}

module.exports = GameList;
