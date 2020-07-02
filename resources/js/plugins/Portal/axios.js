import axios from 'axios';
import store from '~/store/Portal';
import Vue from 'vue';
import Cookie from 'js-cookie';

// Request interceptor
axios.interceptors.request.use(request => {
    var token = store.getters['auth/PORTAL_ACCESS_TOKEN'];
    if (token) {
        request.headers.common['Accept'] = 'application/json';
        request.headers.common['Authorization'] = `Bearer ${token}`;
    }

    const locale = null;
    if (locale) {
        request.headers.common['Accept-Language'] = locale;
    }

    return request;
}, error => {
    return Promise.reject(error);
});

// Response interceptor
axios.interceptors.response.use(response => {
    // Any status code that lie within the range of 2xx cause this function to trigger
    // Do something with response data
    return response;
}, error => {
    if (error.response.status === 401
        && error.response.data.error != undefined
        && error.response.data.error.code == 4010
        && error.response.config.url.name != 'portal.login') {

        window.location = window.location.origin;
        Cookie.remove('PORTAL_ACCESS_TOKEN');
        Cookie.remove('PORTAL_USER');

        return;
    }

    // Any status codes that falls outside the range of 2xx cause this function to trigger
    // Do something with response error
    const vm = new Vue({});
    let errorMessages = ['Something went wrong'];
    if (error.response.status !== 422 && !!error.response.data && !!error.response.data.error.message && typeof error.response.data.error.message === 'string') {
        errorMessages[0] = error.response.data.error.message;
    } else if (error.response.status === 422 && !!error.response.data && !!error.response.data.error.message && typeof error.response.data.error.message === 'object') {
        errorMessages = Object.values(error.response.data.error.message);
    }

    errorMessages.forEach(errorMessage => {
        vm.$bvToast.toast(errorMessage, {
            title: '',
            noCloseButton: false,
            autoHideDelay: 2000,
            appendToast: true,
            toaster: 'b-toaster-top-center',
            variant: 'danger',
            noFade: false,
            solid: true
        });
    });

    return Promise.reject(error);
});

export default axios;
