<template>
    <div>
        <div class="container">
            <div class="row">
                <div class="col-md-5 col-sm-12  mt-5 ml-auto mr-auto">
                    <div class="card">
                        <div class="card-header">
                            <h1 class="text-center">
                                Nova Password
                            </h1>
                        </div>
                        <div class="card-body pb-1">
                            <form method="post" v-on:submit="validateForm">


                                <div class="input-group form-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="fa fa-lock fa-fw"></i></div>
                                    </div>
                                    <input type="password" name="password" v-model="password"
                                           v-bind:class="{ 'is-invalid': missingPassword || invalidPassword }"
                                           class="form-control" placeholder="Nova Password"/>
                                </div>
                                <div class="input-group form-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="fa fa-lock fa-fw"></i></div>
                                    </div>
                                    <input type="password" name="passwordConfirmation" v-model="passwordConfirmation"
                                           v-bind:class="{ 'is-invalid': missingPasswordConfirmation || wrongPasswordConfirmation }"
                                           class="form-control" placeholder="Confirme a Password"/>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block mt-3">Alterar</button>
                                <div class="float-right  mt-3 mb-0">
                                    <p class="">Ir para a página de
                                        <router-link to="/login" class="to_register">login</router-link>
                                    </p>
                                </div>
                                <div class="clearfix">
                                    <div class="alert alert-danger" role="alert" v-cloak v-show="isFormInvalid">
                                        <p v-if="missingPassword">Introduza a password</p>
                                        <p v-if="invalidPassword">Password têm que ter pelo menos 6 caracteres.</p>
                                        <p v-if="missingPasswordConfirmation">Confirme a password</p>
                                        <p v-if="wrongPasswordConfirmation">Confirmação da password não corresponde a
                                            password</p>
                                        <p v-if="serverError">{{ serverErrorMessage }}</p>
                                        <div class="alert alert-success" role="alert" v-cloak v-show="success">
                                            <p>Password alterada com sucesso! Faça login.</p>
                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script type="text/javascript">
    export default {
        props: ['token', 'user'],
        data: function () {
            return {
                password: '',
                passwordConfirmation: '',
                attemptSubmit: false,
                serverError: false,
                serverErrorMessage: '',
                success: false
            }
        },
        computed: {
            missingPassword: function () {
                return this.password.trim() === '' && !this.hasServerError && this.attemptSubmit;
            },
            invalidPassword: function () {
                return !this.missingPassword && !this.validatePasswordStructure(this.password.trim()) && !this.hasServerError && this.attemptSubmit;
            },
            missingPasswordConfirmation: function () {
                return !this.missingPassword && !this.invalidPassword && this.passwordConfirmation.trim() === '' && !this.hasServerError && this.attemptSubmit;
            },
            wrongPasswordConfirmation: function () {
                return !this.missingPassword && !this.invalidPassword && !this.missingPasswordConfirmation && (this.passwordConfirmation.trim() != this.password.trim()) && !this.hasServerError && this.attemptSubmit;
            },
            hasClientError: function () {
                return (this.missingEmail || this.invalidEmail || this.missingPassword || this.invalidPassword || this.wrongPasswordConfirmation || this.missingPasswordConfirmation);
            },
            hasServerError: function () {
                return this.serverError;
            },
            isFormInvalid: function () {
                return (this.hasClientError || this.hasServerError) && this.attemptSubmit;
            },
        },
        methods: {
            validatePasswordStructure: function (password) {
                var re = /^[a-zA-Z0-9]{6,}$/;
                return re.test(password);
            },
            validateForm: function () {
                //CLEARS SERVER ERROR'S
                this.serverError = false;
                this.success = false;

                //PREVENT FORM
                event.preventDefault();

                //FORM SUBMITED
                this.attemptSubmit = true;

                //IF FORM IS VALID MAKE API REQUEST FOR LOGIN
                if (!this.isFormInvalid) {
                    const data = {
                        email: this.user,
                        token: this.token,
                        password: this.password
                    };
                    console.log(data);
                    axios.post('/api/password/reset', data)
                        .then((response) => {

                            this.success = true;
                            this.attemptSubmit = false;
                            this.email = '';
                            this.password = '';
                            this.passwordConfirmation = '';

                        })
                        .catch((error) => {
                            this.serverError = true;
                            this.serverErrorMessage = "server" + error.response.data.msg;
                        });
                }
            },
        }
    }
</script>