<template>
    <div class="container">
        <div class="row">
            <div class="col-md-5 col-sm-12">
                <div class="card ml-auto mr-auto mt-5">
                    <div class="card-header bg-primary">
                        <h1 class="text-center text-light">STATISTICS</h1>
                    </div>
                    <div class="card-body">
                        dsahdlasldksajdk
                    </div>
                </div>
            </div>
            <div class="col-md-5 col-sm-12">
                <div class="card ml-auto mr-auto mt-5">
                    <div class="card-header bg-primary">
                        <h1 class="text-center text-light">Login</h1>
                    </div>

                    <div class="card-body">
                        <form method="post" v-on:submit.prevent="submitForm">

                            <div class="input-group form-group">
                                <div class="input-group-prepend"><i class="input-group-text fa fa-user"></i></div>
                                <input type="text" name="username" v-model="username" v-bind:class="{ 'is-invalid': missingUsername  }" class="form-control" placeholder="Username/Email"  />
                            </div>
                            <div class="input-group form-group mb-0">
                                <div class="input-group-prepend"><i class="input-group-text fa fa-lock"></i></div>
                                <input type="password" name="password" v-model="password" v-bind:class="{ 'is-invalid': missingPassword }" class="form-control margin-bottom0" placeholder="Password"  />
                            </div>
                            <router-link to="/password/reset" class="float-right text-muted mt-0 small font-italic" >Forgot Password?</router-link>

                                <button type="submit" class="btn btn-primary btn-block mt-4">Log in</button>



                            <div class="clearfix">
                                <div class="alert alert-danger" role="alert" v-cloak v-show="isFormInvalid">
                                    <p v-if="missingUsername">Fill Username/Email</p>
                                    <p v-if="missingPassword">Fill Password</p>
                                    <p v-if="serverError">{{serverErrorMessage}}</p>
                                </div>
                            </div>


                                <p class="text-center mb-0 pb-0 mt-2">
                                    Don't have a account? <router-link to="/register" class="">Create</router-link>
                                </p>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data: function(){
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
            submitForm: function (event) {
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
                                        axios.get('/api/user', { headers: {"Authorization" : 'Bearer ' + response.data.access_token}})
                                            .then((response) => {
                                                if(response.data.admin == 1){
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
                                        this.serverErrorMessage = error.response;
                                    });
                            }
                        },
        }
    }
</script>
