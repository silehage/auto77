import Vue from 'vue'
import Vuex from 'vuex'
import { Api } from 'boot/axios'

import createPersistedState from "vuex-persistedstate";
import Cookies from 'js-cookie';

// import SecureLS from "secure-ls";
// var ls = new SecureLS({ isCompression: false });

import user from './user'
import product from './product'
import category from './categories'
import slider from './slider'
import post from './post'
import block from './block'
import order from './order'
import bank from './bank'
import cart from './cart'

import mutations from './mutations'
import getters from './getters'

const stateData = createPersistedState({
  key: '__state',
  paths: ['user', 'cart', 'shop', 'config', 'session_id'],
  storage: {
    getItem: (key) => Cookies.get(key),
    setItem: (key, value) => Cookies.set(key, value, { expires: 1 }),
    removeItem: (key) => Cookies.remove(key),
  }
})

Vue.use(Vuex)

/*
 * If not building with SSR mode, you can
 * directly export the Store instantiation;
 *
 * The function below can be async too; either use
 * async/await or return a Promise which resolves
 * with the Store instance.
 */

export default function (/* { ssrContext } */) {

  const Store = new Vuex.Store({
    state: {
      themes: ['default', 'elegant', 'romance'],
      errors: {},
      loading: false,
      shop: null,
      config: null,
      deferredPrompt: null,
      session_id: null,
      meta: {
        title: '',
        description: 'Simple Web Commerce checkout whatsapp'
      }
    },
    actions: {
      getAdminConfig({commit}) {
        commit('SET_LOADING', true)
        Api().get('adminConfig').then((response) => {
          if(response.status == 200){
            this.commit('SET_CONFIG', response.data.results)
          }
        })
        .finally(() => {
          commit('SET_LOADING', false)
        })
      },
      getShop: ({commit}) => {
        Api().get('shop').then(response => {
          if(response.status == 200) {
            commit('SET_SHOP', response.data.results.shop)
            commit('SET_CONFIG', response.data.results.config)
          }
        })
      },
      setShop: ({commit}) => {
        Api().post('shop').then(response => {
          if(response.status == 200) {
            commit('SET_SHOP', response.data.results)
          }
        })
      },
      getConfig: ({commit}) => {
        Api().get('config').then(response => {
          if(response.status == 200) {
            commit('SET_CONFIG', response.data.results)
          }
        })
      },
      getInitialData: ({commit}) => {
        commit('SET_LOADING', true)
        Api().get('getInitialData').then(response => {
          if(response.status == 200) {
            commit('SET_SHOP', response.data.results.shop),
            commit('SET_CONFIG', response.data.results.config)
            commit('block/SET_BLOCKS', response.data.results.blocks, { root: true }),
            commit('product/SET_INITIAL_PRODUCTS', response.data.results.products, { root: true })
            commit('category/SET_CATEGORIES', response.data.results.categories, { root: true })
            commit('slider/SET_SLIDERS', response.data.results.sliders, { root: true })
            commit('post/SET_INITIAL_POST', response.data.results.posts, { root: true })
            commit('SET_LOADING', false)
            commit('SET_SESSION_ID', response.data.results.sess_id)
          }
        }).catch(() => {
          commit('SET_LOADING', false)
        })
      }
    },
    mutations,
    getters,
    modules: {
      user,
      product,
      category,
      slider,
      post,
      block,
      order,
      bank,
      cart
    },
    plugins: [stateData],

    // enable strict mode (adds overhead!)
    // for dev mode only
    strict: process.env.DEBUGGING
  })

  return Store
}
