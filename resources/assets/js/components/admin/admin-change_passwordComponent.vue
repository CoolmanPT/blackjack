<template>
<div class="container">
	<div class="row pt-3">
		<div class="col-sm-12 col-md-6">
			           <form id="formChangePassword" class="form-horizontal form-label-left" method="post" v-on:submit="validateForm">
                        <div class="form-group" v-bind:class="{ 'has-error': missingCurrentPassword || wrongCurrentPassword }" >
                            <label class="control-label" for="currentPassword">Password <span class="required">*</span>
                            </label>
                            <div class="">
                                <input type="password" name="currentPassword" id="currentPassword" v-model="currentPassword" class="form-control">
                                <span  class="help-block" v-if="missingCurrentPassword">Introduza a password actual</span>
                                <span class="help-block" v-if="wrongCurrentPassword">{{ serverErrorMessage }}</span>
                            </div>
                        </div>
                        <div class="form-group" v-bind:class="{ 'has-error': missingNewPassword || invalidNewPassword  }" >
                            <label class="control-label" for="newPassword">Nova Password <span class="required">*</span>
                            </label>
                            <div class="">
                                <input type="password" name="newPassword" id="newPassword" v-model="newPassword" class="form-control">
                                <span  class="help-block" v-if="missingNewPassword">Introduza a nova password</span>
                                <span  class="help-block" v-if="invalidNewPassword">Password têm que ter pelo menos 6 caracteres.</span>
                            </div>
                        </div>
                        <div class="form-group" v-bind:class="{ 'has-error': missingPasswordConfirmation || wrongPasswordConfirmation  }" >
                            <label class="control-label" for="password_confirmation">Confirme a Nova Password <span class="required">*</span></label>
                            <div class="">
                                <input type="password" name="password_confirmation" id="password_confirmation" v-model="passwordConfirmation" class="form-control">
                                <span  class="help-block" v-if="missingPasswordConfirmation">Confirme a password</span>
                                <span  class="help-block" v-if="wrongPasswordConfirmation">Confirmação da password não corresponde a nova password.</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="">
                                <button class="btn btn-primary" type="button" v-on:click="cancel">Cancelar</button>
                                <button type="submit" class="btn btn-success">Guardar</button>
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
                currentPassword: '',
                newPassword: '',
                passwordConfirmation: '',
                attemptSubmit: false,
                serverErrorCode: 0,
                serverErrorMessage: '',
                success: false
            }
        },
        computed: {
            missingCurrentPassword: function () {
                return this.currentPassword.trim() === '' && !this.hasServerError && this.attemptSubmit;
            },
            missingNewPassword: function () {
                return this.newPassword.trim() === '' && !this.hasServerError && this.attemptSubmit;
            },
            invalidNewPassword: function () {
                return !this.missingNewPassword && !this.validatePasswordStructure(this.newPassword.trim()) && !this.hasServerError && this.attemptSubmit;
            },
            missingPasswordConfirmation: function () {
                return !this.missingNewPassword && !this.invalidNewPassword && this.passwordConfirmation.trim() === '' && !this.hasServerError && this.attemptSubmit;
            },
            wrongPasswordConfirmation: function () {
                return !this.missingNewPassword && !this.invalidNewPassword && !this.missingPasswordConfirmation && (this.passwordConfirmation.trim() != this.newPassword.trim()) && !this.hasServerError && this.attemptSubmit;
            },
            wrongCurrentPassword: function () {
                return this.serverErrorCode == 1;
            },
            hasClientError: function () {
                return (this.missingCurrentPassword || this.missingNewPassword || this.invalidNewPassword || this.wrongPasswordConfirmation || this.missingPasswordConfirmation);
            },
            hasServerError: function () {
                return this.serverErrorCode == -1 || this.wrongCurrentPassword;
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
                this.serverErrorCode = 0;
                this.success = false;

                //PREVENT FORM
                event.preventDefault();

                //FORM SUBMITED
                this.attemptSubmit = true;

                //IF FORM IS VALID MAKE API REQUEST FOR LOGIN
                if (!this.isFormInvalid) {

                    const data = {
                        currentPassword: this.currentPassword,
                        newPassword: this.newPassword,
                    };
                    axios.post('api/user/password/update', data)
                        .then((response) => {

                            this.success = true;
                            this.cancel();

                        })
                        .catch((error) => {
                            this.serverErrorCode = error.response.data.errorCode;
                            this.serverErrorMessage = error.response.data.msg;
                        });
                }
            },
            cancel: function () {
                this.attemptSubmit = false;
                this.serverErrorCode = 0;
                this.currentPassword = '';
                this.newPassword = '';
                this.passwordConfirmation = '';
            },
        }
    }
</script>