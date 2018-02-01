/*jshint esversion: 6 */
var MyConnection = require('./connection.js');

class BlackJackGame {
    constructor(ID, player1Name) {
        this.gameID = ID;
        this.gameEnded = false;
        this.gameStarted = false;
        //players
        this.players=[];
        this.players[0]=player1Name;
        this.nPlayers = 4;
        //
        this.winners=[];
        this.winner = 0;
        //for points
        /*this.playersPoints=[];
        this.playersPoints[0]=0;
        this.playersPlaces=[];*/
        //cards
        this.deck = [];
        this.card = [];
        this.suits = '';
        this.values = '';
        this.hands = [];
        this.hand = []; // to make hands. playerName, card1, card2, ...
        this.drawAllowed = false;
        this.handValue = 0;
        //this.board = [0,0,0,0,0,0,0,0,0]; // dont need board
        //?
        this.playersSocketID=[];
        this.mainDirectory="";
        //?
        //this.hiddenFace = "";
        
        //saber quantas peças ja foram viradas
        /*
        this.currentPlayerAux= 0;
        this.pieceAux=[];
        this.piecehistory=[];
        this.moviments =0;*/
        this.getDeck();
        this.shuffleDeck();
    }

    join(playerName){
        this.players[this.players.length]=playerName;
        this.playersPoints[this.playersPoints.length]=0;
        if(this.nPlayers == this.players.length){
            this.gameStarted = true;
            conn.startGame(this.gameID);
        }
    }

    getDeck(){
        this.suits = ['c', 'e', 'o', 'p'];
        this.values = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13'];
        for (let s = 0; s < this.suits.length; s++) {
            for (let v = 0; v < this.values.length; v++) {
                this.card = [this.values[v], this.suits[s] + this.values[v] + '.png'];
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

    firstDraw(){
        console.log('Initial phase: Draw 2 cards in order')
        for (let n = 0; n < this.players.length; n++) {
            /*this.hand = [players[n], this.deck.slice(0, 1)];
            this.deck.splice(0, 1);*/
            //splice deve retirar a carta do topo do deck e meter na hand DEVE
            let x = this.deck.slice(0, 1)
            console.log(x[1] + ' para ' + this.players[n])

            this.hand = [this.players[n], this.deck.splice(0, 1)];
            this.hands.push(this.hand);
        }
        for (let m = 0; m < this.hands.length; m++){
            //para a posiçao final do o array da mão da contagem, 
            //adiciona outra carta que sai do fim do baralho
            this.hands[m][this.hands[m].length] = this.deck.splice(0, 1);
        }
    }
    
    drawFromDeck(playerName) {
        //na net vi slice (extrai a informacao) mas fica lá no deck
        //e splice extrai a info e remove do deck 
        /*this.hand = [playerName, this.deck.slice(0, 1)];
        this.deck.splice(0, 1);
        this.hands.push(hand);*/
        evaluateHand(playerName) //will change drawAllowed e handValue
        //corre as mãos
        for (let index = 0; index < this.hands.length; index++) {
            //se a mão tiver o nome do argumento playerName e
            //o tamanho do array for menor que 5 (nome + carta1 + 2 + 3 + 4)
            if (this.hands[index][0] == playerName && this.hands[index].length < 5 && this.drawAllowed == true){
                let x = this.deck.slice(0, 1)
                console.log(x[1] + ' para ' + playerName)

                this.hands[index][this.hands[m].length] = this.deck.splice(0, 1);
                console.log('Cards left: ', this.deck.length)
            }else{
                console.log('Obter mais cartas não é permitido.')
            }
        }
        //atualizar number of draws, tamanho do deck e historico 
        
        
        /* codigo da net para um jogo de poker
        if (this.hands.forEach) {
            console.log('Number of draws: ', this.numberOfDraws)
            if (this.numberOfDraws === 0) {
                // Initial phase: Draw 5 cards.
                
                this.hand = this.deck.slice(0, cardsToDraw)
                this.deck.splice(0, cardsToDraw)
                
                
            } else {
                if (this.discardsAllowed) {
                    // Drawing phase: Replace discards with drawn cards
                    console.log('Drawing phase: Replace discards with drawn cards')
                    this.drawnCards = this.deck.slice(0, cardsToDraw)
                    for (let i = 0; i < this.discards.length; i++) {
                        this.hand[this.discards[i]] = this.drawnCards[i];
                    }
                    // Remove drawn cards from deck
                    this.deck.splice(0, cardsToDraw)
                    this.discards = []
                    this.startOfHand = false
                    this.betAllowed = false
                    this.discardsAllowed = false
                    console.log('Cards left: ', this.deck.length)
                    this.numberOfDraws = this.numberOfDraws + 1
                    this.evaluateHand()
                }
            }
        }*/
    }
    evaluateHand(playerName) {
        this.handValue = 0;
        this.drawAllowed = true;
        for (let index = 0; index < this.hands.length; index++) {
            //se a mão tiver o nome do argumento playerName e
            //o tamanho do array for menor que 5 (nome + carta1 + 2 + 3 + 4)
            if (this.hands[index][0] == playerName){
                for(let i = 1; i < this.hands[index].length; i++){
                    this.card = this.hands[index][i];
                    switch (this.card[0]) {
                        case 1:
                            this.handValue += 11;
                            break; 
                        case 2:
                            this.handValue += 2;
                            break; 
                        case 3:
                            this.handValue += 3;
                            break; 
                        case 4:
                            this.handValue += 4;
                            break; 
                        case 5:
                            this.handValue += 5;
                            break; 
                        case 6:
                            this.handValue += 6;
                            break; 
                        case 7:
                            this.handValue += 7;
                            break; 
                        case 8:
                            this.handValue += 8;
                            break; 
                        case 9:
                            this.handValue += 9;
                            break; 
                        case 10:
                        case 11:
                        case 12:
                        case 13:
                            this.handValue += 10;
                            break; 
                        default: 
                            console.log('não devia de passar por aqui :^)');
                    }
                }
            }
        }
        if(handValue >= 21){
            this.drawAllowed = false;
        }
        console.log('Hand value is ' + this.handValue);
        console.log('DrawAllowed: ' + this.drawAllowed);
    }
}
module.exports = BlackJackGame;