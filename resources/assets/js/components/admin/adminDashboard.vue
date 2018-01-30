<template>
	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<h1 class="text-left mt-2">
					Estatisiticas da Plataforma
				</h1>
				<hr>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-6">
				<p>Total Jogadores: {{ statistic.totalPlayers }} </p>
			</div>
			<div class="col-sm-6">
				<p>Total Jogos Já jogados: {{ statistic.totalGames }} </p>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<ul  class="nav nav-tabs nav-fill mt-5">
					<li class="nav-item">
						<a  href="#1a" class="nav-link active" id="graphics-tab" data-toggle="tab" role="tab" aria-controls="home" aria-selected="true">Estatisticas da Plataforma</a>
					</li>
					<li class="nav-item">
						<a href="#2a" class="nav-link" id="user-stat-tab" data-toggle="tab" role="tab" aria-controls="profile" aria-selected="false">Estatisticas de Cada Jogador</a>
					</li>
				</ul>
				<div class="tab-content border border-top-0 pt-3" id="myTabContent">
					<div class="tab-pane fade show active" role="tabpanel" aria-labelledby="graphics-tab" id="1a">
						<div v-show="loadingGamesPerDate">
							<div class="alert alert-danger" v-if="isThereNoGameCreated">
								<h3>{{ errorMessage }}</h3>
							</div>
							<div v-else>
								<line-char @message="childMessage"></line-char>
							</div>
						</div>
					</div>
					<div class="tab-pane fade" role="tabpanel" aria-labelledby="user-stat-tab" id="2a">
						<user-statistics></user-statistics>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
<script type="text/javascript">
    import LineChart from './line_graph_gamesperdate.vue';
    import UserStatistics from './user_statistics.vue';

    export default {
        data: function(){
            return {
                statistic: {
                    totalPlayers: '',
                    totalGames: '',
                },
                code: 0,
                errorMessage: '',
                loadingGamesPerDate: false,
            }
        },
        methods: {
            getStatistics: function () {
                axios.get('/api/generalstatistics')
                    .then(response=>{
                        this.statistic = response.data;
                    });
            },
            childMessage: function(data){
                this.code = data.code;
                this.errorMessage = data.message;
                this.loadingGamesPerDate = true;
            },
        },
        computed: {
            isThereNoGameCreated: function(){
                return this.code > 0;
            },
        },
        components: {
            'line-char': LineChart,
            'user-statistics' : UserStatistics,
        },
        created: function () {
            this.getStatistics();
        }
    }
</script>