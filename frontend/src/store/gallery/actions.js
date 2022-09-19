import { Api } from 'boot/axios'
import { Notify } from 'quasar'
 
export function getAll(context) {
  Api().get('galleries').then(response => {
    context.commit('SET_GALLERY', response.data.results)
  })
}
export function getFront() {
  return Api().get('galleries')
}

export function removeItem( context, id ) {
  context.commit('SET_DATA_STATUS', false)
  Api().delete('gallery/' + id).then(() => {
    context.dispatch('getAll')
    context.commit('REMOVE_SLIDER', id)
    Notify.create({
      type: 'positive',
      message: 'Berhasil menghapus data'
    })
  }).catch(() => {
    context.commit('SET_DATA_STATUS', true)
  })
}

export function updateWeight ( context, payload) {
  context.commit('SET_DATA_STATUS', false)
 Api().post('update-gallery-weight', payload).then(() => {
    context.dispatch('getAll')
  }).catch(() => {
    context.commit('SET_DATA_STATUS', true)
  })
}