import * as types from '../mutation-types';

// state
export const state = {
    loading: {
        enable: false
    }
};

// mutation
export const mutations = {
    [ types.ENABLE_LOADING ](state, res) {
        state.loading.enable = res;
    }
};

// actions 
export const actions = {
    async setEnable({commit}, value) {
        commit(types.ENABLE_LOADING, value);
    },
};
