/*jshint esversion: 6 */

var app = require('http').createServer();

// CORS TRIALS
// var app = require('http').createServer(function(req,res){
// 	// Set CORS headers
// 	res.setHeader('Access-Control-Allow-Origin', 'http://dad.p6.dev');
// 	res.setHeader('Access-Control-Request-Method', '*');
// 	res.setHeader('Access-Control-Allow-Methods', 'OPTIONS, GET');
// 	res.setHeader('Access-Control-Allow-Credentials', true);
// 	res.setHeader('Access-Control-Allow-Headers', req.header.origin);
// 	if ( req.method === 'OPTIONS' ) {
// 		res.writeHead(200);
// 		res.end();
// 		return;
// 	}
// });

global.io = require('socket.io')(app);
var MyConnection = require('./connection.js');
global.conn = new MyConnection();
var MyGame = require('./gamemodel.js');
var GameList = require('./gamelist.js');

app.listen(8080, function(){
	console.log('listening on *:8080');
});

// ------------------------
// Estrutura dados - server
// ------------------------

let games = new GameList();

io.on('connection', function (socket) {
    console.log('client has connected');

    socket.on('disconnect', function() {
        console.log('Got disconnect!');
        let g = games.removePlayer(socket.id);
        for(var i=0;  i < g.length; i++){
            io.to(g[i].gameID).emit('my_active_games_changed');
        }
        io.emit('lobby_changed');
    });

    socket.on('create_game', function (data){
    	console.log('A new game is about to be created');
    	let game = games.createGame(data.playerName, socket.id, data.nPlayers, data.gameID);
    	// Use socket channels/rooms
		socket.join(game.gameID);
		// Notification to the client that created the game
		socket.emit('my_active_games_changed');
		// Notification to all clients
		io.emit('lobby_changed');
    });
    // ....
	socket.on('get_my_activegames', function () {
		var my_games = games.getConnectedGamesOf(socket.id);
		socket.emit('my_activegames', my_games);
    });
	socket.on('get_my_lobbygames', function () {
		var my_games = games.getLobbyGamesOf(socket.id);
		socket.emit('my_lobbygames', my_games);
    });

	socket.on('join_game', function (data) {
        let game = games.joinGame(data.gameID, data.playerName, socket.id);
        socket.join(game.gameID);
        io.to(game.gameID).emit('my_active_games_changed');
        io.emit('lobby_changed');
    });

	socket.on('close_game', function (data) {
        let g = games.removeGamePlayer(socket.id, data.gameID);
        console.log("para: "+data.gameID);
        console.log("Teste: "+g);
        for(var i=0;  i < g.length; i++){
            console.log("Returnados:" + g[i].gameID);
            io.to(g[i].gameID).emit('my_active_games_changed');
        }
        io.emit('lobby_changed');
    });

    socket.on('force_start', function (data) {
        let game = games.gameByID(data.gameID);
	    game.forceStart();
        io.to(game.gameID).emit('game_changed', game)
    });

    socket.on('play', function (data){
        let game = games.gameByID(data.gameID);
        if(game == null){
            console.log("NUll------------------------: "+data.gameID);
            return;
        }

        var numPlayer=0;
        for(var i=0;  i < game.playersSocketID.length; i++){
            if (game.playersSocketID[i] == socket.id) {
                numPlayer = i+1;
            }
        }
        if(game.play(numPlayer, data.index)){
            io.to(game.gameID).emit('game_changed', game)
        }
        if(game.returnPieces){
            var tocheckIfIsSame=game.pieceAux;
            setTimeout(function () {
                for(var i=0; i < tocheckIfIsSame.length; i++){
                    if(game.pieceAux[i] != tocheckIfIsSame[i])
                        return;
                }
                if(game.returnPieces){
                    game.board[game.pieceAux[0]]=game.hiddenFace;
                    game.board[game.pieceAux[1]]=game.hiddenFace;
                    game.pieceAux=[];
                    game.returnPieces=false;
                    io.to(game.gameID).emit('game_changed', game)
                }
            }, 1000);
        }
    });

});
