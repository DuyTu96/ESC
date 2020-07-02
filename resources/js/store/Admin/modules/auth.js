import axios from 'axios';
import Cookies from 'js-cookie';
import * as types from '../mutation-types';

// state
export const state = {
    loginRes: null,
    forgotRes: null,
    registerUserRes: null,
    confirmUserRes: null,
    changePasswordRes: null,
    changeEmailRequestRes: {},
    changeEmailRes: {},
    changeEmailSuccess: false,
    getUserAuthByTokenRes: false,
    user: [],
    ADMIN_USER : Cookies.get('ADMIN_USER'),
    ADMIN_ACCESS_TOKEN: Cookies.get('ADMIN_ACCESS_TOKEN'),
    ADMIN_AUTH_EXPIRE: Cookies.get('ADMIN_AUTH_EXPIRE'),
    USER_BY_ID: Cookies.get('USER_BY_ID')
};

// getters
export const getters = {
    loginRes: state => state.loginRes,
    forgotRes: state => state.forgotRes,
    authExpiresTime: state => state.authExpiresTime,
    checkTokenRes: state => state.checkTokenRes,
    confirmUserRes: state => state.confirmUserRes,
    changePasswordRes: state => state.changePasswordRes,
    changeEmailRes: state => state.changeEmailRes,
    user: state => state.user,
    getUserAuthByTokenRes: state => state.getUserAuthByTokenRes,
    ADMIN_USER : state => state.ADMIN_USER,
    ADMIN_ACCESS_TOKEN: state => state.ADMIN_ACCESS_TOKEN,
    USER_BY_ID: state => state.USER_BY_ID
};

// mutations
export const mutations = {
    [types.AUTH_LOGIN](state, res) {
        state.loginRes = res;
        if (!res.error && res.data.token !== undefined) {
            state.ADMIN_ACCESS_TOKEN = res.data.token;
            if (!res.data.remember_me) {
                Cookies.set('ADMIN_ACCESS_TOKEN', res.data.token);
            } else {
                Cookies.set('ADMIN_ACCESS_TOKEN', res.data.token, {expires: res.data.expires_in / 86400});
                Cookies.set('ADMIN_AUTH_EXPIRE', res.data.expires_in / 86400, {expires: res.data.expires_in / 86400});
            }
        }
    },

    [types.FETCH_AUTH ](state, res) {
        if (!res.error) {
            state.ADMIN_USER = res.data;
            state.user = res.data;
            if (!Cookies.get('ADMIN_AUTH_EXPIRE')) {
                Cookies.set('ADMIN_USER', res.data);
            } else {
                Cookies.set('ADMIN_USER', res.data, {expires: JSON.parse(Cookies.get('ADMIN_AUTH_EXPIRE'))});
            }
        }
    },

    [types.AUTH_LOGOUT ](state, res) {
        if (!res.error) {
            Cookies.remove('ADMIN_ACCESS_TOKEN');
            Cookies.remove('ADMIN_USER');
        }
    },

    [types.AUTH_CHECK_TOKEN](state, res) {
        state.checkTokenRes = res;
    },

    [types.AUTH_FORGOT_PASSWORD_REQUEST](state, res) {
        state.forgotRes = res;
    },

    [types.AUTH_FORGOT_PASSWORD_RESET](state, res) {
        state.forgotRes = res;
    },

    [types.GET_USER_AUTH_BY_ID](state, res) {
        state.getUserAuthByTokenRes = res;
        if (!res.error) {
            state.USER_BY_ID = res.data;
            Cookies.set('USER_BY_ID', res.data);
        }
    },

    [types.AUTH_FORGOT_PASSWORD_RESET](state, res) {
        state.forgotRes = res;
    },

    [types.AUTH_REGISTER_SUCCESS](state, res) {
        state.registerUserRes = res;
    },

    [types.CHANGE_PASSWORD](state, res) {
        state.changePasswordRes = res;
    },

    [types.CHANGE_EMAIL](state, res) {
        state.changeEmailRes = res;
        if (!res.error) {
            Cookies.remove('USER_BY_ID');
        } else if (res.error.code === 4011) {
            Cookies.remove('USER_BY_ID');
        }
    },

    [types.CHANGE_EMAIL_REQUEST](state, res) {
        state.changeEmailRequestRes = res;
        state.CHANGE_EMAIL_REQUEST_USER = state.ADMIN_USER;
    },

    [types.AUTH_CONFIRM_REGISTER_SUCCESS](state, res) {
        state.confirmUserRes = res;
        Cookies.remove('ADMIN_USER');
        Cookies.remove('ADMIN_ACCESS_TOKEN');
    },

    changeEmailSuccess(state, success) {
        state.changeEmailSuccess = success;
    }
};

// actions
export const actions = {
    async login({commit}, params) {
        const response = await axios.post(route('admin.login'), params).then(rs => rs.data).catch(err => err.response.data);
        commit(types.AUTH_LOGIN, response);
    },

    async fetchUser({commit}) {
        const response = await axios.get(route('admin.auth-user')).then(rs => rs.data).catch(err => err.response.data);
        commit(types.FETCH_AUTH , response);
    },

    async logout({commit}) {
        const response = await axios.post(route('admin.logout')).then(rs => rs.data).catch(err => err.response.data);
        commit(types.AUTH_LOGOUT, response);
    },

    async forgotRequest({commit}, params) {
        var response = await axios.post(route('admin.password.request'), params).then(rs => rs.data).catch(err => err.response.data);
        commit(types.AUTH_FORGOT_PASSWORD_REQUEST, response);
    },

    async forgotReset({commit}, params) {
        var response = await axios.post(route('admin.password.reset'), params).then(rs => rs.data).catch(err => err.response.data);
        commit(types.AUTH_FORGOT_PASSWORD_RESET, response);
    },

    async confirm({commit}, params) {
        const data = await axios.post(route('admin.register.confirm'), {token:params})
            .then(rs => rs.data).catch(err => err.response.data);
        commit(types.AUTH_CONFIRM_REGISTER_SUCCESS, data);
    },

    async register({commit}, params) {
        const response = await axios.post(route('admin.register'), params)
            .then(rs => rs.data).catch(err => err.response.data);
        commit(types.AUTH_REGISTER_SUCCESS, response);
    },

    async checkToken({commit}, params) {
        var response = await axios.post(route('admin.password.check'), {token:params}).then(rs => rs.data).catch(err => err.response.data);
        commit(types.AUTH_CHECK_TOKEN, response);
    },

    async changePassword({commit}, params) {
        const response = await axios.post(route('admin.change-password'), params)
            .then(rs => rs.data).catch(err => err.response.data);
        commit(types.CHANGE_PASSWORD, response);
    },

    async changeEmailRequest({commit}, params)
    {
        const response = await axios.post(route('admin.change-email-request'), params)
            .then(rs => rs.data).catch(err => err.response.data);
        commit(types.CHANGE_EMAIL_REQUEST, response);
    },

    async changeEmail({commit}, params) {
        const response = await axios.post(route('admin.change-email'), params)
            .then(rs => rs.data).catch(err => err.response.data);
        commit(types.CHANGE_EMAIL, response);
    },

    async userByIdRequestChangeEmail({commit}, params) {
        const response = await axios.post(route('admin.get-user'), params)
            .then(rs => rs.data).catch(err => err.response.data);
        commit(types.GET_USER_AUTH_BY_ID, response);
    },

    changeEmailSuccess({commit}, params) {
        let success = params;
        commit('changeEmailSuccess', success);
    }
};
