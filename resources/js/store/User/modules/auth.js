import axios from 'axios';
import Cookies from 'js-cookie';
import * as types from '../mutation-types';

// state
export const state = {
    loginRes: null,
    registerUserRes: null,
    confirmUserRes: {},
    USER: {},
    changePasswordRes: null,
    changeEmailRequestRes: {},
    changeEmailRes: {},
    changeEmailSuccess: false,
    socialLoginRes: {},
    user: [],
    USER_ACCESS_TOKEN: Cookies.get('USER_ACCESS_TOKEN'),
    USER_AUTH_EXPIRE: Cookies.get('USER_AUTH_EXPIRE'),
    USER_AUTHENTICATED: Cookies.get('USER_AUTHENTICATED'),
    USER_BY_ID: Cookies.get('USER_BY_ID')
};

// getters
export const getters = {
    confirmUserRes: state => state.confirmUserRes,
    checkTokenRes: state => state.checkTokenRes,
    changePasswordRes: state => state.changePasswordRes,
    loginRes: state => state.loginRes,
    authExpiresTime: state => state.authExpiresTime,
    forgotRes: state => state.forgotRes,
    socialLoginRes: state => state.socialLoginRes,
    USER : state => state.USER,
    USER_AUTHENTICATED : state => state.USER_AUTHENTICATED,
    USER_ACCESS_TOKEN: state => state.USER_ACCESS_TOKEN
};

// mutations
export const mutations = {
    [types.AUTH_LOGIN](state, res) {
        state.loginRes = res;
        if (!res.error && res.data.token !== undefined) {
            state.USER_ACCESS_TOKEN = res.data.token;
            if (!res.data.remember_me) {
                Cookies.set('USER_ACCESS_TOKEN', res.data.token);
            } else {
                Cookies.set('USER_ACCESS_TOKEN', res.data.token, {expires: res.data.expires_in / 86400});
                Cookies.set('USER_AUTH_EXPIRE', (res.data.expires_in / 86400), {expires: res.data.expires_in / 86400});
            }
        }
    },

    [types.FETCH_AUTH ](state, res) {
        if (Cookies.get('USER_ACCESS_TOKEN') && res.error) {
            Cookies.remove('USER_ACCESS_TOKEN');
        }
        if (!res.error) {
            state.USER = res.data;
            var USER_AUTHENTICATED = (({ user_id, is_authenticated, is_has_business_card }) => ({ user_id, is_authenticated, is_has_business_card }) ) (res.data);
            if (!Cookies.get('USER_AUTH_EXPIRE')) {
                Cookies.set('USER_AUTHENTICATED', USER_AUTHENTICATED);
            } else {
                Cookies.set('USER_AUTHENTICATED', USER_AUTHENTICATED, {expires: JSON.parse(Cookies.get('USER_AUTH_EXPIRE'))});
            }
        }
    },


    [types.AUTH_FORGOT_PASSWORD_REQUEST](state, res) {
        state.forgotRes = res;
    },

    [types.GET_USER_AUTH_BY_ID](state, res) {
        state.getUserAuthByTokenRes = res;
        if (!res.error) {
            state.USER_BY_ID = res.data;
            Cookies.set('USER_BY_ID', res.data);
        }
    },

    [types.CHANGE_PASSWORD](state, res) {
        state.changePasswordRes = res;
    },


    [types.AUTH_CHECK_TOKEN](state, res) {
        state.checkTokenRes = res;
    },

    [types.AUTH_FORGOT_PASSWORD_RESET](state, res) {
        state.forgotRes = res;
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
    },

    [types.SOCIAL_LOGIN](state, res) {
        state.socialLoginRes = res;
        if (!res.error && res.data.token !== undefined) {
            state.USER_ACCESS_TOKEN = res.data.token;
            Cookies.set('USER_ACCESS_TOKEN', res.data.token);
        }
    },

    [types.AUTH_LOGOUT](state, res) {
        if (!res.err) {
            Cookies.remove('USER_ACCESS_TOKEN');
            Cookies.remove('USER_AUTHENTICATED');
            Cookies.remove('USER_AUTH_EXPIRE');
            state.USER = {};
        }
    },

    [types.AUTH_REGISTER_SUCCESS](state, res) {
        state.registerUserRes = res;
    },

    [types.AUTH_CONFIRM_REGISTER_SUCCESS](state, res) {
        state.confirmUserRes = res;
        Cookies.remove('USER_AUTHENTICATED');
        Cookies.remove('USER_ACCESS_TOKEN');
    },

    changeEmailSuccess(state, success) {
        state.changeEmailSuccess = success;
    }
};

// actions
export const actions = {
    async login({commit}, params) {
        const response = await axios.post(route('user.login'), params).then(rs => rs.data).catch(err => err.response.data);
        commit(types.AUTH_LOGIN, response);
    },

    async loginSocial({commit}, params) {
        const response = await axios.post(route('user.login.social'), params).then(rs => rs.data).catch(err => err.response.data);
        commit(types.SOCIAL_LOGIN, response);
    },

    async fetchUser({commit}) {
        const response = await axios.get(route('user.auth-user')).then(rs => rs.data).catch(err => err.response.data);
        commit(types.FETCH_AUTH , response);
    },

    async logout({commit}) {
        const response = await axios.post(route('user.logout')).then(rs => rs.data).catch(err => err.response.data);
        commit(types.AUTH_LOGOUT, response);
    },

    async confirm({commit}, params) {
        const data = await axios.post(route('user.register.confirm'), {token:params})
            .then(rs => rs.data).catch(err => err.response.data);
        commit(types.AUTH_CONFIRM_REGISTER_SUCCESS, data);
    },

    async register({commit}, params) {
        const response = await axios.post(route('user.register'), params)
            .then(rs => rs.data).catch(err => err.response.data);
        commit(types.AUTH_REGISTER_SUCCESS, response);
    },

    async forgotRequest({commit}, params) {
        var response = await axios.post(route('user.password.request'), params).then(rs => rs.data).catch(err => err.response.data);
        commit(types.AUTH_FORGOT_PASSWORD_REQUEST, response);
    },

    async forgotReset({commit}, params) {
        var response = await axios.post(route('user.password.reset'), params).then(rs => rs.data).catch(err => err.response.data);
        commit(types.AUTH_FORGOT_PASSWORD_RESET, response);
    },

    async checkToken({commit}, params) {
        var response = await axios.post(route('user.password.check'), {token:params}).then(rs => rs.data).catch(err => err.response.data);
        commit(types.AUTH_CHECK_TOKEN, response);
    },

    async changePassword({commit}, params) {
        const response = await axios.post(route('user.change-password'), params)
            .then(rs => rs.data).catch(err => err.response.data);
        commit(types.CHANGE_PASSWORD, response);
    },

    async changeEmailRequest({commit}, params) {
        const response = await axios.post(route('user.change-email-request'), params)
            .then(rs => rs.data).catch(err => err.response.data);
        commit(types.CHANGE_EMAIL_REQUEST, response);
    },

    async changeEmail({commit}, params) {
        const response = await axios.post(route('user.change-email'), params)
            .then(rs => rs.data).catch(err => err.response.data);
        commit(types.CHANGE_EMAIL, response);
    },

    async userByIdRequestChangeEmail({commit}, params) {
        const response = await axios.post(route('user.get-user'), params)
            .then(rs => rs.data).catch(err => err.response.data);
        commit(types.GET_USER_AUTH_BY_ID, response);
    },

    changeEmailSuccess({commit}, params) {
        let success = params;
        commit('changeEmailSuccess', success);
    }
};
