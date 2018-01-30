


/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

import VueRouter from 'vue-router';
//import VueSocketio from 'vue-socket.io';

Vue.use(VueRouter);
//Vue.use(VueSocketio, 'http://127.0.0.1:8080');

const gameLobby = Vue.component('game-lobby-component', require('./components/frontend/gameLobbyComponent.vue'));
const statistics = Vue.component('statistics-component', require('./components/frontend/statisticsComponent.vue'));
const account = Vue.component('account-component', require('./components/frontend/accountComponent.vue'));

Vue.component('blackjack-component', require('./components/frontend/blackjackGameComponent.vue'));

const routes = [
    { path: '/', redirect: '/lobby' },
    { path: '/lobby', component: gameLobby },
    { path: '/statistics', component: statistics },
    { path: '/account', component: account }
];

const router = new VueRouter({
    routes:routes
});

axios.defaults.headers.common['Accept'] = "application/json";
axios.defaults.headers.common['Authorization'] = localStorage.getItem('access_token');

var isLoggedMixin = {
    data: {
        userName: '',
    },
    methods: {
        checkIfLogged(isLogged){
            console.log(axios.defaults.headers.common['Authorization'])
            if(axios.defaults.headers.common['Authorization'] == null){
                if(!isLogged) {
                    window.location.href = '/';
                }
            } else {
                axios.get('/api/user')
                    .then(response => {
                        if(isLogged){
                            if(response.data.admin == 1){
                                window.location.href = '/admin'
                            } else if (response.data.admin == 0) {
                                window.location.href = '/game'
                            }
                        } else {
                            this.userName = response.data.nickname;
                            console.log(response.data.name);
                        }
                    })
                    .catch(error => {
                        if(!isLogged) {
                            window.location.href = '/';
                        }
                    });
            }
        }
    }
}

const app = new Vue({
    router,
    mixins: [isLoggedMixin],
    methods: {
        logout: function () {
            axios.post('/api/logout')
                .then(response => {
                    localStorage.removeItem('access_token');
                    window.location.href = '/'
                })
                .catch(error => {
                    window.location.href = '/'
                });
        },
    },
    created: function () {
        this.checkIfLogged(false);
    },
}).$mount('#frontend');