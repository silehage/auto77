import { Api } from 'boot/axios'

export function addPost({ commit, dispatch }, payload) {
  // return
  let formData = new FormData();
  for(const x in payload) {

    if(payload[x] !== '' && x != 'gallery') {
      formData.append(x, payload[x])
    }
  }

  for(var j = 0; j < payload.gallery.length; j++ ){
    let file = payload.gallery[j];

    formData.append('gallery[' + j + ']', file);
  }

  let self = this
  commit('SET_LOADER_POST')
  Api().post('posts', formData, {headers: {'content-Type': 'multipart/formData'}}).then(response => {
    if(response.status == 200) {
     dispatch('getAllPost')
      self.$router.push({name: 'AdminPostIndex'})
      commit('SET_LOADING', false, { root: true })
    }
  }).catch(() => {
    commit('SET_LOADING', false, { root: true })
  })
}
export function updatePost (context, payload) {
  context.commit('SET_LOADER_POST')

  let formData = new FormData();

  for(const x in payload) {

    if(payload[x] !== '' && x != 'gallery' && x != 'delete_gallery') {
      formData.append(x, payload[x])
    }
  }

  for(var j = 0; j < payload.gallery.length; j++ ){
    let file = payload.gallery[j];

    formData.append('gallery[' + j + ']', file);
  }

  if(payload.delete_gallery.length) {
    formData.append('delete_gallery', JSON.stringify(payload.delete_gallery))
  }

  formData.append('_method', 'PUT')

  Api().post('posts/'+payload.id, formData, {headers: {'content-Type': 'multipart/formData'}}).then(response => {
    if(response.status == 200) {
      this.$router.push({name: 'AdminPostIndex'})
      context.dispatch('getAllPost')
    }
  })
}
export function deletePost (context, id) {
  context.commit('SET_LOADER_POST')
  Api().delete('posts/'+id).then(response => {
    if(response.status == 200) {
      context.dispatch('getAllPost')
    }
  })
}
export function getAllPost (context) {
  Api().get('posts').then(response => {
    if(response.status == 200) {
      context.commit('SET_ALL_POST', response.data.results)
    }
  })
}
export function listingPost (context) {
  Api().get('posts?q=listing').then(response => {
    if(response.status == 200) {
      context.commit('SET_LISTING_POST', response.data.results)
    }
  })
}
export function getSinglePost (context, id) {
  return Api().get('posts/'+ id)
}
export function getSinglePostBySlug (context,slug) {
  return Api().get('post/'+slug)
}

