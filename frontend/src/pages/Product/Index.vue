<template>
  <q-page class="q-pb-xl">
    <q-header class="text-primary bg-white">
       <q-toolbar>
         <q-btn :to="{name: 'Home'}"
            flat round dense
            icon="arrow_back" />
          <q-toolbar-title class="text-weight-bold brand">{{ title }}</q-toolbar-title>
          <shopping-cart />
       </q-toolbar>
    </q-header>
   <product-section :title="title" :products="products"></product-section>
  </q-page>
</template>

<script>
import { mapActions } from 'vuex'
import ProductSection from 'components/ProductSection.vue'
import ShoppingCart from 'components/ShoppingCart.vue'
export default {
  name: 'ProductIndex',
  components: { ProductSection, ShoppingCart },
  data() {
    return {
      title: 'Katalog Produk',
      description: this.$store.state.meta.description,
    }
  },
  computed: {
    products() {
        return this.$store.state.product.products
      },
  },
  methods: {
    ...mapActions('product', ['getProducts']),
  },
  created() {
    if(this.$route.query.q){
      this.title = 'Produk ' + this.$route.query.q
    }
    if(this.$route.query && this.$route.query.q == 'Terlaris') {
      let q = this.$route.query.q
      this.getProducts(q.toLowerCase())
    } else {
      if(!this.products.data.length) {
          this.getProducts()
      }
    }
  },
  meta() {
    return {
      title: 'Katalog Produk',
      meta: {
        description: { name: 'description', content: this.description },
        ogDescription: { name: 'og:description', content: this.description },
        ogTitle:  { name: 'og:title', content: this.title },
        ogUrl:  { name: 'og:url', content: location.href },
      }
      
    }
  }
}
</script>
<style lang="scss">
.relative {
  position:relative;
}
.absolute {
  position:absolute;
  &__top-right{
    top:0;
    right:0;
  }
}
.mini .q-field__marginal,
.mini .q-field__control{
 height: 30px;
}
</style>