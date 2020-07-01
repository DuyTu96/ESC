// import Vue from 'vue';
// import Meta from 'vue-meta';
// import router from '~/router/Admin';
// import store from '~/store/Admin';
// import Admin from '~/components/Admin.vue';
// import BootstrapVue from 'bootstrap-vue';
// import '~/plugins/Admin';
// import { extend } from 'vee-validate';
// import * as rules from 'vee-validate/dist/rules';
// import { messages } from '~/lang/vee-validate.json';
// import ScrollLoader from 'vue-scroll-loader';
// import 'bootstrap-vue/dist/bootstrap-vue.css';
// var valid = require('card-validator');
// import { Cropper } from 'vue-advanced-cropper';

// Vue.use(Meta);
// Vue.use(BootstrapVue);
// Vue.use(ScrollLoader);

// Object.keys(rules).forEach(rule => {
//     extend(rule, {
//         ...rules[rule], // copies rule configuration
//         message: messages[rule] // assign message
//     });
// });

// extend('email', {
//     /* eslint-disable no-useless-escape */
//     validate: value => (/^[a-zA-Z0-9]+([\w\.\'\!\#\$\%\&\*\+\-\/\=\?\^\`\{\|\}\~])*([a-zA-Z0-9])+@([a-zA-Z0-9]+\.)+[a-zA-Z0-9]{2,8}$/).test(value),
//     message: '{_field_}は、有効なメールアドレス形式で指定してください。'
// });

// extend('ccn', {
//     validate: value => valid.number(value).isValid,
//     message: '無効なクレジットカード番号です。'
// });

// extend('cvc', {
//     validate: value => valid.cvv(value).isValid || valid.cvv(value, 4).isValid,
//     message: 'CVCが不正です。'
// });

// import DefaultLayout from '~/layouts/Common/DefaultLayout';
// import AdminLayout from '~/layouts/Admin/AdminLayout';
// import AdminIndexLayout from '~/layouts/Admin/AdminIndexLayout';

// Vue.component('DefaultLayout', DefaultLayout);
// Vue.component('AdminLayout', AdminLayout);
// Vue.component('AdminIndexLayout', AdminIndexLayout);
// Vue.component(Cropper);

// const vueApp = new Vue({
//     router,
//     store,
//     ...Admin
// });

// export default vueApp;
