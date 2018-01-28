<template>
    <div class="container">
        <div class="row">
            <div class="col-md-5 col-sm-12 m-auto">
                <div class="card ml-auto mr-auto mt-5">
                    <div class="card-header bg-primary">
                        <h1 class="text-center text-light">New User</h1>
                    </div>
                    <div class="card-body">
                        <form method="post" v-on:submit="submitForm">

                            <div class="form-group">
                                <input type="text" name="name" v-model="name" v-bind:class="{ 'is-invalid': missingName  }"
                                       class="form-control" placeholder="Name"/>
                            </div>
                            <div class="form-group">
                                <input type="text" name="nickname" v-model="nickname" v-bind:class="{ 'is-invalid': missingNickname || nicknameAlreadyUsed  }"
                                       class="form-control" placeholder="Username"/>
                            </div>
                            <div class="form-group">
                                <input type="text" name="email" v-model="email"
                                       v-bind:class="{ 'is-invalid': missingEmail || invalidEmail || emailAlreadyUsed  }" class="form-control"
                                       placeholder="Email"/>
                            </div>
                            <div class="form-group">
                                <input type="password" name="password" autocomplete="new-password" v-model="password"
                                       v-bind:class="{ 'is-invalid': missingPassword || invalidPassword }" class="form-control"
                                       placeholder="New Password"/>
                            </div>
                            <div class="form-group">
                                <input type="password" name="passwordConfirmation" v-model="passwordConfirmation"
                                       v-bind:class="{ 'is-invalid': missingPasswordConfirmation || wrongPasswordConfirmation }"
                                       class="form-control" placeholder="Confirm Password"/>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">Register</button>

                            <div class="clearfix">
                                <div class="alert alert-success" role="alert" v-cloak v-show="success">
                                    <p>User created with success. Check your email for a activation link</p>
                                </div>
                                <div class="alert alert-danger" role="alert" v-cloak v-show="isFormInvalid">
                                    <p v-if="missingName">Fill name</p>
                                    <p v-if="missingEmail">Fill Email</p>
                                    <p v-if="invalidEmail">Invalid Email</p>
                                    <p v-if="missingNickname">Fill Username</p>
                                    <p v-if="missingPassword">Fill Password</p>
                                    <p v-if="invalidPassword">Password must have at least 6 digits</p>
                                    <p v-if="missingPasswordConfirmation">Confirm Password</p>
                                    <p v-if="wrongPasswordConfirmation">Passwords mismatch</p>
                                    <p v-if="hasServerError">{{ serverErrorMessage }}</p>
                                </div>
                            </div>

                                <p class="text-center mb-0 pb-0 mt-2">
                                    Go Back to
                                    <router-link to="/login" class="to_register">Login</router-link>

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
                name: '',
                nickname: '',
                email: '',
                password: '',
                passwordConfirmation: '',
                attemptSubmit: false,
                serverErrorCode: 0,
                serverErrorMessage: '',
                success: false
            }
        },
        computed: {
            missingName: function () {
                return this.name.trim() === '' && !this.hasServerError && this.attemptSubmit;
            },
            missingEmail: function () {
                return this.email.trim() === '' && !this.hasServerError && this.attemptSubmit;
            },
            invalidEmail: function () {
                return !this.missingEmail && !this.validateEmail(this.email.trim()) && !this.hasServerError && this.attemptSubmit;
            },
            emailAlreadyUsed: function () {
                return this.serverErrorCode == 1;
            },
            missingNickname: function () {
                return this.nickname.trim() === '' && !this.hasServerError && this.attemptSubmit;
            },
            nicknameAlreadyUsed: function () {
                return this.serverErrorCode == 2;
            },
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
                return (this.missingName || this.missingEmail || this.missingNickname || this.missingPassword || this.invalidPassword || this.wrongPasswordConfirmation || this.missingPasswordConfirmation || this.invalidEmail);
            },
            hasServerError: function () {
                return this.serverErrorCode == -1 || this.emailAlreadyUsed || this.nicknameAlreadyUsed;
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
            validatePasswordStructure: function (password) {
                var re = /^[a-zA-Z0-9]{6,}$/;
                return re.test(password);
            },
            clear: function () {
                this.name = '';
                this.nickname = '';
                this.email = '';
                this.password = '';
                this.passwordConfirmation = '';
                this.attemptSubmit = false;
                this.serverErrorCode = 0;
            },
            submitForm: function () {
                //CLEARS SERVER ERROR'S
                this.serverErrorCode = 0;
                this.success = false;

                //PREVENT FORM
                event.preventDefault();

                //FORM SUBMITED
                this.attemptSubmit = true;

                //IF FORM IS VALID MAKE API REQUEST FOR LOGIN
                if (!this.isFormInvalid) {
                    const data = {
                        name: this.name,
                        nickname: this.nickname,
                        email: this.email,
                        password: this.password
                    };
                    axios.post('/api/register', data)
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
        }
    }
</script>
