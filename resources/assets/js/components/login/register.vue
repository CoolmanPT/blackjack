<template>
    <form method="post" v-on:submit="validateForm">
        <h1>Registar</h1>
        <div class="alert alert-success" role="alert" v-cloak v-show="success">
            <p>Utilizador registado com sucesso! Recebeu um email active a sua conta.</p>
        </div>
        <div class="alert alert-danger" role="alert" v-cloak v-show="isFormInvalid">
            <p v-if="missingName">Introduza o nome</p>
            <p v-if="missingEmail">Introduza o email</p>
            <p v-if="invalidEmail">Email inválido</p>
            <p v-if="missingNickname">Introduza o nome de utilizador</p>
            <p v-if="missingPassword">Introduza a password</p>
            <p v-if="invalidPassword">Password têm que ter pelo menos 6 caracteres.</p>
            <p v-if="missingPasswordConfirmation">Confirme a password</p>
            <p v-if="wrongPasswordConfirmation">Confirmação da password não corresponde a password</p>
            <p v-if="hasServerError">{{ serverErrorMessage }}</p>
        </div>
        <div>
            <input type="text" name="name" v-model="name" v-bind:class="{ 'is-invalid': missingName  }"
                   class="form-control" placeholder="Nome"/>
        </div>
        <div>
            <input type="text" name="nickname" v-model="nickname" v-bind:class="{ 'is-invalid': missingNickname || nicknameAlreadyUsed  }"
                   class="form-control" placeholder="Nome de utilizador"/>
        </div>
        <div>
            <input type="text" name="email" v-model="email"
                   v-bind:class="{ 'is-invalid': missingEmail || invalidEmail || emailAlreadyUsed  }" class="form-control"
                   placeholder="Email"/>
        </div>
        <div>
            <input type="password" name="password" autocomplete="new-password" v-model="password"
                   v-bind:class="{ 'is-invalid': missingPassword || invalidPassword }" class="form-control"
                   placeholder="Nova Password"/>
        </div>
        <div>
            <input type="password" name="passwordConfirmation" v-model="passwordConfirmation"
                   v-bind:class="{ 'is-invalid': missingPasswordConfirmation || wrongPasswordConfirmation }"
                   class="form-control" placeholder="Confirme a Password"/>
        </div>

        <button type="submit" class="btn btn-primary">Registar</button>
        <button type="button" @click="clear" class="btn btn-danger">Cancelar</button>

        <div class="clearfix"></div>

        <div class="separator">
            <p class="change_link">
                Voltar para o
                <router-link to="/login" class="to_register">Login</router-link>

            </p>
        </div>
    </form>
</template>


<script type="text/javascript">
    export default {
        data: function () {
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