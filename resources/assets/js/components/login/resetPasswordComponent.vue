<template>
    <div class="container">
        <div class="row">
            <div class="col-md-5 col-sm-12 m-auto">
                <div class="card ml-auto mr-auto mt-5">
                    <div class="card-header bg-primary">
                        <h1 class="text-center text-light">Recover Password</h1>
                    </div>
                    <div class="card-body">
                        <form method="post" v-on:submit="submitForm">
                            <div class="form-group input-group">
                                <input type="password" name="password" v-model="password" v-bind:class="{ 'is-invalid': missingPassword || invalidPassword }" class="form-control" placeholder="New password"  />
                            </div>
                            <div class="form-group input-group">
                                <input type="password" name="passwordConfirmation" v-model="passwordConfirmation" v-bind:class="{ 'is-invalid': missingPasswordConfirmation || wrongPasswordConfirmation }" class="form-control" placeholder="Confirm Password"  />
                            </div>
                            <button type="submit" class="btn btn-primary btn-block mt-4">Change</button>



                            <div class="clearfix">
                                <div class="alert alert-success" role="alert" v-cloak v-show="success">
                                    <p>Password Changed</p>
                                </div>
                                <div class="alert alert-danger" role="alert" v-cloak v-show="isFormInvalid">
                                    <p v-if="missingPassword">Fill Password</p>
                                    <p v-if="invalidPassword">Password must have ate least 6 digits</p>
                                    <p v-if="missingPasswordConfirmation">Confirm Password</p>
                                    <p v-if="wrongPasswordConfirmation">Passwords mismatch</p>
                                    <p v-if="serverError">{{ serverErrorMessage }}</p>
                                </div>
                            </div>
                            <p class="text-center mb-0 pb-0 mt-2">Go back to
                                <router-link to="/login" class="to_register">login</router-link>
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
        props: ['token','user'],
        data: function(){
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
            submitForm: function () {
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
                            this.serverErrorMessage = error.response.data.msg;
                        });

                }
            },
        }
    }
</script>
