/*jshint esversion: 6 */
const axios = require("axios");
var waitUntil = require('wait-until');
var domainBase = "http://blackjack.lol/";
var myDomain = "http://blackjack.lol/api/";
//var myDomain = "http://dad.oncodi.com/api/";
//var domainBase = "http://dad.oncodi.com/";

axios.defaults.headers.common['Accept'] = "application/json";

class MyConnection {
    constructor() {
        this.login();
    }

    login(){
        const data = {
            email: "admin",
            password: "secret"
        };
        console.log("Request to: "+myDomain+'login');
        axios.post(myDomain+'login', data)
            .then((response) => {
                axios.defaults.headers.common['Authorization'] = 'Bearer ' + response.data.access_token;
                console.log("Token: " +axios.defaults.headers.common['Authorization']);
            })
            .catch((error) => {
                console.log("Erro: Login: "+error.response.data.msg);
                this.login();
            });
    }
    joinGame(playerName, gameID){
        const data = {
            nickName: playerName,
            gameId: gameID
        };
        axios.post(myDomain+'joinGame', data)
            .then((response) => {
                console.log("joinGame: " + response.data.msg);
                return response.data.id;
            })
            .catch((error) => {
                console.log("Erro: joinGame url: " +myDomain+"joinGame" + " error: " +error.response.data.msg);
                return 0;
            });
    }
    startGame(gameID){
        const data = {
            gameId: gameID,
            status: 1
        };
        axios.post(myDomain+'changeStatus', data)
            .then((response) => {
                console.log("startGame:" + response.data.msg);
            })
            .catch((error) => {
                console.log("Erro: startGame url: " +myDomain+"changeStatus" + " error: "  +error.response.data.msg);
            });
    }

    cancelGame(gameID){
        const data = {
            gameId: gameID,
            status: 4
        };
        axios.post(myDomain+'changeStatus', data)
            .then((response) => {
                console.log("cancelGame:" + response.data.msg);
            })
            .catch((error) => {
                console.log("Erro: cancelGame url: " +myDomain+"changeStatus" + " error: "+error.response.data.msg);
            });
    }

    setWinner(gameID, playerName){
        const data = {
            gameId: gameID,
            nickName: playerName
        };
        axios.post(myDomain+'setWinner', data)
            .then((response) => {
                console.log("setWinner:" + response.data.msg);
            })
            .catch((error) => {
                console.log("Erro: setWinner url: " +myDomain+"setWinner" + " error: "+error.response.data.msg);
            });
    }

    /*getGamePieces(game){
        axios.get(myDomain+'game/pieces/'+((game.x*game.y)/2))
            .then((response) => {
                game.hiddenBoard = response.data.tiles;
                game.hiddenFace = response.data.hidden;
                game.mainDirectory = domainBase+response.data.directory;
                //fill board
                for (var i=0; i < game.x*game.y; i++){
                    game.board[i] = game.hiddenFace;
                }
                io.to(game.playersSocketID[0]).emit("my_active_games_changed");
            })
            .catch((error) => {
                console.log("Erro: getGamePieces url: " +myDomain+"game/pieces/"+((game.x*game.y)/2));
            });
    }*/
    
}
module.exports = MyConnection;