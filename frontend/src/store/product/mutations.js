export function SET_PRODUCT (state, payload) 
{
  state.products.data.push(payload)
}

export function SET_PRODUCTS (state, payload) 
{
  state.products.data = payload
  state.products.ready = true
  state.products.available = payload.length > 0 ? true : false
}
export function SET_INITIAL_PRODUCTS (state, payload) 
{
  state.initial_products.data = payload
  state.initial_products.ready = true
  state.initial_products.available = payload.length > 0 ? true : false
}
export function SET_PRODUCT_SEARCH (state, payload) 
{
  state.productSearch.data = payload
  state.productSearch.ready = true
  state.productSearch.available = payload.length > 0 ? true : false
}
export function CLEAR_PRODUCT_SEARCH (state) 
{
  state.productSearch.data = []
  state.productSearch.ready = false
  state.productSearch.available = true
}
export function SET_SEARCH_KEY(state, payload) 
{
  state.searchKey = payload
}
export function ADD_REMOVE_TO_FAVORITE (state, fid) 
{
  state.productFavorites.data = state.productFavorites.data.filter(el => el.id != fid)
  if(state.productFavorites.data.length == 0) {
    state.productFavorites.available = false
    state.productFavorites.ready = true
  }

  if(state.favorites.length > 0) {
    let n = state.favorites.find(e => e == fid)
    if(n != undefined) {
     state.favorites = state.favorites.filter(h => h != fid)
    } else {
      state.favorites.push(fid)
    }
  } else {
    state.favorites.push(fid)
  }
  // localStorage.setItem('wacommerce-favorites', JSON.stringify(state.favorites))
}
export function SET_FRONT_RODUCT_READY (state, status) 
{
  state.terbaru.ready = status
  state.terbaru.terlaris = status
}
export function SET_PRODUCT_CATEGORY (state, payload) 
{
  state.productByCategory.data = payload
  state.productByCategory.ready = true
  state.productByCategory.available = payload.length > 0 ? true : false
}
export function CLEAR_PRODUCT_CATEGORY (state) 
{
  state.productByCategory.data = []
  state.productByCategory.ready = false
  state.productByCategory.available = true
}
export function SET_PRODUCT_FAVORITE (state, payload) 
{
  state.productFavorites.data = payload
  state.productFavorites.ready = true
  state.productFavorites.available = payload.length > 0 ? true : false
}
export function CLEAR_PRODUCT_FAVORITE (state) 
{
  state.productFavorites.data = []
  state.productFavorites.ready = false
  state.productFavorites.available = true
}
export function SET_PRODUCT_FAVORITE_STATUS (state, payload) 
{
  state.productFavorites.ready = payload.ready
  state.productFavorites.available = payload.available
}
