import { Api } from 'boot/apiv2'

export function productStore({ dispatch, commit }, payload) {
  commit('SET_LOADING', true, { root: true })
  Api().post('product', payload)
    .then(() => {
      dispatch('getAdminProducts')
      this.$router.push({ name: 'AdminProductIndex' })

    })
    .finally(() => {
      commit('SET_LOADING', false, { root: true })
    })

}

export function productUpdate({ commit }, payload) {
  commit('SET_LOADING', true, { root: true })
  Api().post('product/update', payload)
    .then(response => {
      commit('UPDATE_PRODUCT', response.data.results)

      this.$router.push({ name: 'AdminProductIndex' })
    })
    .finally(() => {
      commit('SET_LOADING', false, { root: true })
    })
}

export function getAdminProducts({ commit }, q = null) {

  let url = 'getAdminProducts';

  if (q) {
    url += `?=${q}`
  }
  Api().get(url).then(response => {
    commit('SET_ADMIN_PRODUCTS', response.data.results)
  })
}
export function searchAdminProducts({ commit }, key) {
  return Api().get('searchAdminProducts/' + key)
}

export function getProducts({ commit }) {

  Api().get('products').then(response => {
    commit('SET_PRODUCTS', response.data)
  })
}

export function getProductById({ }, id) {
  return Api().get('productById/' + id)
}
export function getProductBySlug({ }, slug) {
  return Api().get('product/' + slug)
}

export function productDelete({ dispatch }, id) {
  Api().delete('product/' + id).then(() => {
    dispatch('getAdminProducts')
  })
}

export function searchProducts({ commit }, q) {
  return Api().get('search/' + q)
}

export function getProductsByCategory({ commit }, id) {

  Api().get('getProductsByCategory/' + id).then(response => {
    if (response.status == 200) {
      commit('SET_PRODUCT_CATEGORY', response.data)
      if (response.data.data.length) {
        if (response.data.data[0].category) {
          commit('SET_META_TITLE', response.data.data[0].category.title, { root: true })
        }
      }
    }
  })
}

export function getProductsFavorites({ commit }, payload) {
  Api().post('getProductsFavorites', payload).then(response => {
    if (response.status == 200) {
      commit('SET_PRODUCT_FAVORITE', response.data)
    }
  })
}
export function addProductReview({ commit }, payload) {
  return Api().post('addProductReview', payload)
}
export function loadProductReview({ }, payload) {
  if (payload.skip) {
    return Api().get('loadProductReview/' + payload.product_id + '?skip=' + payload.skip)
  } else {
    return Api().get('loadProductReview/' + payload.product_id)
  }
}
