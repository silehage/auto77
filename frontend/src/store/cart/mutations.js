

export function ADD_TO_CART (state, payload) {
  let item;
  let hasItem = state.carts.find(el => el.product_id == payload.product_id)
  if(hasItem != undefined) {
    item = hasItem
    state.carts = state.carts.filter(ek => ek.product_id != payload.product_id)
    item = {...item, quantity: item.quantity+payload.quantity }
  } else {
    item = payload
  }
  state.carts.push(item) 
}

export function UPDATE_CART(state, payload) {
  
  let objIndex = state.carts.findIndex(el => el.product_id == payload.product_id)

  state.carts[objIndex].quantity = payload.quantity

}

export function REMOVE_CART(state, payload) {
  state.carts = state.carts.filter(c => c.product_id != payload.product_id)
}

export function CLEAR_CART (state) {
  state.carts = []
}

export function SET_CARTS (state, payload) {
  state.carts = payload
}

export function COMMIT_CARTS(state) {
  localStorage.setItem('_wacommerce-carts',JSON.stringify(state.carts))
}
export function ROLLBACK_CARTS(state) {
  state.carts = JSON.parse(localStorage.getItem('_wacommerce-carts'))
}