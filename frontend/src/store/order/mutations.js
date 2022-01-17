
export function SET_ORDERS (state, payload) {

  state.orders.data = payload
  state.orders.ready = true
  state.orders.available = payload.length > 0 ? true : false
  state.orders.canLoadMore = payload.length < 4 ? false : true
  
}
export function SET_PAGINATE_ORDERS (state, payload) {

  state.orders.data = [...state.orders.data, ...payload]
  state.orders.canLoadMore = payload.length < 4 ? false : true
  
}

export function SET_CUSTOMER_ORDERS (state, payload) {

  state.customer_order.data = payload
  state.customer_order.ready = true
  state.customer_order.available = payload.length > 0 ? true : false
  state.customer_order.canLoadMore = payload.length < 4 ? false : true
  
}
export function SET_PAGINATE_CUSTOMER_ORDERS (state, payload) {

  state.customer_order.data = [...state.customer_order.data, ...payload]
  state.customer_order.canLoadMore = payload.length < 4 ? false : true
  
}
export function SET_INVOICE (state, payload) {

  state.invoice = payload
  
}
export function SET_TRANSACTION (state, payload) {

  state.transaction = payload
  
}
export function SET_LOAD_MORE (state, status) {

  state.orders.isLoadMore = status
  
}
export function SET_LOAD_MORE_CUSTOMER (state, status) {

  state.customer_order.isLoadMore = status
  
}


