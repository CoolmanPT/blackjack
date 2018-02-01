/*jshint esversion: 6 */
var MyConnection = require('./connection.js');

class BlackJackGame {
    constructor(ID, player1Name, date, deck) {
        this.gameID = ID;
        this.gameEnded = false;
        this.gameStarted = false;
        this.gameRoundActive = false;
        this.createdAt = date;
        this.gameRound = 1;
        this.roundPlayers = 0;
        //players
        this.players=[];
        this.players[0]=player1Name;
        this.playersSocketID=[];
        //for points
        this.playersPoints=[];
        this.playerWinner = null;
        this.playersTied = [];
        //cards
        this.deck = [];
        this.suits = ['c', 'e', 'o', 'p'];
        this.values = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13'];
        this.hands = [];
        this.handsVisible = [];

        this.deckDirectory = '/img/decks/'+deck+'/';

        this.getDeck();
        this.shuffleDeck();
    }

    join(playerName){
        this.players[this.players.length] = playerName;
        this.playersPoints[this.playersPoints.length] = 0;
        if(this.players.length === 4){
            this.gameStarted = true;
            conn.startGame(this.gameID);
        }
    }

    getDeck(){
        for (let s = 0; s < this.suits.length; s++) {
            for (let v = 0; v < this.values.length; v++) {
                var val=0;
                if(v === 0){
                    val=11;
                }else if(v>0 && v<10){
                    val = v+1;
                }else{
                    val = 10;
                }
                var card = {value: val, card: this.suits[s] + this.values[v]};
                this.deck.push(card);
            }
        }
        console.log('Deck created');
    }

    shuffleDeck() {
        var m = this.deck.length, t, i;
        while (m) {
            i = Math.floor(Math.random() * m--);
            t = this.deck[m];
            this.deck[m] = this.deck[i];
            this.deck[i] = t;
        }
        console.log('Fisher–Yates shuffle');
    }
    
    drawFromDeck(socketID) {
        this.handsVisible = [];
        for(let i = 0; i < this.playersSocketID.length; i++){
            if(this.hands[i].player==socketID){
                if(this.hands[i].drawAllowed){
                    this.hands[i].cards.push(this.deck.slice(0, 1));
                    //console.log(this.hands[i]);
                    this.playersPoints[i]+=this.deck[0].value;

                    this.deck.splice(0, 1);

                    if(this.playersPoints[i]===21){
                        this.hands[i].drawAllowed=false;
                    }
                    this.handsVisible.push(this.hands[i]);
                }
            }else{
                if(this.hands[i].cards.length === 1){
                    this.handsVisible.push(
                        {player: this.hands[i].player,
                            cards: [this.hands[i].cards[0]],
                            drawAllowed: this.hands[i].drawAllowed}
                            );
                }else if(this.hands[i].cards.length === 2){
                    this.handsVisible.push(
                        {player: this.hands[i].player,
                            cards: [this.hands[i].cards[0],
                                {value:0,card:'semFace'}],
                            drawAllowed: this.hands[i].drawAllowed}
                            );
                }else if(this.hands[i].cards.length === 3){
                    this.handsVisible.push(
                        {player: this.hands[i].player,
                            cards: [this.hands[i].cards[0],
                                {value:0,card:'semFace'},
                                {value:0,card:'semFace'}],
                            drawAllowed: this.hands[i].drawAllowed}
                    );
                }else if(this.hands[i].cards.length === 4){
                    this.handsVisible.push(
                        {player: this.hands[i].player,
                            cards: [this.hands[i].cards[0],
                                {value:0,card:'semFace'},
                                {value:0,card:'semFace'},
                                {value:0,card:'semFace'}],
                            drawAllowed: this.hands[i].drawAllowed}
                    );
                }
            }
        }
        if(this.gameRound <2){
            this.roundPlayers--;

            if(this.roundPlayers===0){
                this.gameRoundActive=false;
                return this.gameRound;
            }
        }
        return this.gameRound;
    }

    startRound2(){
        if(!this.gameRoundActive && this.gameRound === 1){
            this.gameRoundActive = true;
            this.gameRound=2;
            this.roundPlayers++;
        }
    }

    startRound3(){
        if(!this.gameRoundActive && this.gameRound === 2){
            this.gameRoundActive = true;
            this.gameRound=3;
            this.roundPlayers++;
        }
    }

    standRound(id){
        for(let i = 0; i < this.playersSocketID.length; i++){
            if(this.playersSocketID[i] === id){
                this.hands[i].drawAllowed=false;
            }
        }
        this.roundPlayers--;

        if(this.roundPlayers===0){
            this.gameRoundActive=false;
            return this.gameRound;
        }
        return this.gameRound;
    }

    forceStart(){
        this.gameStarted=true;
        conn.startGame(this.gameID);
    }

    finishGame(){
        var pos,val = 0,counter=0;
        for(let i = 0; i < this.playersPoints.length; i++){
            if(this.playersPoints[i] > val && this.playersPoints[i] <= 21){
                pos = i;
                val = this.playersPoints[i];
            }
        }
        for(let i = 0; i < this.playersPoints.length; i++){
            if(this.playersPoints[i] == val && i != pos){
                counter++;
                this.playersTied.push(this.playersSocketID[i]);
                if(this.playersPoints[pos]==21){
                    conn.setTie(this.gameID, this.players[i], 100);
                }else{
                    conn.setTie(this.gameID, this.players[i], 50);
                }
            }else{
                conn.setLost(this.gameID, this.players[i], 0);
            }
        }
        if(counter==0){
            this.playerWinner = this.playersSocketID[pos];
            if(this.playersPoints[pos]==21){
                conn.setWin(this.gameID, this.players[pos],150);
            }else{
                conn.setWin(this.gameID, this.players[pos],100);
            }
        }else{
            this.playersTied.push(this.playersSocketID[pos]);
            if(this.playersPoints[pos]==21){
                conn.setTie(this.gameID, this.players[pos], 100);
            }else{
                conn.setTie(this.gameID, this.players[pos], 50);
            }
        }
        conn.gameTerminate(this.gameID);

        this.gameEnded=true;
    }

}
module.exports = BlackJackGame;