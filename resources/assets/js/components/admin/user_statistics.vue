<template>
    <div>
        <div class="container">
            <form id="search">
                Search <input name="query" v-model="searchQuery">
            </form>
            <div v-if="loading">
                <h3>{{ message }}</h3>
            </div>
            <div v-else>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th v-for="key in gridColumns" @click="sortBy(key)" :class="{ active: sortKey == key }">
                                {{ key | capitalize }}
                                <span class="arrow" :class="sortOrders[key] > 0 ? 'asc' : 'dsc'"></span>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="entry in filteredData">
                            <td v-for="key in gridColumns">
                                {{entry[key]}}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
<script type="text/javascript">
    export default {
        data: function () {
            return {
                sortKey: '',
                sortOrders: {'nome':1,'email':1, 'username':1, 'jogos':1, 'vitorias':1, 'empates':1, 'derrotas':1},
                searchQuery: '',
                gridColumns: ['nome', 'email', 'username', 'jogos', 'vitorias', 'empates', 'derrotas'],
                gridData: [],
                loading: true,
                message: 'Loading ... :<',
            }
        },
        computed: {
            filteredData: function () {
                var sortKey = this.sortKey;
                var filterKey = this.searchQuery && this.searchQuery.toLowerCase();
                var order = this.sortOrders[sortKey] || 1;
                var data = this.gridData;
                if (filterKey) {
                    data = data.filter(function (row) {
                        return Object.keys(row).some(function (key) {
                            return String(row[key]).toLowerCase().indexOf(filterKey) > -1;
                        })
                    })
                }
                if (sortKey) {
                    data = data.slice().sort(function (a, b) {
                        a = a[sortKey];
                        b = b[sortKey];
                        return (a === b ? 0 : a > b ? 1 : -1) * order;
                    })
                }
                return data;
            }
        },
        filters: {
            capitalize: function (str) {
                return str.charAt(0).toUpperCase() + str.slice(1);
            }
        },
        methods: {
            sortBy: function (key) {
                this.sortKey = key;
                this.sortOrders[key] = this.sortOrders[key] * -1;
            },
            getAllUsersStatistics: function(){
                axios.post('api/allusersstatistics')
                    .then(response => {
                        this.gridData = response.data.data;
                        this.loading = false;
                    })
                    .catch(error => {
                        this.message = error.response.data.message;
                    });
            },
        },
        created: function(){
            this.getAllUsersStatistics();
        },
    }
</script>