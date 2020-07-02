// import Vue from 'vue';
// import Meta from 'vue-meta';
// import router from '~/router/User';
// import store from '~/store/User';
// import User from '~/components/User.vue';
// import BootstrapVue from 'bootstrap-vue';
// import '~/plugins/User';
// import { extend } from 'vee-validate';
// import * as rules from 'vee-validate/dist/rules';
// import { messages } from '~/lang/vee-validate.json';
// import 'bootstrap-vue/dist/bootstrap-vue.css';
// import VueScrollTo from 'vue-scrollto';
// import ScrollLoader from 'vue-scroll-loader';
// import TextHighlight from 'vue-text-highlight';
// import { Cropper } from 'vue-advanced-cropper';
// import VueHtml2Canvas from 'vue-html2canvas';

// Vue.use(Meta);
// Vue.use(BootstrapVue);
// Vue.use(VueScrollTo);
// Vue.use(ScrollLoader);
// Vue.use(VueHtml2Canvas);

// Object.keys(rules).forEach(rule => {
//     extend(rule, {
//         ...rules[rule], // copies rule configuration
//         message: messages[rule] // assign message
//     });
// });

// extend('email', {
//      eslint-disable no-useless-escape 
//     validate: value => (/^[a-zA-Z0-9]+([\w\.\'\!\#\$\%\&\*\+\-\/\=\?\^\`\{\|\}\~])*([a-zA-Z0-9])+@([a-zA-Z0-9]+\.)+[a-zA-Z0-9]{2,8}$/).test(value),
//     message: '{_field_}は、有効なメールアドレス形式で指定してください。'
// });

// import UserLayoutBefore from '~/layouts/User/UserLayoutBefore';
// import UserLayout from '~/layouts/User/UserLayout';
// import UserIndexLayout from '~/layouts/User/UserIndexLayout';
// import DefaultLayout from '~/layouts/Common/DefaultLayout';
// import UserHideSidebarLayout from '~/layouts/User/UserHideSidebarLayout';

// Vue.component('UserLayoutBefore', UserLayoutBefore);
// Vue.component('UserLayout', UserLayout);
// Vue.component('DefaultLayout', DefaultLayout);
// Vue.component('UserIndexLayout', UserIndexLayout);
// Vue.component('UserHideSidebarLayout', UserHideSidebarLayout);
// Vue.component('text-highlight', TextHighlight);
// Vue.component(Cropper);

// const vueApp = new Vue({
//     router,
//     store,
//     ...User
// });

// export default vueApp;
