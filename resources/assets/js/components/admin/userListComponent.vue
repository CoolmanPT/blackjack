<template>
    <div>
        <h1 class="mt-2">List of Users</h1>
        <hr>
        <div class="alert alert-success" role="alert" v-if="showSuccess">
            <p>{{successMessage}}</p>
        </div>
        <table class="table table-hover table-striped border-0 mt-5">
            <thead>
            <tr>
                <th class="bg-dark">Name</th>
                <th class="bg-dark">Email</th>
                <th class="bg-dark">Nickname</th>
                <th class="bg-dark">Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="user in users" :key="user.id" :class="{}">
                <td>{{ user.name }}</td>
                <td>{{ user.email }}</td>
                <td>{{ user.nickname }}</td>
                <td>
                    <b-button @click="changeUserState(user)" v-if="user.blocked === 0">
                        Block User
                    </b-button>
                    <b-button @click="changeUserState(user)" v-else>
                        Unblock User
                    </b-button>
                    <a class="btn btn-xs btn-danger" v-on:click.prevent="deleteUser(user)">Delete User</a>
                </td>
            </tr>
            </tbody>
        </table>
        <b-modal id="blockModal"
                 ref="blockModal"
                 title="Block user"
                 @ok="saveState"
                 @shown="cancel">
            <form @submit.stop.prevent="handleSubmit">
                <b-form-textarea :rows="3"
                                 :max-rows="6"
                                 :no-resize=true

                              v-model="reason" placeholder="Reason:"></b-form-textarea>
            </form>
        </b-modal>
        <b-modal id="unblockModal"
                 ref="unblockModal"
                 title="Unblock user"
                 @ok="saveState"
                 @shown="cancel">
            <form @submit.stop.prevent="handleSubmit">
                <b-form-textarea :rows="3"
                                 :max-rows="6"
                                 :no-resize=true

                              v-model="reason" placeholder="Reason:"></b-form-textarea>
            </form>
        </b-modal>
        <b-modal id="deleteModal"
                 ref="deleteModal"
                 title="Delete user"
                 @ok="deleteUserConfirm"
                 @shown="cancel">
            <form @submit.stop.prevent="handleSubmit">
                <b-form-textarea :rows="3"
                              :max-rows="6"
                              :no-resize=true

                              v-model="reason" placeholder="Reason:"></b-form-textarea>
            </form>
        </b-modal>
    </div>

</template>

<script type="text/javascript">
    // Component code (not registered)

    module.exports = {
        props: ['users'],
        data: function () {
            return {
                name: '',
                reason: '',
                user: '',
                state: '',
                successMessage: '',
                showSuccess: false,
            }
        },
        components: {

        },
        methods: {
            changeUserState(user) {
                this.user = user;
                if (this.user.blocked === 0) {
                    this.$refs.blockModal.show(user);
                }
                else {
                    this.$refs.unblockModal.show(user);
                }

            },
            deleteUser: function (user) {
                this.user = user;
                this.$refs.deleteModal.show(user);

            },
            definePlayer: function (user, player) {
                this.$root.$data['player' + player] = user;
                this.$emit('message', user.name + ' selected as Player' + player);
            },
            cancel() {
                this.name = ''
            },
            saveState(evt) {
                // Prevent modal from closing
                evt.preventDefault()
                if (!this.reason) {
                    alert('Please enter a reason')
                } else {
                    let user = this.user;
                    const data = {
                        reason: this.reason,
                        state: user.blocked,
                    };
                    console.log(data);
                    axios.put('api/users/' + user.id, data)
                        .then(response => {
                            this.$parent.getUsers();
                            this.showSuccess = true;
                            this.successMessage = "user changed!";
                            this.$parent.getUsers();
                        });
                    this.handleSubmit()
                }
            },
            deleteUserConfirm(evt) {
                evt.preventDefault();
                if (!this.reason) {
                    alert('Please enter a reason')
                } else {
                    console.log(this.reason);
                    let user = this.user;

                    axios.delete('api/users/' + user.id + '/' + this.reason)
                        .then(response => {
                            this.$parent.getUsers();
                            this.showSuccess = true;
                            this.successMessage = "user deleted!";

                        });

                this.handleSubmit()
            }
        },
        handleSubmit() {
            this.$refs.blockModal.hide();
            this.$refs.unblockModal.hide();
            this.$refs.deleteModal.hide();


        },
    },
    }
</script>