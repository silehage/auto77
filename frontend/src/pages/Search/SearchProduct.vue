<template>
  <q-page class="q-pb-xl" :class="{'bg-dark': $q.dark.isActive }">
     <q-header class="box-shadow" :class="{ 'bg-dark text-grey-1': $q.dark.isActive, 'bg-white text-dark': !$q.dark.isActive }">
       <q-toolbar class="header__padding">
         <q-btn :to="{name: 'Home'}"
            flat round dense
            icon="eva-arrow-back" />
            <q-toolbar-title class="text-weight-bold brand">Cari Produk</q-toolbar-title>
       </q-toolbar>
    </q-header>
    <div class="q-px-md q-pt-md">
      <div class="col border">
        <q-input ref="input" outlined dense color="grey-2" v-model="search" autofocus @keyup.enter="searchProduct" placeholder="ketik nama produk"
        >
        <template v-slot:append>
          <q-icon
            name="eva-search"
            class="cursor-pointer"
            @click="searchProduct"
          />
        </template>
        </q-input>
      </div>
      <template v-if="products.ready && products.available">
        <div class="q-pt-sm">Produk hasil pencarian "<b>{{ searchTitle }}</b>"</div>
      </template>
    </div>
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
    <div class="flex justify-center q-py-lg" v-if="products && products.links">
     <q-btn label="loadmore" color="primary" outline :loading="isLoadmore" v-if="products.links.next" @click="paginate(products.links.next)"></q-btn>
   </div>
  </q-page>
</template>

<script>
import ProductSection from 'components/ProductSection.vue'
import { mapActions } from 'vuex'
import { Api } from 'boot/axios'
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
        available: true,
        links: null,
        meta: null
      },
      isLoadmore: false
    }
  },
  methods: {
    ...mapActions('product', ['searchProducts']),
    searchProduct() {
      this.loading = true
      this.$refs.input.blur()
      this.searchProducts(this.search).then(response => {
        if(response.status == 200) {
          this.products.data = response.data.data
          this.products.links = response.data.links
          this.products.meta = response.data.meta
          this.products.available = this.products.data.length? true : false
        }
      }).finally(() => {
        this.products.ready = true
        this.loading = false
        this.searchTitle = this.search
        this.search = ''
      })
    },
    paginate(url) {
      this.isLoadmore = true
      Api().get(url).then(response => {
        if(response.status == 200) {
          this.products.data = [...this.products.data, ...response.data.data]
          this.products.links = response.data.links
          this.products.meta = response.data.meta
        }
      }).finally(() =>  this.isLoadmore = false)
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