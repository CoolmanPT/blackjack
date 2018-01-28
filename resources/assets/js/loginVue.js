require('./bootstrap');

window.Vue = require('vue');

import Vue from 'vue';
import VueRouter from 'vue-router';
Vue.use(VueRouter);

axios.defaults.headers.common['Accept'] = "application/json";
axios.defaults.headers.common['Authorization'] = localStorage.getItem('access_token');

/*
* COMPONENTS
* */

const login = Vue.component('login-component', require('./components/login/loginComponent.vue'));
const requestPassword = Vue.component('request-password-component', require('./components/login/requestPasswordComponent.vue'));
const resetPassword = Vue.component('password-reset-component', require('./components/login/resetPasswordComponent.vue'));
const register = Vue.component('password-reset-component', require('./components/login/registerComponent.vue'));
const activation = Vue.component('activation-component', require('./components/login/activationComponent.vue'));


/*ROUTES*/
const routes = [
    { path: '/', redirect: '/login' },
    { path: '/login', component: login },
    { path: '/password/reset', component: requestPassword },
    { path: '/password/reset/:token/:user', component: resetPassword, props: true  },
    { path: '/register', component: register },
    { path: '/activation/:token', component: activation, props: true  },
];

const router = new VueRouter({
    routes:routes
});

new Vue({
    router,

}).$mount('#login_app');