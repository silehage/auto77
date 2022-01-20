<template>
  <q-btn outline @click="btnFavorite" icon="favorite" size="sm" unelevated round :color="getFavoriteStyle()"></q-btn>
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
       return 'dark'
    },
    btnFavorite() {
      this.$store.commit('product/ADD_REMOVE_TO_FAVORITE', this.product.id)
    },
  }
}
</script>

<style>

</style>