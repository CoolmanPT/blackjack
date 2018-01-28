<template>
    <div class="container">
        <div class="row">
            <div class="col-md-5 col-sm-12 m-auto">
                <div class="card ml-auto mr-auto mt-5">
                    <div class="card-header bg-primary">
                        <h1 class="text-center text-light">Account Activation</h1>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success" role="alert" v-cloak v-show="success">
                            <p>Registo activado com sucesso.</p>
                        </div>
                        <div class="alert alert-danger" role="alert" v-cloak v-show="serverError">
                            <p v-if="serverError">{{ serverErrorMessage }}</p>
                        </div>
                        <p class="text-center mb-0 pb-0 mt-2">Faça
                            <router-link to="/login" class="to_register">login</router-link>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script type="text/javascript">
    export default {
        props: ['token'],
        data: function(){
            return {
                serverError: false,
                serverErrorMessage: '',
                success: false
            }
        },
        mounted: function () {
            const data = {
                token: this.token
            };
            axios.post('/api/activate', data)
                .then((response) => {
                    this.success = true;
                })
                .catch((error) => {
                    this.serverError = true;
                    this.serverErrorMessage = error.response.data.msg;
                });
        }
    }
</script>