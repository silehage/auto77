export function SET_USER (state, payload) {
  state.user = payload
  state.loggedUser = true
}

export function LOGOUT (state) {
  localStorage.removeItem('API_TOKEN')
  state.user = false
  state.loggedUser = false
}
