<template>
    <div>
        <div class="container-fluid">
            <div class="row">
                <h1 class="jumbotron">Estatísticas da Plataforma</h1>
            </div>
            <div class="row">
                <div class="col-sm-7">
                    <h2>Estatistica Geral</h2>
                    <div class="row">
                        <div class="col-sm-12">
                            <p>Total Jogadores: <strong>{{ statistic.totalPlayers }}</strong> </p>
                            <p>Total Jogos Já jogados: <strong>{{ statistic.totalGames }}</strong> </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <h5>Top 5 Jogos Ganhos:</h5>
                            <div v-for="(user, index) in usersWins">
                                <strong>{{ index+1 }}º</strong> {{ user.nickname }} <br />
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <h5>Top 5 Pontuação:</h5>
                            <div v-for="(user, index) in usersScore">
                                <strong>{{ index+1 }}º</strong> {{ user.nickname }} <br />
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <h5>Top 5 Média:</h5>
                            <div v-for="(user, index) in usersAVG">
                                <strong>{{ index+1 }}º</strong> {{ user.nickname }} <br />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-5">
                    <h2>Estatística Pessoal</h2>
                    <div class="row">
                        <div class="col-sm-12">
                            <span>Total Jogos: <strong>{{ userStatistic.gamesPlayed }}</strong></span>
                        </div>
                        <div class="col-sm-12">
                            <span>Total Vitórias: <strong>{{ userStatistic.gamesWon }}</strong></span>
                        </div>
                        <div class="col-sm-12">
                            <span>Total Empates: <strong>{{ userStatistic.gamesTied }}</strong></span>
                        </div>
                        <div class="col-sm-12">
                            <span>Total Derrotas: <strong>{{ userStatistic.gamesLost }}</strong></span>
                        </div>
                        <div class="col-sm-12">
                            <span>Total Pontuação: <strong>{{ userStatistic.score }}</strong></span>
                        </div>
                        <div class="col-sm-12">
                            <span>A Sua Média: <strong>{{ userStatistic.average }}</strong></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script type="text/javascript">
    export default {
        data: function(){
            return {
                statistic: {
                    totalPlayers: '',
                    totalGames: '',
                },
                usersWins: [],
                usersScore: [],
                usersAVG: [],
                userStatistic: {
                    gamesPlayed: '',
                    gamesWon: '',
                    gamesTied: '',
                    gamesLost: '',
                    score: '',
                    average: '',
                },
            }
        },
        methods: {
            getStatistics: function () {
                axios.get('/api/generalstatistics')
                    .then(response=>{
                        this.statistic = response.data;
                    });
            },
            getTop5Wins: function () {
                axios.get('/api/topfivewins')
                    .then(response=>{
                        this.usersWins = response.data.data;
                    });
            },
            getTop5Score: function () {
                axios.get('/api/topfivescore')
                    .then(response=>{
                        this.usersScore = response.data.data;
                    });
            },
            getTop5AVG: function () {
                axios.get('/api/topfiveavg')
                    .then(response=>{
                        this.usersAVG = response.data.data;
                    });
            },
            getUserStatistics: function () {
                axios.post('/api/userstatistics')
                    .then(response=>{
                        this.userStatistic = response.data;
                    });
            },
        },
        created: function () {
            this.getUserStatistics();
            this.getTop5AVG();
            this.getTop5Score();
            this.getTop5Wins();
            this.getStatistics();
        }
    }
</script>