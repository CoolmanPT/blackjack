require('./bootstrap');
window.Vue = require('vue');

//IMPORT VUE VALIDATOR
import Vue from 'vue';
import VueRouter from 'vue-router';
import axios from 'axios';

Vue.use(VueRouter);

/*****************************************
 COMPONENT TO CHECK IF IS LOGGED OR NOT
 * **************************************/

axios.defaults.headers.common['Accept'] = "application/json";
axios.defaults.headers.common['Authorization'] = localStorage.getItem('access_token');

var isLoggedMixin = {
    data: {
        nameOfUser: '',
    },  
    methods: {
        checkIfLogged(isLogged, redirectTo){
            console.log(axios.defaults.headers.common['Authorization'])
            if(axios.defaults.headers.common['Authorization'] == null){
                if(!isLogged) {
                    window.location.href = '/';
                }
            } else { //TEM UM TOKEN VAI VERIFICAR SE É VALIDO
                axios.get('/api/user')
                    .then((response) => {
                        if(isLogged){
                            if(response.data.admin == 1){
                                window.location.href = '/admin'
                            } else if (response.data.admin == 0) {
                                window.location.href = '/game'
                            }
                        } else {
                            this.nameOfUser = response.data.name;
                            console.log(response.data.name);
                        }
                    })
                    .catch((error) => {
                        if(!isLogged) {
                            window.location.href = '/';
                        }
                    });
            }
        }
    }
}

/*****************************************
 DASHBOARD, SETTINGS, USERS AND PIECES COMPONENTS
 * **************************************/
//DASHBOARD
const dashboard = Vue.component('dashboard-component', require('./components/admin/adminDashboard.vue'));
const settings = Vue.component('settings-component', require('./components/admin/settingsComponent.vue'));
Vue.component('platform-email-settings-component', require('./components/admin/platform-email_settingsComponent.vue'));
Vue.component('admin-change-email-component', require('./components/admin/admin-change_emailComponent.vue'));
Vue.component('admin-change-password-component', require('./components/admin/admin-change_passwordComponent.vue'));



//Vue.component('statistic-component', require('./components/admin/statistic.vue'));
//Vue.component('games-perday-component', require('./components/admin/games-perday-component.vue'));
//Vue.component('users-statistic-component', require('./components/admin/users_statistic.vue'));

//SETTINGS COMPONENT



//USER COMPONENT
//const users = Vue.component('users-component', require('./components/admin/users.vue'));



//PIECES
//const pieces = Vue.component('pieces-component', require('./components/admin/pieces.vue'));
//Vue.component('pieces-upload', require('./components/admin/pieces_upload.vue'));

/*ROUTES*/


const routes = [
    { path: '/', redirect: 'dashboard' },
    { path: '/dashboard', component: dashboard },
    { path: '/settings', component: settings },
    //{ path: '/users', component: users },
    //{ path: '/pieces', component: pieces },
    
];

const router = new VueRouter({
    routes:routes
});

new Vue({
    router,
    mixins: [isLoggedMixin],
    computed: {

    },
    methods: {
        logout: function () {
            axios.post('/api/logout')
                .then((response) => {
                    localStorage.removeItem('access_token');
                    window.location.href = '/'
                })
                .catch((error) => {
                    window.location.href = '/'
                });
        },
    },
    created: function () {
        this.checkIfLogged(false);
    }
}).$mount('#admin_app');

