<template>
  <q-page class="q-pb-lg">
    <q-header class="text-primary bg-white">
       <q-toolbar>
         <q-btn :to="{name: 'Home'}"
            flat round dense
            icon="arrow_back" />
            <q-toolbar-title class="text-weight-bold brand">Cari Produk</q-toolbar-title>
            <!-- <q-btn flat label="Cari Order" :to="{ name: 'OrderSearch'}"></q-btn> -->
       </q-toolbar>
    </q-header>
    <div class="col bg-white border q-ma-md">
      <q-input ref="input" outlined dense color="grey-2" v-model="search" autofocus @keyup.enter="searchProduct" placeholder="ketik nama produk"
      >
      <template v-slot:append>
        <q-icon
          name="search"
          class="cursor-pointer"
          @click="searchProduct"
        />
      </template>
      </q-input>
    </div>
    <template v-if="products.ready && products.available">
      <div class="text-center"><b>{{ products.data.length }}</b> produk hasil pencarian "<b>{{ searchTitle }}</b></div>
    </template>
    <template v-if="products.data.length">
      <product-section :products="products"></product-section>
    </template>
    <template v-if="!products.available">
      <div class="text-center column">
        <div class="text-h6">Opps..</div>
        <div class="">Tidak ada produk dengan kata kunci <b>"{{ searchTitle }}"</b> </div>
      </div>
    </template>
    <q-inner-loading :showing="loading">
        <q-spinner-facebook size="50px" color="primary"/>
    </q-inner-loading>
  </q-page>
</template>

<script>
import ProductSection from 'components/ProductSection.vue'
import { mapActions } from 'vuex'
export default {
  name: 'ProductSearch',
  components: { ProductSection },
  data () {
    return {
      loading: false,
      search: '',
      searchTitle: '',
      products: {
        data: [],
        ready: false,
        available: true
      },
    }
  },
  methods: {
    ...mapActions('product', ['searchProducts']),
    searchProduct() {
      this.loading = true
      this.$refs.input.blur()
      this.searchProducts(this.search).then(response => {
        if(response.status == 200) {
          this.products.data = response.data.results.products
          this.products.available = this.products.data.length? true : false
        }
      }).finally(() => {
        this.products.ready = true
        this.loading = false
        this.searchTitle = this.search
        this.search = ''
      })
    }
  },
  mounted() {
    if(this.$route.query.q){
      let query = this.$route.query.q
      if(query) {
        this.search = query
        this.searchProduct()
      }
    }
  },
 created() {
   this.$store.commit('product/CLEAR_PRODUCT_SEARCH')
 }
  
}
</script>