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
                            <div class="input-group form-group">
                                <div class="input-group-prepend"><i class="input-group-text fa fa-envelope"></i></div>
                                <input type="text" name="email" v-model="email" v-bind:class="{ 'is-invalid': missingEmail || invalidEmail  }" class="form-control" placeholder="Email"  />
                            </div>
                            <button type="submit" class="btn btn-primary btn-block mt-4">Recover</button>

                            <div class="clearfix">
                                <div class="alert alert-success" role="alert" v-cloak v-show="success">
                                    <p>Email containing the link to recover the password has been sent!</p>
                                </div>
                                <div class="alert alert-danger" role="alert" v-cloak v-show="isFormInvalid">
                                    <p v-if="missingEmail">Fill Email</p>
                                    <p v-if="invalidEmail">Invalid Email</p>
                                    <p v-if="serverError">{{ serverErrorMessage }}</p>
                                </div>
                            </div>

                                <p class="text-center mb-0 pb-0 mt-2">Return to <router-link to="/login" class="">login</router-link>
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
                email: '',
                attemptSubmit: false,
                serverError: false,
                serverErrorMessage: '',
                success: false
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
