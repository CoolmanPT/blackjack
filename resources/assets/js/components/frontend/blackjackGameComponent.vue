<template>
    <div class="gameseparator">
        <div>
            <h2 class="text-center">Game {{game.gameID}} of {{game.players[0]}}</h2>
            <br>
        </div>
        <div class="game-zone-content">
            <div class="alert" :class="myAlertType" v-if="myMessage!=''">
                <strong>{{ myMessage }} &nbsp;&nbsp;&nbsp;&nbsp;<a v-on:click.prevent="closeGame">Close Game</a></strong>
            </div>
            <div class="alert" :class="alertType" v-if="myMessage==''">
                <strong>{{ game.successMessage }} {{ message }} &nbsp;&nbsp;&nbsp;&nbsp; <span>Your Points: {{myPoints}} </span> <a v-on:click.prevent="closeGame">Close Game</a></strong>  &nbsp;&nbsp;&nbsp;&nbsp; <span v-if="leftTime[game.gameID]!='' && game.gameStarted && game.nPlayers > 1">Left time to play: {{ leftTimeToPlay }}</span>
            </div>
            <div class="alert alert-info" v-if="game.gameEnded">
                <strong >Historic - The game has {{game.moviments}} steps, </strong><a v-on:click.prevent="startHistoric">Review of the game</a>
            </div>
            <div class="table">
                <div v-cloak v-for="(card, key) of game.hands">
                    <img v-bind:src="pieceImageURL(card)">
                </div>
                <button type="button" class="btn btn-primary" v-on:click="hit()">Hit</button>
                <button type="button" class="btn btn-primary" v-on:click="stand()">Stand</button>
            </div>
            <hr>
        </div>
    </div>
</template>

<script type="text/javascript">
    export default {
        props: ['game'],
        data: function(){
            return {
                myMessage: '',
                myAlertType:'',
                leftTime:[],
                hitoricLenght:0
            }
        },
        
        computed: {
            myPoints:function(){
                for(var i=0;  i < this.game.playersSocketID.length; i++){
                    if (this.game.playersSocketID[i] == this.$parent.socketId) {
                        return this.game.playersPoints[i];
                    }
                }
            },
            leftTimeToPlay: function(){
                return this.leftTime[this.game.gameID];
            },
            ownPlayerNumber: function () {
                for(var i=0;  i < this.game.playersSocketID.length; i++){
                    console.log("Sockets: " + this.game.playersSocketID + "Pai: " + this.$parent.socketId);
                    if (this.game.playersSocketID[i] == this.$parent.socketId) {
                        console.log("Meu: " +this.$parent.socketId);
                        return i+1;
                    }
                }
                return 0;
            },
            adversaryPlayerName: function () {
                return this.game.players[this.game.playerTurn-1];
            },
            message: function () {
                // return Message to show
                if(!this.game.gameStarted){
                    return "Game not started yet. Waiting for players " + this.game.players.length + " of " + this.game.nPlayers;
                }else if(this.game.gameEnded){ // fim de jogo
                    if(this.game.playersPlaces[0] == this.$parent.socketId){
                        //REQUEST A DIZER QUE É VENCEDOR
                        return "You win!!";
                    }else{
                        return "You lost!!";
                    }
                    /*--------------------------------------------------------------------------------------------------------*/
                    // if(this.game.winners.length > 1){ // quando tem mais que um winner
                    //     if(this.game.winners.length != this.game.players.length){// caso o numero de winners seja diferente ao numero de players
                    //         for(var i=0; this.game.winners.length; i++){
                    //             if(this.game.winners[i] == this.$parent.socketId){// caso eu seja um dos winners
                    //                 return ", It's a tie!!";//empatei com alguem
                    //             }
                    //         }
                    //     }else{// caso o numero de winners seja igual ao numero de players empataram todos
                    //         return "It's a tie!!";
                    //     }
                    // } // caso tenha sido apenas um winner
                    // if(this.game.winners[0] == this.$parent.socketId){ // se fui eu
                    //     if(this.game.players.length != 1){
                    //         return ", You win!!";
                    //     }else{
                    //         return "";
                    //     }
                    // }else if(this.game.winners[0] != this.$parent.socketId){// se nao fui eu
                    //     return ", You lost!!";
                    // }
                }else{// o jogo ainda nao acabaou
                    if(this.game.playerTurn == this.ownPlayerNumber){// é a minha vez
                        if(this.leftTime[this.game.gameID] == "" && this.game.players.length != 1){
                            this.leftTime[this.game.gameID] = 30;
                            this.comeToTimeOut();
                        }
                        return "It's your turn - "+this.game.currentPlayerAux +" piece turned";
                    }else{// nao é a minha vez
                        this.leftTime[this.game.gameID] = "";
                        return "It's " + this.adversaryPlayerName + " turn - "+this.game.currentPlayerAux +" piece turned";
                    }
                }
                return "Game is inconsistent";// outros
            },
            alertType: function () {
                if(!this.game.gameStarted){
                    return "alert-warning";
                }else if(this.game.gameEnded){
                    if(this.game.playersPlaces[0] == this.$parent.socketId){
                        return "alert-success";
                    }else{
                        return "alert-danger";
                    }
                    /*----------------------------------------*/
                    // var aux = false;
                    // for(var i=0; i < this.game.winners.length; i++){
                    //     console.log("this.game.winners" + this.game.winners);
                    //     if(this.game.winners[i] == this.$parent.socketId){
                    //         aux=true;
                    //         continue;
                    //     }
                    // }
                    // if(aux){
                    //     return "alert-success";
                    // }else{
                    //     return "alert-danger";
                    // }
                }
                if(this.game.playerTurn == this.ownPlayerNumber){
                    return "alert-success";
                }else{
                    return "alert-info";
                }
            }
        },
        methods: {
            /*startHistoric: function () {

                for (var i=0; i < this.game.x*this.game.y; i++){
                    this.$set(this.game.board, i, this.game.hiddenFace);
                }
                this.hitoricLenght=0;
                this.unRollGame();
            },
            unRollGame(){
                if(this.hitoricLenght < this.game.piecehistory.length){
                    this.$set(this.game.board, this.game.piecehistory[this.hitoricLenght], this.game.hiddenBoard[this.game.piecehistory[this.hitoricLenght]]);
                    this.hitoricLenght = this.hitoricLenght+1;
                    var self = this;
                    setTimeout(function(){self.unRollGame();}, 200);
                }
            },*/
            comeToTimeOut: function () {
                if(this.game.gameEnded){
                    this.leftTime[this.game.gameID] = "";
                    return;
                }
                if(this.leftTime[this.game.gameID] == "")
                    return;
                this.$set(this.leftTime, this.game.gameID, this.leftTime[this.game.gameID]- 1);
                // this.leftTime[this.game.gameID] = this.leftTime[this.game.gameID]- 1;
                console.log("Time: "+this.leftTime[this.game.gameID]);
                if(this.leftTime[this.game.gameID] == 0 && this.game.playerTurn == this.ownPlayerNumber) {
                    this.$parent.close(this.game);
                }else{
                    setTimeout(this.comeToTimeOut, 1000);
                }
            },
            closeGame: function () {
                // Click to close game
                this.$parent.close(this.game);
            },
            /*forceStart: function () {
                this.$parent.forceStart(this.game);
            },*/
            /*cleanMyMessages: function () {
                this.myMessage='';
                this.myAlertType='';
            },*/
            clickPiece: function (index){
                if(this.game.currentPlayerAux == 1){
                    this.leftTime[this.game.gameID] = 30;
                }
                setTimeout(this.cleanMyMessages, 5000);
                if(!this.game.gameEnded){
                    if(this.game.playerTurn != this.ownPlayerNumber){

                        this.myMessage="It's not your turn to play";
                        this.myAlertType="alert-danger";
                    }else{
                        if(this.game.board[index] == this.game.hiddenFace || this.game.returnPieces){
                            this.$parent.play(this.game, index);
                        }
                    }
                }
            },
            pieceImageURL: function (card) {
                return this.game.mainDirectory + card;
            }
        },
        /*beforeMount(){
            this.leftTime[this.game.gameID] = "";
        }*/
    }
</script>

<style scoped>
    .gameseparator{
        border-style: solid;
        border-width: 2px 0 0 0;
        border-color: black;
    }
    a{
        cursor: pointer;
    }
</style>
