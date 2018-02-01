<template>
    <div>
        <div class="container">
            <div class="row">
                <div class="col-md-5 col-sm-12  mt-5 ml-auto mr-auto">
                    <div class="card">
                        <div class="card-header bg-dark">
                            <h1 class="text-center">
                                Recuperar Password
                            </h1>
                        </div>
                        <div class="card-body pb-1">
                            <form method="post" v-on:submit="validateForm">
                                <div class="input-group form-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="fa fa-envelope fa-fw"></i></div>
                                    </div>
                                    <input type="text" name="email" v-model="email"
                                           v-bind:class="{ 'is-invalid': missingEmail || invalidEmail  }"
                                           class="form-control" placeholder="Email"/>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block mt-3">Recuperar</button>

                                <div class="clearfix">

                                    <div class="float-right mt-3 mb-0">
                                        <p class="mb-0">Voltar para o
                                            <router-link to="/login" class="">login</router-link>
                                        </p>
                                    </div>
                                </div>
                            </form>
                            <div class="alert alert-success" role="alert" v-cloak v-show="success">
                                <p>O email com o link de reset foi enviado!</p>
                            </div>
                            <div class="alert alert-danger" role="alert" v-cloak v-show="isFormInvalid">
                                <p v-if="missingEmail">Introduza o email</p>
                                <p v-if="invalidEmail">Email inválido</p>
                                <p v-if="serverError">{{ serverErrorMessage }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script type="text/javascript">
    export default {
        data: function () {
            return {
                email: '',
                attemptSubmit: false,
                serverError: false,
                serverErrorMessage: '',
                success: false,
                serverUser: '',
                serverToken: '',

            }
        },
        computed: {
            missingEmail: function () {
                return this.email.trim() === '' && !this.hasServerError && this.attemptSubmit;
            },
            invalidEmail: function () {
                return !this.missingEmail && !this.validateEmail(this.email.trim()) && !this.hasServerError && this.attemptSubmit;
            },
            hasClientError: function () {
                return (this.missingEmail || this.invalidEmail);
            },
            hasServerError: function () {
                return this.serverError;
            },
            isFormInvalid: function () {
                return (this.hasClientError || this.hasServerError) && this.attemptSubmit;
            },
        },
        methods: {
            validateEmail: function (email) {
                var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                return re.test(email);
            },
            validateForm: function () {
                //CLEARS SERVER ERROR'S
                this.serverError = false;
                this.success = false;

                //PREVENT FORM
                event.preventDefault();

                //FORM SUBMITED
                this.attemptSubmit = true;


                if (!this.isFormInvalid) {
                    const data = {
                        email: this.email
                    };
                    axios.post('/api/password/email', data)
                        .then((response) => {
                            this.success = true;
                            this.attemptSubmit = false;
                            //this.email = '';
                        })
                        .catch((error) => {
                            this.serverError = true;
                            this.serverErrorMessage = error.response.data.msg + "vue";
                        });
                }
            },
        }
    }
</script>