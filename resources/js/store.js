import { createStore } from 'vuex';

const store = createStore({
  state() {
    return {
      perPage: 10, // default value
    };
  },
  mutations: {
    setPerPage(state, value) {
      state.perPage = value;
    },
  },
  actions: {
    updatePerPage({ commit }, value) {
      commit('setPerPage', value);
    },
  },
  getters: {
    perPage: (state) => state.perPage,
  },
});

export default store;
