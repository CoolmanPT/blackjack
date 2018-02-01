/*jshint esversion: 6 */
const axios = require("axios");
var waitUntil = require('wait-until');
var domainBase = "http://blackjack.lel/";
var myDomain = "http://blackjack.lel/api/";
//var myDomain = "http://dad.oncodi.com/api/";
//var domainBase = "http://dad.oncodi.com/";

axios.defaults.headers.common['Accept'] = "application/json";

class MyConnection {
    constructor() {
        this.login();
    }

    login(){
        console.log("Request to: "+myDomain+'login');
        axios.post(myDomain+'login', {email: "admin@mail.dad", password: "secret"})
            .then(response => {
                axios.defaults.headers.common['Authorization'] = 'Bearer ' + response.data.access_token;
                console.log("Token: " +axios.defaults.headers.common['Authorization']);
            })
            .catch(error => {
                console.log("Erro: Login: "+error.response.data.msg);
                this.login();
            });
    }
    joinGame(playerUsername, gameID){
        axios.post(myDomain+'joingame', {username: playerUsername,gameId: gameID})
            .then(response => {
                console.log("joinGame: " + response.data.message);
                return response.data.id;
            })
            .catch(error => {
                console.log("Erro: joinGame url: " +myDomain+"joinGame" + " error: " +error.response.data.message);
                return 0;
            });
    }
    startGame(gameID){
        axios.post(myDomain+'changestatus', {gameId: gameID,status: 1})
            .then(response => {
                console.log("startGame:" + response.data.message);
            })
            .catch(error => {
                console.log("Erro: startGame url: " +myDomain+"changeStatus" + " error: "  +error.response.data.message);
            });
    }

    cancelGame(gameID){
        axios.post(myDomain+'changestatus', {gameId: gameID,status: 4})
            .then(response => {
                console.log("cancelGame:" + response.data.message);
            })
            .catch(error => {
                console.log("Erro: cancelGame url: " +myDomain+"changeStatus" + " error: "+error.response.data.message);
            });
    }

    setWin(gameID, playerUsername, playerPoints){
        axios.post(myDomain+'setwinner', {gameId: gameID,username: playerUsername, playerPoints: playerPoints})
            .then(response => {
                console.log("setWinner:" + response.data.message);
            })
            .catch(error => {
                console.log("Erro: setWinner url: " +myDomain+"setWinner" + " error: "+error.response.data.message);
            });
    }

    setTie(gameID, playerUsername, playerPoints){
        axios.post(myDomain+'settied', {gameId: gameID,username: playerUsername, playerPoints: playerPoints})
            .then(response => {
                console.log("setTied:" + response.data.message);
            })
            .catch(error => {
                console.log("Erro: setTied url: " +myDomain+"setTied" + " error: "+error.response.data.message);
            });
    }

    setLost(gameID, playerUsername, playerPoints){
        axios.post(myDomain+'setloser', {gameId: gameID, username: playerUsername, playerPoints: playerPoints})
            .then(response => {
                console.log("setLoser:" + response.data.message);
            })
            .catch(error => {
                console.log("Erro: setLooser url: " +myDomain+"setLooser" + " error: "+error.response.data.message);
            });
    }

    gameTerminate(gameID){
        axios.post(myDomain+'changestatus', {gameId: gameID,status: 3})
            .then(response => {
                console.log("gameTerminate:" + response.data.message);
            })
            .catch(error => {
                console.log("Erro: gameTerminate url: " +myDomain+"changeStatus" + " error: "+error.response.data.message);
            });
    }
}
module.exports = MyConnection;