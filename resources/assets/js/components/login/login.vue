<template>
    <div>
        <div class="container">
            <div class="row">
                <div class="col-md-5 col-sm-12  mt-5 ml-auto mr-auto">
                    <div class="card">
                        <div class="card-header bg-dark">
                            <h1 class="text-center">Login</h1>
                        </div>
                        <div class="card-body pb-1">
                            <form method="post" v-on:submit="validateForm" class="form">
                                <div class="input-group form-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="fa fa-user fa-fw"></i></div>
                                    </div>
                                    <input type="text" name="username" v-model="username"
                                           v-bind:class="{ 'is-invalid': missingUsername  }"
                                           class="form-control" placeholder="Username/Email"/>
                                </div>

                                <div class="input-group form-group mb-0">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="fa fa-lock fa-fw"></i></div>
                                    </div>
                                    <input type="password" name="password" v-model="password"
                                           v-bind:class="{ 'is-invalid': missingPassword }"
                                           class="form-control"
                                           placeholder="Password"/>
                                </div>
                                <router-link to="/password/reset"
                                             class="float-right form-text text-muted font-weight-normal font-italic registerUserText">
                                    Recuperar
                                    Password
                                </router-link>

                                <div class="">
                                    <button type="submit" class="btn btn-primary btn-block mt-5">Log in</button>
                                </div>

                                <div class="clearfix">
                                    <p class="float-right mt-3 mb-0">
                                        Não tem conta?
                                        <router-link to="/register" class="text-primary">Registar</router-link>
                                    </p>
                                </div>
                            </form>

                            <!-- FORM ALERTS -->
                            <div class="alert alert-danger" role="alert" v-cloak v-show="isFormInvalid">
                                <p v-if="missingUsername">Introduza o username/email</p>
                                <p v-if="missingPassword">Introduza a password</p>
                                <p v-if="serverError">{{serverErrorMessage}}</p>
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
                username: '',
                password: '',
                attemptSubmit: false,
                serverError: false,
                serverErrorMessage: '',
            }
        },
        computed: {
            missingUsername: function () {
                return this.username.trim() === '' && !this.hasServerError && this.attemptSubmit;
            },
            missingPassword: function () {
                return this.password.trim() === '' && !this.hasServerError && this.attemptSubmit;
            },
            hasClientError: function () {
                return (this.missingUsername || this.missingPassword);
            },
            hasServerError: function () {
                return this.serverError;
            },
            isFormInvalid: function () {
                return (this.hasClientError || this.hasServerError) && this.attemptSubmit;
            },
        },
        methods: {
            validateForm: function () {

                //CLEARS SERVER ERROR'S
                this.serverError = false;

                //PREVENT FORM
                event.preventDefault();

                //FORM SUBMITED
                this.attemptSubmit = true;

                //IF FORM IS VALID MAKE API REQUEST FOR LOGIN
                if (!this.isFormInvalid) {
                    const data = {
                        email: this.username,
                        password: this.password
                    };
                    axios.post('/api/login', data)
                        .then((response) => {
                            localStorage.setItem('access_token', 'Bearer ' + response.data.access_token);
                            axios.get('/api/user', {headers: {"Authorization": 'Bearer ' + response.data.access_token}})
                                .then((response) => {
                                    if (response.data.admin == 1) {
                                        window.location.href = '/admin'
                                    } else if (response.data.admin == 0) {
                                        window.location.href = '/game'
                                    } else {
                                        this.serverError = true;
                                    }
                                })
                                .catch((error) => {
                                    this.serverError = true;
                                });
                        })
                        .catch((error) => {
                            this.serverError = true;
                            this.serverErrorMessage = error.response.data.msg;
                        });
                }
            },

        }
    }
</script>