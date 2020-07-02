import Vue from 'vue';
//variant (primary, danger, warning)
const toast = (message, variant = 'primary') => {
    const vm = new Vue({});
    return vm.$bvToast.toast(message, {
        title: '',
        noCloseButton: false,
        autoHideDelay: 2000,
        appendToast: true,
        toaster: 'b-toaster-top-center',
        variant: variant,
        noFade: false,
        solid: true
    });
};

export default toast;
