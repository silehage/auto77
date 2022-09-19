export function SET_GALLERY ( state, payload) {
  state.galleries.data = payload
  state.galleries.ready = true
  state.galleries.available = payload.length > 0 ? true : false
}
export function REMOVE_GALLERY( state, ids ) {
  state.galleries.data = state.galleries.data.filter(el => el.id != ids)
}
export function SET_DATA_STATUS ( state, status ) {
  state.galleries.ready = status
}
