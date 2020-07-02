import Vue from 'vue';
import Meta from 'vue-meta';
import router from '~/router/Portal';
import store from '~/store/Portal';
import Portal from '~/components/Portal.vue';
import BootstrapVue from 'bootstrap-vue';
import plugins from '~/plugins/Portal';
import {extend} from 'vee-validate';
import * as rules from 'vee-validate/dist/rules';
import {messages} from '~/lang/vee-validate.json';
import CoreUiVue from '@coreui/vue';
import {iconsSet as icons} from './assets/icons/icons.js';
import i18n from '~/plugins/i18n';

Vue.use(BootstrapVue);
Vue.use(CoreUiVue);
Vue.use(Meta);
Vue.use(plugins);

Object.keys(rules).forEach(rule => {
    extend(rule, {
        ...rules[rule], // copies rule configuration
        message: messages[rule] // assign message
    });
});

import PortalLayout from '~/layouts/Portal/PortalLayout.vue';
import DefaultLayout from '~/layouts/Portal/DefaultLayout.vue';

Vue.component('PortalLayout', PortalLayout);
Vue.component('DefaultLayout', DefaultLayout);

const vueApp = new Vue({
    i18n,
    router,
    store,
    icons,
    ...Portal
});

export default vueApp;
