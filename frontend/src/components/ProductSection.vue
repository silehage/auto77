<template>
  <section>
    <div class="row q-pa-xs justify-end items-center q-mt-sm padding-responsive" v-if="products.ready && products.available">
      <div class="q-gutter-x-xs" v-if="config">
        <q-btn @click="changeViewMode('grid')" unelevated size="sm" :color="config.product_view_mode == 'grid' ? 'green-7' : 'grey-8'" dense icon="grid_view"></q-btn>
        <q-btn @click="changeViewMode('list')" unelevated size="sm" :color="config.product_view_mode == 'list' ? 'green-7' : 'grey-8'" dense icon="view_list"></q-btn>
      </div>
    </div>
    <div class="column q-gutter-y-sm" v-if="config && config.product_view_mode == 'list'">
      <template v-if="products.ready">
        <product-list v-for="(product, index) in products.data" :key="index" :product="product" />
      </template>
      <template v-else>
        <product-list-skeleton v-for="a in 4" :key="a" />
      </template>
    </div>
     <div class="row items-stretch q-px-xs" v-else>
      <template v-if="products.ready">
        <product-card v-for="(product, index) in products.data" :key="index" :product="product" />
      </template>
      <template v-else>
        <product-card-skeleton v-for="a in 4" :key="a" />
      </template>
    </div>
  </section>
</template>

<script>
import ProductCard from 'components/ProductCard.vue'
import ProductCardSkeleton from 'components/ProductCardSkeleton.vue'
import ProductList from 'components/ProductList.vue'
import ProductListSkeleton from 'components/ProductListSkeleton.vue'

export default {
  name: 'ProductSection',
  components: { 
    ProductCard, 
    ProductList, 
    ProductCardSkeleton, 
    ProductListSkeleton 
    },
  props: {
    products: Object,
    title: String,
  },
  data() {
    return {
      viewMode: 'grid',
    }
  },
  computed: {
    config() {
      return this.$store.state.config
    }
  },
  methods: {
    changeViewMode(str) {
      this.$store.commit('SET_PRODUCT_VIEW_MODE', str)
    },
    
  }
  

}
</script>
