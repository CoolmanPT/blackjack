
<script type="text/javascript">// Dá error se exister <template></template>
    import { Line } from 'vue-chartjs'

    export default {
        extends: Line,
        data: function (){
            return {
                datesToLabels: [],
                gameCountToData: [],
                dataList: [],
            }
        },
        methods: {
            getTotalGamesPerDate: function (){
                axios.post('api/totalgamesperdate')
                    .then(response => {
                        this.dataList = response.data.statList;
                        console.log(this.dataList);
                        for (var i = 0, len = this.dataList.length; i < len; i++) {
                            this.gameCountToData.push(this.dataList[i].gameCount);
                            this.datesToLabels.push(this.dataList[i].date);
                        }
                        this.$emit('message',{code: 0, errorMessage: ''});
                    }).catch(error => {
                        this.$emit('message',error.response.data);
                });
            },
        },
        mounted () {
            this.getTotalGamesPerDate();
            // Overwriting base render method with actual data.
            this.renderChart({
                labels: this.datesToLabels,
                datasets: [
                    {
                        label: 'Total Games Per Day',
                        backgroundColor: '#5c5ff8',
                        data: this.gameCountToData
                    },
                ]
            }, {
                responsive: true,
                maintainAspectRatio: false
            });
        }
    }
</script>