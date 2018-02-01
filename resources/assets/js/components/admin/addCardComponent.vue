<template>
    <div class="container">
    	<div class="row">
    		<div class="col-sm-12">
    			<h1>Add Cards to </h1>
    		</div>
    	</div>
        
    </div>

</template>

<script type="text/javascript">
    // Component code (not registered)

    module.exports = {
        props: ['deck'],
        data: function () {
            return {
                deck: '',

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
                console.log(this);

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
                    console.log(data);
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