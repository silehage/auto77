<template>
  <q-btn outline class="btn-favorite" padding="4px" @click="btnFavorite" icon="favorite" size="12px" unelevated round :text-color="getFavoriteStyle()"></q-btn>
</template>

<script>
export default {
  props: ['product'],
  computed: {
      favorites: function() {
        return this.$store.state.product.favorites
      }
  },
  methods: {
    getFavoriteStyle() {
       let m = this.favorites.find(e => e == this.product.id)
       if(m) return 'pink'
       return 'grey'
    },
    btnFavorite() {
      this.$store.commit('product/ADD_REMOVE_TO_FAVORITE', this.product.id)
    },
  }
}
</script>