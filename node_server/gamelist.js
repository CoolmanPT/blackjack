/*jshint esversion: 6 */
var Mygame = require('./gamemodel.js');

class GameList {
    constructor() {
        //this.contadorID = 0;
        this.games = new Map();
    }

    gameByID(gameID) {
        let game = this.games.get(gameID);
        return game;
    }

    createGame(playerName, socketID, nPlayers, gameID) {
        console.log("New game: "+gameID+" created by "+ playerName);
        var game = new Mygame(gameID, playerName, nPlayers);
        game.playersSocketID[0] = socketID;
        game.playersPlaces[0] = socketID;
        if(nPlayers == 1){
            game.gameStarted = true;
            //conn.startGame(game.gameID);
        }
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
        game.playersPlaces[game.playersPlaces.length] = socketID;
        return game;
    }

    //remove all of player
    removePlayer(socketID){
        let games = [];
        for (var [key, value] of this.games) {
            for(var i=0;  i < value.playersSocketID.length; i++){
                if(value.playersSocketID == socketID){
                    if(value.playersSocketID.length == 1){
                        console.log("Game Deleted");
                        this.games.delete(value.gameID);
                        if(!value.gameEnded){
                            conn.cancelGame(value.gameID);
                            value.gameEnded = true;
                        }
                    }else{//se nao for o unico no jogo
                        //adiciona nos jogos para returnar
                        games.push(value);
                        //remove dos players
                        value.players.splice(i, 1);
                        //remover os meus pontos
                        value.playersPoints.splice(i, 1);
                        // remover dos sockets
                        value.playersSocketID.splice(i, 1);
                        //retirar dos podio
                        for(var ii= 0;  ii < value.playersPlaces.length; ii++){
                            if(value.playersPlaces[ii] == socketID){
                                value.playersPlaces.splice(ii, 1);
                                continue;
                            }
                        }
                        //se fui eu a sair
                        if(i+1 == value.playerTurn){
                            console.log("Eu sai");
                            if(value.playerTurn > value.players.length){
                                console.log("volta inicio");
                                value.playerTurn=1;
                            }
                            //se foi antes do jogador atual
                        }else if(i+1 < value.playerTurn){
                            console.log("Saio antes do atual");
                            value.playerTurn--;
                            // quem saio era depois de mim
                        }else if(i+1 > value.playerTurn){
                            console.log("Saio depois do atual, nao se faz nada");
                        }
                    }
                }
            }
        }
        return games;
    }

    //remove game from player
    removeGamePlayer(socketID, gameID){

        let games = [];
        for (var [key, value] of this.games) {
            for(var i=0;  i < value.playersSocketID.length; i++){
                if(value.playersSocketID[i] == socketID && value.gameID == gameID){ //caso esteja no jogo e seja este que quero fechar
                    if(value.playersSocketID.length == 1){ //caso seja o unico
                        console.log("Game Deleted");
                        this.games.delete(value.gameID);
                        if(!value.gameEnded){
                            conn.cancelGame(value.gameID);
                            value.gameEnded = true;
                        }
                    }else{//se nao for o unico no jogo
                        // //adiciona nos jogos para returnar menos a mim
                        games.push(value);
                        //remove dos players
                        value.players.splice(i, 1);
                        //remover os meus pontos
                        value.playersPoints.splice(i, 1);
                        // remover dos sockets
                        value.playersSocketID.splice(i, 1);
                        //retirar dos podio
                        for(var ii= 0;  ii < value.playersPlaces.length; ii++){
                            if(value.playersPlaces[ii] == socketID){
                                value.playersPlaces.splice(ii, 1);
                                continue;
                            }
                        }
                        //se fui eu a sair
                        if(i+1 == value.playerTurn){
                            console.log("Eu sai");
                            if(value.playerTurn > value.players.length){
                                console.log("volta inicio");
                                value.playerTurn=1;
                            }
                            //se foi antes do jogador atual
                        }else if(i+1 < value.playerTurn){
                            console.log("Saio antes do atual");
                            value.playerTurn--;
                            // quem saio era depois de mim
                        }else if(i+1 > value.playerTurn){
                            console.log("Saio depois do atual, nao se faz nada");
                        }
                    }
                    // como removeu ve se so ficou um e é definido como winner
                    if(!value.gameEnded && value.playersSocketID.length == 1){
                        value.gameEnded=true;
                        conn.setWinner(value.gameID, value.players[0]);
                    }
                }
            }
        }
        return games;
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
