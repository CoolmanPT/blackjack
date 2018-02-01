<template>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h1>Estatisticas da Plataforma</h1>
                <p>Total Jogadores: {{ statistic.totalPlayers }} </p>
                <p>Total Jogos Já jogados: {{ statistic.totalGames }} </p>
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
                usersAVG: []
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
        },
        created: function () {
            this.getTop5AVG();
            this.getTop5Score();
            this.getTop5Wins();
            this.getStatistics();
        }
    }
</script>