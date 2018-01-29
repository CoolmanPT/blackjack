<template>
    <div>
        <div class="container-fluid">
            <div class="row">
                <h1 class="jumbotron">Gestão de Conta</h1>
            </div>
            <div class="row">
                <div class="col-sm-8">
                    <form method="post" v-on:submit.prevent="validateGeneralInfoForm" class="form-horizontal">
                        <h2>Dados da conta</h2>

                        <div class="form-group">
                            <label class="col-sm-12" for="name">Nome *</label>
                            <div class="col-sm-12">
                                <input type="text" name="name" id="name" v-model="name" class="form-control"/>
                            </div>
                            <div class="alert alert-danger" role="alert" v-show="isNameEmpty">
                                <p>Introduza o nome</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-12" for="nickname">Username *</label>
                            <div class="col-sm-12">
                                <input type="text" name="nickname" id="nickname" v-model="nickname" class="form-control"/>
                            </div>
                            <div class="alert alert-danger" role="alert" v-show="isNicknameEmpty">
                                <p>Introduza o nome de utilizador</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-12" for="email">Email *</label>
                            <div class="col-sm-12">
                                <input type="text" name="email" id="email" v-model="email" class="form-control"/>
                            </div>
                            <div class="alert alert-danger" role="alert" v-show="isEmailEmpty || isEmailInvalid">
                                <p v-if="isEmailEmpty">Introduza o email</p>
                                <p v-if="isEmailInvalid">Email inválido</p>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <button type="button" @click="getData" class="btn btn-danger">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                        <br/>
                        <div class="clearfix"></div>

                        <div class="alert alert-success" role="alert" v-cloak v-show="successGeneralInfo">
                            <p>{{ successMessageInfoForm }}</p>
                        </div>

                        <div class="alert alert-danger" role="alert" v-cloak v-show="hasServerErrorGeneral">
                            <p>{{ serverErrorMessage }}</p>
                        </div>

                    </form>
                </div>
                <div class="col-sm-4">
                    <h2>Alterar Password</h2>

                    <form id="formChangePassword" class="form-horizontal" method="post" v-on:submit.prevent="validatePasswordForm">
                        <div class="form-group">
                            <label class="col-sm-12" for="currentPassword">Password Atual *</label>
                            <div class="col-sm-12">
                                <input type="password" name="currentPassword" id="currentPassword" v-model="currentPassword" class="form-control col-xs-12">
                            </div>
                            <div class="alert alert-danger" role="alert" v-cloak v-show="isCurrentPasswordEmpty || isCurrentPasswordIncorrect">
                                <p class="help-block" v-if="isCurrentPasswordEmpty">Introduza a password actual</p>
                                <p class="help-block" v-if="isCurrentPasswordIncorrect">{{ serverErrorMessage }}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-12" for="newPassword">Nova Password *</label>
                            <div class="col-sm-12">
                                <input type="password" name="newPassword" id="newPassword" v-model="newPassword" class="form-control col-xs-12">
                            </div>
                            <div class="alert alert-danger" role="alert" v-show="isNewPasswordEmpty || isNewPasswordIncorrect">
                                <p class="help-block" v-if="isNewPasswordEmpty">Introduza a nova password</p>
                                <p class="help-block" v-if="isNewPasswordIncorrect">Password têm que ter pelo menos 6 caracteres.</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-12" for="password_confirmation">Confirme a Nova Password *</label>
                            <div class="col-sm-12">
                                <input type="password" name="password_confirmation" id="password_confirmation" v-model="passwordConfirmation" class="form-control col-xs-12">
                            </div>
                            <div class="alert alert-danger" role="alert" v-show="isPasswordConfirmationEmpty || isPasswordConfirmationIncorrect">
                                <p class="help-block" v-if="isPasswordConfirmationEmpty">Confirme a password</p>
                                <p class="help-block" v-if="isPasswordConfirmationIncorrect">Confirmação da password não corresponde a nova password.</p></div>
                        </div>

                        <div class="col-sm-12">
                            <button class="btn btn-danger" type="button" v-on:click="cancel">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                        <br/>
                        <div class="clearfix"></div>

                        <div class="alert alert-success" role="alert" v-cloak v-show="successPassword">
                            <p>{{ successMessagePassForm }}</p>
                        </div>
                        <div class="alert alert-danger" role="alert" v-cloak v-show="isPasswordFormInvalid">
                            <p v-if="hasClientErrorPassForm"><strong>Erro:</strong> Reveja o formulário.</p>
                            <p v-if="hasServerErrorPassForm">{{ serverErrorMessage }}</p>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <h2>Apagar conta?</h2>
                    <button type="button" @click="deleteAccount" class="btn btn-danger">Apagar conta</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script type="text/javascript">
    export default {
        data: function () {
            return {
                name: '',
                nickname: '',
                email: '',
                currentPassword: '',
                newPassword: '',
                passwordConfirmation: '',
                submitAttemptedGeneral: false,
                submitAttemptedPassForm: false,
                serverErrorPass: 0,
                serverErrorGeneralInfo: 0,
                serverErrorMessage: '',
                successPassword: false,
                successGeneralInfo: false,
                successMessageInfoForm: '',
                successMessagePassForm: '',
            }
        },
        computed: {
            // General Info Form _________________
            isNameEmpty: function () {
                return this.name.trim() === ''
                    && this.submitAttemptedGeneral
                    && !this.hasServerErrorGeneral;
            },
            isEmailEmpty: function () {
                return this.email.trim() === ''
                    && this.submitAttemptedGeneral
                    && !this.hasServerErrorGeneral;
            },
            isEmailInvalid: function () {
                return !this.isEmailEmpty
                    && !this.validateEmail(this.email.trim())
                    && !this.hasServerErrorGeneral
                    && this.submitAttemptedGeneral;
            },
            isEmailAlreadyInUse: function () {
                return this.serverErrorGeneralInfo == 2;
            },
            isNicknameEmpty: function () {
                return this.nickname.trim() === ''
                    && this.submitAttemptedGeneral
                    && !this.hasServerErrorGeneral;
            },
            isNicknameAlreadyInUse: function () {
                return this.serverErrorGeneralInfo == 3;
            },
            hasClientErrorGeneral: function () {
                return this.isNameEmpty
                    || this.isEmailEmpty
                    || this.isNicknameEmpty
                    || this.isPasswordEmpty
                    || this.isPasswordInvalid
                    || this.isPasswordConfirmationIncorrect
                    || this.isPasswordConfirmationEmpty
                    || this.isEmailInvalid;
            },
            hasServerErrorGeneral: function () {
                return this.serverErrorGeneralInfo == 1
                    || this.isEmailAlreadyInUse
                    || this.isNicknameAlreadyInUse;
            },
            isGeneralInfoFormInvalid: function () {
                return (this.hasClientErrorGeneral || this.hasServerErrorGeneral)
                    && this.submitAttemptedGeneral;
            },
            // Password Form _______________________
            isCurrentPasswordEmpty: function () {
                return this.currentPassword.trim() === ''
                    && this.submitAttemptedPassForm
                    && !this.hasServerErrorPassForm;
            },
            isNewPasswordEmpty: function () {
                return this.newPassword.trim() === ''
                    && this.submitAttemptedPassForm
                    && !this.hasServerErrorPassForm ;
            },
            isNewPasswordIncorrect: function () {
                return !this.isNewPasswordEmpty
                    && !this.validatePasswordStructure(this.newPassword.trim())
                    && !this.hasServerErrorPassForm
                    && this.submitAttemptedPassForm;
            },
            isPasswordConfirmationEmpty: function () {
                return !this.isNewPasswordEmpty
                    && !this.isNewPasswordIncorrect
                    && !this.hasServerErrorPassForm
                    && this.passwordConfirmation.trim() === ''
                    && this.submitAttemptedPassForm;
            },
            isPasswordConfirmationIncorrect: function () {
                return !this.isNewPasswordEmpty
                    && !this.isNewPasswordIncorrect
                    && !this.isPasswordConfirmationEmpty
                    && !this.hasServerErrorPassForm
                    && (this.passwordConfirmation.trim() != this.newPassword.trim())
                    && this.submitAttemptedPassForm;
            },
            isCurrentPasswordIncorrect: function () {
                return this.serverErrorPass == 2;
            },
            hasClientErrorPassForm: function () {
                return this.isCurrentPasswordEmpty
                    || this.isNewPasswordEmpty
                    || this.isNewPasswordIncorrect
                    || this.isPasswordConfirmationIncorrect
                    || this.isPasswordConfirmationEmpty;
            },
            hasServerErrorPassForm: function () {
                return this.serverErrorPass == 1
                    || this.isCurrentPasswordIncorrect;
            },
            isPasswordFormInvalid: function () {
                return (this.hasClientErrorPassForm || this.hasServerErrorPassForm)
                    && this.submitAttemptedPassForm;
            },
        },
        methods: {
            // General Info Form ________________
            validateEmail: function (email) {
                var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                return re.test(email);
            },
            validateGeneralInfoForm: function () {
                this.serverErrorGeneralInfo = 0;
                this.successGeneralInfo = false;
                this.submitAttemptedGeneral = true;

                if (!this.isGeneralInfoFormInvalid) {
                    axios.post('/api/user/update',
                        {name: this.name,nickname: this.nickname,email: this.email})
                        .then(response => {
                            this.successGeneralInfo = true;
                            this.successMessageInfoForm = response.data.message;
                            this.submitAttemptedGeneral = false;
                        })
                        .catch(error => {
                            this.serverErrorGeneralInfo = error.response.data.error;
                            this.serverErrorMessage = error.response.data.message;
                        });
                }
            },
            getData: function () {
                this.successGeneralInfo = false;
                this.serverErrorGeneralInfo = 0;
                this.submitAttemptedGeneral = false;

                axios.get('/api/user')
                    .then(response => {
                        //this.avatar = 'img/avatars/' + response.data.avatar;
                        this.name = response.data.name;
                        this.nickname = response.data.nickname;
                        this.email = response.data.email;
                    });
            },
            // Password Form _____________
            validatePasswordStructure: function (password) {
                var re = /^[a-zA-Z0-9]{6,}$/;
                return re.test(password);
            },
            validatePasswordForm: function () {
                this.serverErrorPass = 0;
                this.successPassword = false;
                this.submitAttemptedPassForm = true;

                if (!this.isPasswordFormInvalid) {
                    axios.post('api/user/password/update',
                        {currentPassword: this.currentPassword,newPassword: this.newPassword})
                        .then(response => {
                            this.successPassword = true;
                            this.successMessagePassForm = response.data.message;
                            this.cancel();
                        })
                        .catch(error => {
                            this.serverErrorPass = error.response.data.error;
                            this.serverErrorMessage = error.response.data.message;
                        });
                }
            },
            cancel: function () {
                this.submitAttemptedPassForm = false;
                this.serverErrorPass = 0;
                this.currentPassword = '';
                this.newPassword = '';
                this.passwordConfirmation = '';
            },
            // Delete User Account __________
            deleteAccount: function(){
                axios.delete('api/user/deleteaccount')
                    .then(response => {
                        window.location.href = '/';
                    });
            },
        },
        created: function () {
            this.getData();
        }
    }
</script>