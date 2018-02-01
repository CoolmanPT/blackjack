<template>
    <div class="gameseparator">
        <div>
            <h2 v-cloak class="text-center">Game {{game.gameID}} of {{game.players[0]}}</h2>
            <br>
        </div>
        <div class="game-zone-content">
            <div class="alert" :class="alertType">
                <span v-cloak><strong>{{ message }}</strong> &nbsp;&nbsp;&nbsp;&nbsp;
                    <button v-if="game.players.length > 1 &&
                            verifyPlayerCurrent(game.playersSocketID[0]) && !game.gameStarted && !game.gameEnded"
                            class="btn btn-warning" v-on:click.prevent="startGame">Start Game</button>&nbsp;&nbsp;
                    <button v-if="!game.gameEnded" class="btn btn-danger" v-on:click.prevent="closeGame">Leave Game</button>
                    <button v-if="game.gameEnded" class="btn btn-info" v-on:click.prevent="removeGame">Close Finished Game</button>
                </span>
            </div>
            <div class="table" v-if="game.gameStarted">
                <div class="row">
                    <div class="col-sm-12">
                        <div v-if="game.hands.length>0">

                            <div v-cloak v-for="(hand, key1) of game.hands" v-if="key1==3" v-bind:key="key1">
                                <div v-if="hand.cards.length>0">
                                    <div v-if="verifyPlayerCurrent(hand.player) || game.gameEnded">
                                        <div v-for="(card, key2) of hand.cards" v-bind:key="key2">
                                            <img class="card" v-bind:src="pieceImageURL(key1,key2)">
                                        </div>
                                    </div>
                                    <div v-else>
                                        <div v-for="(card, key2) of hand.cards" v-bind:key="key2">
                                            <div v-if="key2==0">
                                                <img class="card" v-bind:src="pieceImageURL(key1,key2)">
                                            </div>
                                            <div v-else>
                                                <img class="card" v-bind:src="game.deckDirectory + 'semFace.png'">
                                            </div>
                                        </div>
                                    </div>
                                    <p :class="{imHere: verifyPlayerCurrent(hand.player)}">{{game.players[key1]}}</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div v-if="game.hands.length>0">

                            <div v-cloak v-for="(hand, key1) of game.hands" v-if="key1==2" v-bind:key="key1">
                                <div v-if="hand.cards.length>0">
                                    <div v-if="verifyPlayerCurrent(hand.player) || game.gameEnded">
                                        <div v-for="(card, key2) of hand.cards" v-bind:key="key2">
                                            <img class="card" v-bind:src="pieceImageURL(key1,key2)">
                                        </div>
                                    </div>
                                    <div v-else>
                                        <div v-for="(card, key2) of hand.cards" v-bind:key="key2">
                                            <div v-if="key2==0">
                                                <img class="card" v-bind:src="pieceImageURL(key1,key2)">
                                            </div>
                                            <div v-else>
                                                <img class="card" v-bind:src="game.deckDirectory + 'semFace.png'">
                                            </div>
                                        </div>
                                    </div>
                                    <p :class="{imHere: verifyPlayerCurrent(hand.player)}">{{game.players[key1]}}</p>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div v-if="game.hands.length>0">

                            <div v-cloak v-for="(hand, key1) of game.hands" v-if="key1==1" v-bind:key="key1">
                                <div v-if="hand.cards.length>0">
                                    <div v-if="verifyPlayerCurrent(hand.player) || game.gameEnded">
                                    <div v-for="(card, key2) of hand.cards" v-bind:key="key2">
                                        <img class="card" v-bind:src="pieceImageURL(key1,key2)">
                                    </div>
                                    </div>
                                        <div v-else>
                                            <div v-for="(card, key2) of hand.cards" v-bind:key="key2">
                                                <div v-if="key2==0">
                                                    <img class="card" v-bind:src="pieceImageURL(key1,key2)">
                                                </div>
                                                <div v-else>
                                                    <img class="card" v-bind:src="game.deckDirectory + 'semFace.png'">
                                                </div>
                                            </div>
                                        </div>
                                        <p :class="{imHere: verifyPlayerCurrent(hand.player)}">{{game.players[key1]}}</p>
                                    </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div v-if="game.hands.length>0">

                            <div v-cloak v-for="(hand, key1) of game.hands" v-if="key1==0" v-bind:key="key1">
                                <div  v-if="hand.cards.length>0">
                                    <div v-if="verifyPlayerCurrent(hand.player) || game.gameEnded">
                                        <div v-for="(card, key2) of hand.cards" v-bind:key="key2">
                                            <img class="card" v-bind:src="pieceImageURL(key1,key2)">
                                        </div>
                                    </div>
                                    <div v-else>
                                        <div v-for="(card, key2) of hand.cards" v-bind:key="key2">
                                            <div v-if="key2==0">
                                                <img class="card" v-bind:src="pieceImageURL(key1,key2)">
                                            </div>
                                            <div v-else>
                                                <img class="card" v-bind:src="game.deckDirectory + 'semFace.png'">
                                            </div>
                                        </div>
                                    </div>
                                    <p :class="{imHere: verifyPlayerCurrent(hand.player)}">{{game.players[key1]}}</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="alert-info" v-show="game.gameStarted && !game.gameEnded && isRoundActive">
                    <p v-cloak>Left time to play: {{ leftTimeToPlay }}</p>
                    <button type="button" :class="{'disable': isActive}" class="btn btn-primary" v-on:click="hit">Hit</button>
                    <button type="button" :class="{'disable': isActive}" class="btn btn-primary" v-on:click="stand">Stand</button>
                </div>
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
                id: -1,
                alertType:'alert-info',
                leftTime:[],
                round: 1,
                roundActive: false,
                allowedToPlay: true,
                playerRoundEnd: false,
            }
        },
        computed: {
            isActive: function(){
                return this.playerRoundEnd;
            },
            isRoundActive: function(){
                return this.game.gameRoundActive;
            },
            leftTimeToPlay: function(){
                return this.leftTime[this.game.gameID];
            },
            message: function () {
                if(!this.game.gameStarted){
                    return "Game not started yet. Waiting for players... ";
                }else if(this.game.gameEnded){
                    if(this.game.playerWinner == this.$parent.socketId){
                        this.alertType='alert-success';
                        return "You win!!";
                    }else{
                        if(this.game.playersTied.length > 0){
                            for(let i=0;  i < this.game.playersTied.length; i++){
                                if(this.$parent.socketId == this.game.playersTied[i]){
                                    this.alertType='alert-success';
                                    return "You tied!!";
                                }
                            }
                            this.alertType='alert-danger';
                            return "You lost!!";
                        }else{
                            this.alertType='alert-danger';
                            return "You lost!!";
                        }
                    }
                }
                return "";
            },
        },
        methods: {
            verifyPlayerCurrent: function(playerId){
                return this.$parent.socketId == playerId;
            },
            startNextRound: function(){
                this.round++;
                if(this.round === 2){
                    console.log('Round 2');
                    this.$parent.startGameRound2(this.game);
                }
                if(this.round === 3){
                    console.log('Round 3');
                    this.$parent.startGameRound3(this.game);
                }
                this.playerRoundEnd=false;
                this.roundActive=true;
                this.leftTime[this.game.gameID] = 20;
                this.timer();
            },
            hit: function(){
                this.playerRoundEnd = true;
                this.allowedToPlay = true;
            },
            stand: function(){
                this.allowedToPlay = false;
                this.playerRoundEnd = true;
                for(let i=0;  i < this.game.hands.length; i++){
                    if (this.game.hands[i].player == this.$parent.socketId) {
                        this.game.hands[i].drawAllowed = false;
                    }
                }
                this.$parent.standGame(this.game);
            },
            endRound: function(){
                if(this.allowedToPlay){
                    this.$parent.play(this.game);
                }
                if(this.round < 3){
                    setTimeout(this.startNextRound(), 1000);
                }else{
                    this.$parent.finish(this.game);
                }
            },
            timer: function () {
                if(this.game.gameEnded){
                    this.leftTime[this.game.gameID] = "";
                    return;
                }
                if(this.leftTime[this.game.gameID] == ""){
                    return;
                }
                this.$set(this.leftTime, this.game.gameID, this.leftTime[this.game.gameID]- 1);

                console.log("Time: "+this.leftTime[this.game.gameID]);
                if(this.leftTime[this.game.gameID] == 0) {
                    //this.$parent.close(this.game);
                    console.log('End round '+this.round);
                    if(!this.playerRoundEnd){
                        this.stand();
                    }
                    this.endRound();
                }else{
                    setTimeout(this.timer, 1000);
                }
            },
            closeGame: function () {
                this.$parent.close(this.game);
            },
            removeGame:function(){
                this.$parent.remove(this.game);
            },
            startGame: function () {
                console.log('startGame!');
                this.$parent.startGame(this.game);
                this.$parent.play(this.game);
                this.$parent.play(this.game);
                this.startNextRound();
            },
            pieceImageURL: function (key1,key2) {
                //console.log('Posição: '+key1+' card: '+this.game.handsVisible[key1].cards[key2][0].card);
                return this.game.deckDirectory + this.game.hands[key1].cards[key2][0].card + '.png';
            }
        },
        beforeMount(){
            this.leftTime[this.game.gameID] = "";
            this.id=this.game.gameID;
        }
    }
</script>
