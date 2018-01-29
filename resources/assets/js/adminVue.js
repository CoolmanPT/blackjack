
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 *
 *
 * npm run watch-poll
 *
 *
 */

//require('./bootstrap');


window.Vue = require('vue');

//IMPORT VUE VALIDATOR
import Vue from 'vue';
import VueRouter from 'vue-router';

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
/*
//SETTINGS COMPONENT
const settings = Vue.component('settings-component', require('./components/admin/settings.vue'));
Vue.component('email-settings-component', require('./components/admin/email_settings.vue'));
Vue.component('change-email-component', require('./components/admin/change_email.vue'));
Vue.component('change-password-component', require('./components/admin/change_password.vue'));

//USER COMPONENT
const users = Vue.component('users-component', require('./components/admin/users.vue'));

//DASHBOARD
const dashboard = Vue.component('dashboard-component', require('./components/admin/dashboard.vue'));
Vue.component('statistic-component', require('./components/admin/statistic.vue'));
Vue.component('games-perday-component', require('./components/admin/games-perday-component.vue'));
Vue.component('users-statistic-component', require('./components/admin/users_statistic.vue'));

//PIECES
const pieces = Vue.component('pieces-component', require('./components/admin/pieces.vue'));
Vue.component('pieces-upload', require('./components/admin/pieces_upload.vue'));

/*ROUTES*/

/*
const routes = [
    { path: '/', redirect: '/dashboard' },
    { path: '/dashboard', component: dashboard },
    { path: '/users', component: users },
    { path: '/pieces', component: pieces },
    { path: '/settings', component: settings },
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
}).$mount('#app');

*/