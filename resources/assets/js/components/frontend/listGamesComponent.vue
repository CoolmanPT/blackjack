<template>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>ID</th>
            <th>Player Owner</th>
            <th>Number of Players</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="game in games"  :key="game.gameID">
            <td>{{ game.gameID }}</td>
            <td>{{ game.players[0] }}</td>
            <td>{{ game.players.length }} of 4</td>
            <td>{{ game.createdAt.date }}</td>
            <td>
                <a class="btn btn-xs btn-primary" v-on:click.prevent="join(game)">Join</a>
            </td>
        </tr>
        </tbody>
    </table>
</template>

<script type="text/javascript">
    // Component code (not registered)
    module.exports={
        props: ['games','gameJoinSuccess'],
        methods: {
            join:function(game) {
                for(var i=0;  i < game.playersSocketID.length; i++){
                    if (game.playersSocketID[i] == this.$parent.socketId) {
                        return ;
                    }
                }
                this.$emit('join-click', game);
                if(this.gameJoinSuccess){
                    document.getElementById("my-tab").click();
                }
            },
        },
    }
</script>