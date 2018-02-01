<template>
    <div class="container">
    	<div class="row pt-3">
    		<div class="col-sm-12 col-md-6">
    			                    <form id="formEmail" class="" method="post" v-on:submit.prevent="validateForm">
                        <div class="form-group" v-bind:class="{ 'has-error': missingEmail || invalidEmail  }">
                            <label class="" for="admin_email">Administrator Email <span class="required">*</span>
                            </label>
                            <div class="">
                                <input  type="text" name="admin_email" id="admin_email" v-model="email" class="form-control">
                                <span class="help-block" v-if="missingEmail">Fill Email</span>
                                <span class="help-block" v-if="invalidEmail">Invalid Email</span>
                                <span class="help-block" v-if="emailAlreadyUsed">{{ serverErrorMessage }}</span>
                            </div>
                        </div>
                        <div class="ln_solid"></div>
                        <div class="form-group">
                            <div class="">
                                <button class="btn btn-primary" type="button" v-on:click="cancel">Cancel</button>
                                <button type="submit" class="btn btn-success">Save</button>
                            </div>
                        </div>
                    </form>
    		</div>
    	</div>
    </div>
</template>

<script type="text/javascript">
    export default {
        data: function(){
            return {
                email: '',
                attemptSubmit: false,
                serverErrorCode: 0,
                serverErrorMessage: '',
                success: false,
                options: [
                    { text: 'SSL', value: 'ssl' },
                    { text: 'TLS', value: 'tls' },
                    { text: 'Nenhuma', value: '' }
                ]
            }
        },
        computed: {
            missingEmail: function () {
                return this.email.trim() === '' && !this.hasServerError && this.attemptSubmit;
            },
            invalidEmail: function () {
                return !this.missingEmail && !this.validateEmail(this.email.trim()) && !this.hasServerError && this.attemptSubmit;
            },
            emailAlreadyUsed: function () {
                return this.serverErrorCode == 1;
            },
            hasClientError: function () {
                return (this.missingEmail || this.invalidEmail);
            },
            hasServerError: function () {
                return this.serverErrorCode == -1 || this.emailAlreadyUsed;
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
                this.serverErrorCode = 0;
                this.success = false;

                //PREVENT FORM
                event.preventDefault();

                //FORM SUBMITED
                this.attemptSubmit = true;

                //IF FORM IS VALID MAKE API REQUEST FOR LOGIN
                if (!this.isFormInvalid) {
                    const data = {
                        email:  this.email,
                    };
                    axios.post('api/user/email/update', data)
                        .then((response) => {
                            this.success = true;
                            this.attemptSubmit = false;
                        })
                        .catch((error) => {
                            this.serverErrorCode = error.response.data.errorCode;
                            this.serverErrorMessage = error.response.data.msg;
                        });
                }
            },
            getUserSettings: function () {
                axios.get('/api/user')
                    .then(response=>{
                        this.email = response.data.email;
                    });
            },
            cancel: function () {
                this.attemptSubmit = false;
                this.serverErrorCode = 0;
                this.serverErrorMessage = '';
                this.getUserSettings();
            }
        },
        created: function () {
            this.getUserSettings();
        }
    }
</script>