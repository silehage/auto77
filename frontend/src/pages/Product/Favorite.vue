<template>
  <q-page :class="{'flex flex-center':!products.available, 'bg-grey-9': $q.dark.isActive}" class="q-pb-xl">
    <q-header class="text-primary box-shadow" :class="{ 'bg-dark text-grey-1': $q.dark.isActive, 'bg-white': !$q.dark.isActive }">
        <q-toolbar class="header__padding">
          <q-btn @click="backButton"
            flat round dense
            icon="eva-arrow-back" />
          <q-toolbar-title class="text-weight-bold brand">Produk Favorite</q-toolbar-title>
          <MenuRight />
        </q-toolbar>
    </q-header>
    <template v-if="products.available">
      <product-section title="Produk Favorit" :products="products"></product-section>
    </template>
     <template v-if="!products.available">
      <div class="column items-center">
        <p class="text-grey-5 text-weight-medium text-center">Tidak ada produk favorit</p>
        <q-btn :to="{name: 'ProductIndex'}" rounded text-color="white" color="primary" unelevated
            icon="eva-arrow-back" label="Kehalaman produk" no-caps/>
      </div>
    </template>
  </q-page>
</template>

<script>
import { mapActions, mapState } from 'vuex'
import ProductSection from 'components/ProductSection.vue'
import MenuRight from 'components/MenuRight.vue'
export default {
  name: 'ProductFavorite',
  components: {ProductSection, MenuRight },
  computed: {
    ...mapState({
      favorites: state => state.product.favorites,
      products: state => state.product.productFavorites
    }),
  },
  methods: {
    ...mapActions('product', ['getProductsFavorites']),
    backButton() {
      if(window.history.length > 2) {
        window.history.back()
      }else {
        this.$router.push({name: 'ProductIndex'})
      }
    },
  },
  created() {
    if(this.favorites.length) {
      this.$store.commit('product/CLEAR_PRODUCT_FAVORITE')
      this.getProductsFavorites({ pids: this.favorites })
    } else {
      this.$store.commit('product/SET_PRODUCT_FAVORITE_STATUS', { ready: true, available: false })
    }
  },
  meta() {
  return {
    title: 'Produk Favorit',
      meta: {
        ogTitle:  { name: 'og:title', content: 'Produk Favorit' },
        ogUrl:  { name: 'og:url', content: location.href },
        ogImage:  { name: 'og:image', content: this.shop?.logo ? this.shop.logo : '' },
      }
      
    }
  }
}
</script>

<style>

</style>