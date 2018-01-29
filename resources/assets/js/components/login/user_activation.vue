<template>
    <div>
        <h1>Activação de registo</h1>
        <div class="alert alert-success" role="alert" v-cloak v-show="success">
            <p>Registo activado com sucesso.</p>
        </div>
        <div class="alert alert-danger" role="alert" v-cloak v-show="serverError">
            <p v-if="serverError">{{ serverErrorMessage }}</p>
        </div>
        <div class="separator">
            <p class="change_link">Faça
                <router-link to="/login" class="to_register">login</router-link>
            </p>
        </div>
        <div class="clearfix"></div>
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