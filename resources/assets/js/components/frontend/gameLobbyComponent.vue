<template>
    <div>
        <div class="container">
            <ul class="nav nav-tabs nav-fill mt-5" id="my" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="list-tab" data-toggle="tab" href="#listtab" role="tab" aria-controls="listtab" aria-selected="true">Game Lobby</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="my-tab" data-toggle="tab" href="#mytab" role="tab" aria-controls="mytab" aria-selected="false">My Games</a>
                </li>
            </ul>
            <div class="tab-content border border-top-0 pt-3" id="myContent">
                <div class="tab-pane fade" id="mytab" role="tabpanel" aria-labelledby="my-tab">
                    <h3>New Game:</h3>
                    <button class="btn btn-xs btn-success" v-on:click.prevent="createGame">Create a New Game</button>
                    <div class="alert alert-info" v-if="message!=''">
                        <strong>{{ message }}</strong>
                    </div>
                    <hr>
                    <div v-if="activeGames.length == 0">
                        <h3 class="text-center">No joined games... yet!</h3>
                    </div>
                    <template v-for="game in activeGames">
                        <blackjack ref="gameRef" :game="game"></blackjack>
                    </template>
                </div>
                <div class="tab-pane fade show active" id="listtab" role="tabpanel" aria-labelledby="list-tab">
                    <h4>Pending games: </h4>
                    <list-games :gameJoinSuccess="joinGameSuccess" :games="lobbyGames" @join-click="joinGame"></list-games>
                    <div v-if="lobbyGames.length == 0">
                        <h3 class="text-center">Waiting for games to be created...</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script type="text/javascript">
    import ListGames from './listGamesComponent.vue';
    import BlackJack from './blackjackGameComponent.vue';

    export default {
        data: function(){
            return {
                currentPlayer: '',
                lobbyGames: [],
                activeGames: [],
                socketId: "",
                nPlayers: 1,
                newPlayer: "",
                message:'',
                deck: {},
                joinGameSuccess: true,
            }
        },
        sockets:{
            connect: function (){
                console.log('socket connected');
                this.socketId = this.$socket.id;
            },
            disconnect: function (){
                console.log('socket disconnected');
                this.socketId = "";
            },
            lobby_changed: function (){
                this.loadLobby();
            },
            my_active_games_changed: function (){
                this.loadActiveGames();
                this.joinGameSuccess = true;
            },
            my_activegames: function (games){
                this.activeGames = games;
            },
            my_lobbygames: function (games){
                this.lobbyGames = games;
            },
            game_started_or_ended: function (game){
                console.log(game);
                for (var lobbyGame of this.lobbyGames){
                    if(game.gameID == lobbyGame.gameID){
                        Object.assign(lobbyGame, game);
                        break;
                    }
                }
                for(var activeGame of this.activeGames){
                    if(game.gameID == activeGame.gameID){
                        Object.assign(activeGame, game);
                        break;
                    }
                }
                console.log('game started or ended');
            },
            game_changed: function (game){
                for(var activeGame of this.activeGames){
                    if(game.gameID == activeGame.gameID){
                        Object.assign(activeGame, game);
                        break;
                    }
                }
            },
            join_game_failed: function(){
                this.joinGameFailed();
            },
            play_game_round1: function(game){
                this.play(game);
            },
            start_game_round2: function(game){
                for (var children of this.$children){
                    console.log(children.id+' ,,,, '+game.gameID);
                    if(children.id==game.gameID){
                        children.startNextRound();
                    }
                }
            }
        },
        methods: {
            getDeck: function(){
                axios.post('/api/deck')
                    .then(response => {
                        var i, decks = response.data.data;
                        //if(decks.length == 0){
                            // para testes sem decks na BD
                            this.deck = {id: 1, name:'default', hiddenFaceImagePath: ''};
                            //return;
                        //}else{
                        //    i = Math.floor(Math.random() * decks.length--);
                        //    this.deck = decks[i];
                        //}
                    });
            },
            getPlayer: function () {
                axios.get('/api/user')
                    .then(response => {
                        this.currentPlayer = response.data.nickname;
                    });
            },
            loadLobby: function (){
                this.$socket.emit('get_my_lobbygames');
            },
            loadActiveGames: function (){
                this.$socket.emit('get_my_activegames');
            },
            createGame: function (){       
                if (this.currentPlayer == "") {
                    this.message = "Cannot create a game because logged user name is null.";
                    return;
                } else {
                    axios.post('/api/creategame',{deckId: this.deck.id})
                        .then(response => {
                            console.log("createGame:" + response.data.message + "Gameid: " + response.data.id);
                            this.$socket.emit('create_game', {playerName: this.currentPlayer,
                                                            gameID: response.data.id,
                                                            gameDate: response.data.date,
                                                            gameDeck: this.deck.name});
                        })
                        .catch(error => {
                            this.message="Erro ao criar jogo! " + error.response.data.message;
                        });
                }
            },
            joinGame: function (game){
                for (var player of game.players){
                    if(player == this.currentPlayer){
                        this.message="You can't join a game where you are already playing!";
                        setTimeout(this.cleanMessages, 5000);
                        return;
                    }
                }
                this.$socket.emit('join_game', {gameID: game.gameID, playerName: this.currentPlayer});
            },
            cleanMessages: function (){
                this.message='';
            },
            play: function (game){
                this.$socket.emit('play', {gameID: game.gameID});
            },
            startGame: function (game){
                this.$socket.emit('start_game', {gameID: game.gameID});
            },
            finish: function(game){
                this.$socket.emit('finish_game', {gameID: game.gameID});
            },
            close: function (game){
                if(game.players.length == 1){
                    for(var i=0; i < this.activeGames.length; i++){
                        if(game.gameID == this.activeGames[i].gameID){
                            this.activeGames.splice(i, 1);
                            break;
                        }
                    }
                }
                this.$socket.emit('close_game', {gameID: game.gameID});
            },
            remove: function(game){
                this.$socket.emit('remove_game', {gameID: game.gameID});
            },
            standGame: function(game){
                this.$socket.emit('stand_game',{gameID: game.gameID});
            },
            startGameRound2: function(game){
                this.$socket.emit('start_game_round2',{gameID: game.gameID});
            },
            startGameRound3: function(game){
                this.$socket.emit('start_game_round3',{gameID: game.gameID});
            },
            joinGameFailed: function(){
                this.message="Failed to join the game.";
                this.joinGameSuccess = false;
                setTimeout(this.cleanMessages, 5000);
                this.loadLobby();
            },
        },
        components: {
            'list-games': ListGames,
            'blackjack': BlackJack
        },
        mounted: function () {
            this.loadLobby();
            this.getPlayer();
            this.getDeck();
            this.loadActiveGames();
        },
        beforeMount: function (){
            this.loadLobby();
            this.loadActiveGames();
        }

    }
</script>

