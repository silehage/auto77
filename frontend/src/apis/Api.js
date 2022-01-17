import Axios from 'axios'
import { Loading, Notify } from 'quasar'

// Axios.defaults.withCredentials = true;
// Axios.defaults.baseURL = process.env.API_URL
var BaseApi = Axios.create({
  baseURL: API_URL
})

BaseApi.defaults.headers.common = { 'X-Requested-With': 'XMLHttpRequest' }

 // add a request interceptor
BaseApi.interceptors.request.use(config => {
  // change some state in your store here
  // Do something before request is sent
  // Loading.show()
  // store().commit('SET_LOADING', true)

  return config
}, error => {
  // Do something with request error
  Loading.hide()
  // store().commit('SET_LOADING', false)

  Notify.create({
    type: 'negative',
    message: 'Network Error'
  })
  return Promise.reject(error)
})

// Add a response interceptor
BaseApi.interceptors.response.use(response => {
  // Do something with response data
  // call some store functions etc..
  Loading.hide()
  return response

}, error => {
  Loading.hide()

  // var errors = error
  var errors = []
  let showMessage = true

  if (error.response) {

      // Session Expired
      if (401 === error.response.status) {
        showMessage = false
          errors.push(error.response.data.message)
          // store().dispatch('logOut')
          localStorage.removeItem('API_TOKEN')
          Notify.create({
            type: 'negative',
            message: 'Sesi anda sudah habis, silahkan login'
          });
          location.reload()
        }

      // Errors from backend
      if (error.response.status == 422) {
          // errors = error.response.data.message
          // showMessage = false
          // store().commit('category/SET_ERRORS', error.response.data.errors)
          // setTimeout(() => {
          //   store().commit('SET_ERRORS', {})
          // },15000)

          for(var errorKey in error.response.data.errors) {
              for(var i = 0; i < error.response.data.errors[errorKey].length; i++) {
                  errors.push(String(error.response.data.errors[errorKey][i]))
              }
          }
      }

      // Backend error
      if (500 === error.response.status) {
          // errors = error.response.data.message
          errors.push('Something went wrong, Please try again')
      }
      if (400 === error.response.status) {
          // errors = error.response.data.message
          errors.push('Network Error')
      }

      // 404
      if (error.response.status == 404) {
          errors.push('Page not found')
      }
  } else {
    errors.push('Network Error')
  }

  if(showMessage) {
    let length = 1;
    if(errors.length >= 2) {
      length = 2
    }
    for(var i = 0; i < length; i++) {
      Notify.create({
        type: 'negative',
        message: String(errors[i]),
      })
    }
  }
  
  // Do something with response error       
  return Promise.reject(error)
})


var Api = function () {
  const token = localStorage.getItem('API_TOKEN')
  if (token) {
    BaseApi.defaults.headers.common['Authorization'] = `Bearer ${token}`
    // BaseApi.defaults.headers.common.Authorization = `Bearer ${token}`
  }
  return BaseApi;
}

export default Api 