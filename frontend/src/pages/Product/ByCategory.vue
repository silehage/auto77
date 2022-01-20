<template>
  <q-page :class="{'flex flex-center':!products.available}" class="q-pb-xl q-pt-sm">
     <q-header class="text-primary bg-white">
       <q-toolbar>
         <q-btn @click="backButton"
            flat round dense
            icon="arrow_back" />
          <q-toolbar-title class="text-weight-bold brand">{{ title }}</q-toolbar-title>
          <shopping-cart />
       </q-toolbar>
    </q-header>
    <template v-if="products.available">
      <product-section title="Produk Katalog" :products="products"></product-section>
    </template>
    <template v-if="!products.available">
      <div class="column items-center">
        <p class="text-grey-8 text-weight-bold text-center">Tidak ada produk ditemukan</p>
        <q-btn :to="{name: 'ProductIndex'}" rounded text-color="white" color="dark"
            icon="keyboard_backspace" label="kembali kehalaman produk" no-caps/>
      </div>
    </template>
  </q-page>
</template>

<script>
import { mapActions } from 'vuex'
import ProductSection from 'components/ProductSection.vue'
import ShoppingCart from 'components/ShoppingCart.vue'
export default {
  name: 'ProductCategory',
  components: { ProductSection, ShoppingCart },
  data () {
    return {
      likes: [],
      description: this.$store.state.meta.description,
      shop: this.$store.state.shop,
    }
  },
  computed: {
    favorites: function() {
      return this.$store.state.product.favorites
    },
    products() {
      return this.$store.state.product.productByCategory
    },
    categories() {
      return this.$store.state.category.categories
    },
    title() {
      return this.$store.state.meta.title
    }
      
  },
  methods: {
    ...mapActions('product', ['getProductsByCategory']),
    backButton() {
      if(window.history.length > 2) {
        window.history.back()
      }else {
        this.$router.push({name: 'ProductIndex'})
      }
    },
    getCategoryTitle() {
      if(this.categories.data.length) {
        let c = this.categories.data.find(el => el.id == this.$route.params.id)
        if(c != undefined) {
          this.$store.commit('SET_META_TITLE', c.title)
        }
      }
    }
  },
  mounted() {
    this.getCategoryTitle()
  },
  created() {
    if(this.products.data.length) {

      if(this.products.data[0].id != this.$route.params.id) {

        this.$store.commit('product/CLEAR_PRODUCT_CATEGORY')
        this.getProductsByCategory(this.$route.params.id)

      }

    } else {

      this.$store.commit('product/CLEAR_PRODUCT_CATEGORY')
      this.getProductsByCategory(this.$route.params.id)
    }
  },
  meta() {
  return {
    title: this.title,
    meta: {
        description: { name: 'description', content: this.description },
        ogDescription: { name: 'og:description', content: this.description },
        ogTitle:  { name: 'og:title', content: this.title },
        ogUrl:  { name: 'og:url', content: location.href },
        ogImage:  { name: 'og:image', content: this.shop?.logo ? this.shop.logo: ''},
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