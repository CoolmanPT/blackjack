<template>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <user-list :users="users"></user-list>

            </div>
        </div>

    </div>
</template>

<script type="text/javascript">
    import UserList from './userListComponent.vue';

    //import changeUserState from './changeUserStateComponent.vue';

    export default {
        data: function () {
            return {
                title: 'List Users',
                showSuccess: false,
                successMessage: '',
                currentUser: null,
                users: [],
            }
        },
        methods: {
            blockUser: function (user) {
                this.currentUser = user;
                this.showSuccess = false;
            },
            deleteUser: function (user) {
                axios.delete('api/users/' + user.id)
                    .then(response => {
                        this.showSuccess = true;
                        this.successMessage = 'User Deleted';
                        this.getUsers();
                    });
            },
            savedUser: function () {
                this.currentUser = null;
                this.$refs.usersListRef.editingUser = null;
                this.showSuccess = true;
                this.successMessage = 'User Saved';
            },
            cancelEdit: function () {
                this.currentUser = null;
                this.$refs.usersListRef.editingUser = null;
                this.showSuccess = false;
            },
            getUsers: function () {
                axios.get('api/users')
                    .then(response => {
                        this.users = response.data.data;
                    });
            },
            childMessage: function (message) {
                this.showSuccess = true;
                this.successMessage = message;
            }
        },
        components: {
            'user-list': UserList,
            //'user-change': changeUserState
        },
        mounted() {
            this.getUsers();

        }

    }
</script>
