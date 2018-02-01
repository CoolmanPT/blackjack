<template>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h1>New Deck</h1>
            </div>
        </div>
        <div class="row">
        <div class="col-sm-12">
            <form action="">
                <div class="alert alert-success " role="alert" v-cloak v-show="success">
                <p>New Deck Image uploaded</p>
            </div>
            <div class="alert alert-danger" role="alert" v-cloak v-show="wrongFormat">
                <p>Invalid Format (jpg | png)</p>

            </div>
            
                <div class="form-group">
                                <input type="text" name="name" v-model="name" 
                                class="form-control" placeholder="Name"/>
                            </div>
                <div class="form-group">
                <label class="btn btn-success" for="inputImage" title="Upload image file">
                    <input type="file" v-on:change="onFileChange" class="sr-only" id="inputImage" name="file"
                           accept="image/*"/>
                    <span class=""> New Image</span>
                </label>

                <button type="button" @click="upload" class="btn btn-primary">Save</button>
            </div>
            </form>

        </div>
    </div>
    </div>

</template>


<script type="text/javascript">
    export default {
        data: function () {
            return {
                name: '',
                image: '',
                success: false,
                image: false,
                wrongFormat: false,
            }
        },
        computed: {

        },
        methods: {
            onFileChange(e) {
                console.log(e.target.files[0])
                var fileReader = new window.FileReader();
                fileReader.readAsDataURL(e.target.files[0]);
                fileReader.onload = (e) => {this.image = e.target.result};
                console.log(this);

            },
            createImage(file) {
                if (file.type == "image/jpeg" || file.type == "image/jpg" || file.type == "image/png") {
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
        },
        created: function () {
          
        }
    }
</script>