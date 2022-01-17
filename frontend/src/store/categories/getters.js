export const getById = (state) => (id) => {
  return state.categories.data.find(el => el.id == id)
}

export const categoryCount = (state) => {
  return state.categories.data.length
}
export const getValueOptions = (state) => {
  let data = []
  if(state.categories.data.length > 0) {
    state.categories.data.forEach(el => {
      data.push({label: el.title, value: el.id})
    });
  }
  return data;
}