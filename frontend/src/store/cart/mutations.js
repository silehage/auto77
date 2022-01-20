

export function ADD_TO_CART (state, payload) {
  let item;
  let hasItem = state.carts.find(el => el.sku == payload.sku)
  if(hasItem != undefined) {
    item = hasItem
    state.carts = state.carts.filter(ek => ek.sku != payload.sku)
    item = {...item, quantity: item.quantity+payload.quantity }
  } else {
    item = payload
  }
  state.carts.push(item) 
}

export function UPDATE_CART(state, payload) {

  let objIndex = state.carts.findIndex(el => el.sku == payload.sku)

  state.carts[objIndex].quantity = parseInt(payload.quantity)

}

export function REMOVE_CART(state, payload) {
  state.carts = state.carts.filter(c => c.sku != payload.sku)
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