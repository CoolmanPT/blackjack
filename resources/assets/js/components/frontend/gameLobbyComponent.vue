<template>
    <div>
        <div class="container-fluid">
        <ul class="nav nav-tabs" id="my" role="tablist">
            <li class="nav-item active">
                <a class="nav-link" id="list-tab" data-toggle="tab" href="#listtab" role="tab" aria-controls="listtab" aria-selected="false">Game Lobby</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="my-tab" data-toggle="tab" href="#mytab" role="tab" aria-controls="mytab" aria-selected="true">My Games</a>
            </li>
        </ul>
        <div class="tab-content" id="myContent">
            <div class="tab-pane fade in active" id="mytab" role="tabpanel" aria-labelledby="my-tab">
                <h3>New Game:</h3>
                <button class="btn btn-xs btn-success" v-on:click.prevent="createGame">Create a New Game</button>
                <div class="alert alert-info" v-if="message!=''">
                    <strong>{{ message }}</strong>
                </div>
                <hr>
                <div v-if="activeGames.length == 0">
                    <h3 class="text-center">No games yet...</h3>
                </div>
                <template v-for="game in activeGames">
                    <blackjack :game="game"></blackjack>
                </template>
            </div>
            <div class="tab-pane fade" id="listtab" role="tabpanel" aria-labelledby="list-tab">
                <h4>Pending games: </h4>
                <list-games :games="lobbyGames" @join-click="join"></list-games>
                <div v-if="lobbyGames.length == 0">
                    <h3 class="text-center">Waiting for games...</h3>
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
            console.log(this.$root.$data.nameOfUser);
            return {
                currentPlayer: '',
                lobbyGames: [],
                activeGames: [],
                socketId: "",
                nPlayers: 1,
                newPlayer: "",
                message:''
            }
        },
        sockets:{
            connect: function (){
                console.log('socket connected');
                this.socketId = this.$socket.id;
            },
            discconnect: function (){
                console.log('socket disconnected');
                this.socketId = "";
            },
            lobby_changed: function (){
                // For this to work, websocket server must emit a message
                // named "lobby_changed"
                this.loadLobby();
            },
            my_active_games_changed: function (){
                this.loadActiveGames();
            },
            my_activegames: function (games){
                this.activeGames = games;
            },
            my_lobbygames: function (games){
                this.lobbyGames = games;
            },
            game_changed: function (game){
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
            }
        },
        methods: {
            playerInfo: function () {
                axios.get('/api/user')
                    .then((response) => {
                        this.currentPlayer = response.data.nickname;
                    })
                    .catch((error) => {
                    });
            },
            getSizeRange: function () {
                axios.get('/api/game/countPieces')
                    .then((response) => {
                        console.log("Range: "+response.data.range);
                        this.range = response.data.range;
                    })
                    .catch((error) => {
                        console.log("Error range: ");
                    });
            },
            loadLobby: function (){
                // send message to server to load the list of games on the lobby
                this.$socket.emit('get_my_lobbygames');
            },
            loadActiveGames: function (){
                // send message to server to load the list of games that player is playing
                this.$socket.emit('get_my_activegames');
            },
            getPlayers: function () {
                axios.get('api/getnewuser/' + document.getElementById("newuser").value)
                    .then((response) => {
                        this.newPlayer = response.data.msg;
                    })
                    .catch((error) => {
                        console.log("Erro:");
                    });
            },
            createGame: function (){       
                if (this.currentPlayer == "") {
                    this.message = "Cannot create a game because logged user name is null.";
                    return;
                }
                //TODO:
                else {
                    axios.post('/api/createGame')
                        .then((response) => {
                            console.log("createGame:" + response.data.msg + "Gameid: " + response.data.id);
                            this.$socket.emit('create_game', { playerName: this.currentPlayer, gameID: response.data.id,});
                        })
                        .catch((error) => {
                            this.message="Erro ao criar jogo!";
                            // console.log("Erro: createGame: "+error.response.data.msg);
                        });
                }
            },
            join: function (game){
                // Click to join game
                for (var player of game.players){
                    if(player == this.currentPlayer){
                        this.message="You are not able to join because your name is already in use!";
                        setTimeout(this.cleanMyMessages, 5000);
                        return;
                    }
                }
                this.$socket.emit('join_game', {gameID: game.gameID, playerName: this.currentPlayer});
            },
            cleanMyMessages: function (){
                this.message='';
            },
            play: function (game, index){
                // play a game - click on piece on specified index
                this.$socket.emit('play', {gameID: game.gameID, index: index});
            },
            forceStart: function (game){
              this.$socket.emit('force_start', {gameID: game.gameID});
            },
            close: function (game){
                // to close a game if is the last game
                if(game.players.length == 1){
                    for(var i=0; i < this.activeGames.length; i++){
                        if(game.gameID == this.activeGames[i].gameID){
                            this.activeGames.splice(i, 1);
                            break;
                        }
                    }
                }
                this.$socket.emit('close_game', {gameID: game.gameID});
            }
        },
        components: {
            'list-games': ListGames,
            'blackjack': BlackJack
        },
        mounted: function () {
            this.loadLobby();
            this.playerInfo();
            this.getSizeRange();
        },
        beforeMount: function (){
            this.loadLobby();
        }

    }
</script>

<style>
    input.matrixSize{
        width: 25px;
    }
    .invalidMatrixSize{
        width: 402px;
    }
</style>

