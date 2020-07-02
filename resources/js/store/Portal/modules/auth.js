import axios from 'axios';
import Cookies from 'js-cookie';
import * as types from '../mutation-types';

export const state = {
    loginRes: null,
    forgotRes: null,
    resetRes: null,
    PORTAL_USER : Cookies.get('PORTAL_USER'),
    PORTAL_ACCESS_TOKEN: Cookies.get('PORTAL_ACCESS_TOKEN')
};

// getters
export const getters = {
    loginRes: state => state.loginRes,
    forgotRes: state => state.forgotRes,
    PORTAL_USER : state => state.PORTAL_USER,
    PORTAL_ACCESS_TOKEN: state => state.PORTAL_ACCESS_TOKEN
};

// mutations
export const mutations = {
    [types.AUTH.AUTH_LOGIN](state, res) {
        state.loginRes = res.data;
        if (res.status === 200) {
            const data = res.data.data;
            state.PORTAL_ACCESS_TOKEN = data.token;
            Cookies.set('PORTAL_ACCESS_TOKEN', data.token, {expires: data.expires_in / 86400});
        }
    },

    [types.AUTH.FETCH_AUTH ](state, res) {
        if (!res.error) {
            state.PORTAL_USER = res.data;
            Cookies.set('PORTAL_USER', res.data, {expires: res.data.expires_in / 86400 });
        }
    },

    [types.AUTH.AUTH_LOGOUT ](state, res) {
        if (!res.err) {
            Cookies.remove('PORTAL_ACCESS_TOKEN');
            Cookies.remove('PORTAL_USER');
        }
    },

    [types.AUTH.AUTH_FORGOT_PASSWORD_REQUEST](state, res) {
        state.forgotRes = res;
    },

    [types.AUTH.AUTH_FORGOT_PASSWORD_RESET](state, res) {
        state.resetRes = res;
    },
};

// actions
export const actions = {
    async login({commit}, params) {
        const response = await axios.post(route('portal.login'), params);
        commit(types.AUTH.AUTH_LOGIN, response);
    },

    async fetchUser({commit}) {
        const response = await axios.get(route('portal.auth-user'));
        commit(types.AUTH.FETCH_AUTH , response);
    },

    async logout({commit}) {
        const response = await axios.post(route('portal.logout'));
        commit(types.AUTH.AUTH_LOGOUT, response);
    },

    async forgotRequest({commit}, params) {
        var response = await axios.post(route('portal.password.request'), params);
        commit(types.AUTH.AUTH_FORGOT_PASSWORD_REQUEST, response);
    },

    async forgotReset({commit}, params) {
        var response = await axios.post(route('portal.password.reset'), params);
        commit(types.AUTH.AUTH_FORGOT_PASSWORD_RESET, response);
    }
};
