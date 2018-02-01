<template>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <deck-list :decks="decks"></deck-list>

            </div>
        </div>

    </div>
</template>

<script type="text/javascript">
    import DeckList from './decksListComponent.vue';


    export default {
        data: function () {
            return {
                title: 'List Decks',
                showSuccess: false,
                successMessage: '',
                decks: [],
            }
        },
        methods: {
            getDecks: function () {
                axios.get('api/decks')
                    .then(response => {
                        this.decks = response.data.data;
                    });
            },
        },
        components: {
            'deck-list': DeckList,
            //'user-change': changeUserState
        },
        mounted() {
            this.getDecks();

        }

    }
</script>
