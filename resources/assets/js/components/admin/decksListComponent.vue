<template>
    <div>
        <h1 class="mt-2">List of Decks </h1> 
        <hr>
        <router-link to="/newDeck" class="btn btn-dark btn-lg"> New Deck</router-link>
        <div class="alert alert-success" role="alert" v-if="showSuccess">
            <p>{{successMessage}}</p>
        </div>
        <table class="table table-hover table-striped border-0 mt-5" v-if="decks.length > 0">
            <thead>
            <tr>
                <th class="bg-dark">Name</th>
                <th class="bg-dark">Path</th>
                <th class="bg-dark">Active</th>
                <th class="bg-dark">Complete</th>
                <th class="bg-dark">Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="deck in decks" :key="deck.id" :class="">

                <td>{{ deck.name }}</td>
                <td><img :src="deck.hiddenFaceImagePath"></td>
                <td>{{ deck.active }}</td>
                <td>{{ deck.complete }}</td>
                <td>
                    <b-button @click="editDeck(deck)">
                        Edit Deck
                    </b-button>
                    <a class="btn btn-xs btn-danger" v-on:click.prevent="deleteDeck(deck)">Delete Deck</a>
                    <router-link to="/addCard" class="btn btn-dark"> Add Cards</router-link>
                </td>
            </tr>
            </tbody>
        </table>
        <div v-else> <h2>No decks created</h2></div>
        <b-modal id="editDeckModal"
                 ref="editDeckModal"
                 title='Edit Deck'
                 @ok="saveDeck"
                 @shown="cancel">
            <form @submit.stop.prevent="handleSubmit">
                <p>Current Deck: {{ deck.name}}</p>
                <div class="form-group">
                    <b-form-input v-model="deckName"
                  type="text"
                  placeholder="Name" class="form-control"></b-form-input>
                </div>
                  <b-form-file v-model="image" :state="Boolean(image)" placeholder="Choose a file..." @change="onFileChange"></b-form-file>
            </form>
        </b-modal>
    </div>

</template>

<script type="text/javascript">
    // Component code (not registered)

    module.exports = {
        props: ['decks'],
        data: function () {
            return {
                deck: '',
                deckName: '',
                image: '',
                successMessage: '',
                showSuccess: false,
            }
        },
        components: {

        },
        methods: {
            deleteDeck(deck) {
               this.deck = deck;
                axios.delete('api/decks/' + deck.id)
                        .then(response => {
                            this.$parent.getDecks();
                            this.showSuccess = true;
                            this.successMessage = "Deck deleted!";
                            
                        });
            },
            editDeck(deck) {
                this.deck = deck;
                
                    this.$refs.editDeckModal.show(deck);
                

            },
            saveDeck(evt) {
                evt.preventDefault();

                if (!this.deckName || !this.image) {
                    alert('Please enter the name and path');
                } else {
                    const data = {
                        id: this.deck.id,
                        deckName: this.deckName,
                        image: this.image,
                    };
                    console.log(data);
                    axios.post('/api/decks/update', data)
                        .then((response) => {
                            this.$parent.getDecks();
                            this.success = true;
                            this.attemptSubmit = false;
                            
                        })
                        .catch((error) => {
                            this.serverError = true;
                            this.serverErrorMessage = error.response.data.msg;
                        });
                        this.handleSubmit();
                }
                
                
            },
            onFileChange(e) {
                console.log(e.target.files[0])
                var fileReader = new window.FileReader();
                fileReader.readAsDataURL(e.target.files[0]);
                fileReader.onload = (e) => {this.image = e.target.result};

            },
            createImage(file) {
                if (image.type == "image/jpeg" || image.type == "image/jpg" || image.type == "image/png") {
                    this.image = '';
                    let reader = new FileReader();
                    let vm = this;
                    reader.onload = (e) => {
                        vm.image = e.target.result;
                    };

                    reader.readAsDataURL(file);
                    this.imageChanged = true;
                } else {
                    this.wrongFormat = true;
                }
            },
            upload(){
                if (this.image) {
                    const data = {
                        name: this.name,
                        image: this.image,
                    };

                    axios.post('/api/decks/store', data)
                        .then((response) => {
                        this.success = true;
                    this.clear();
                })
                .catch((error) => {
                        this.serverErrorCode = error.response.data.errorCode;
                    this.serverErrorMessage = error.response.data.msg;
                });
                    
                }
            },
            cancel() {

            },
            handleSubmit() {
            this.$refs.editDeckModal.hide();



        },

    },
    }
</script>