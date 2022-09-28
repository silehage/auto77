<template>
  <div :id="'block-' + category.id" style="min-height:200px;">
    <template v-if="success">
      <div class="block-container bg-linear">
        <div class="q-mb-md" v-if="ready">
          <div class="row items-end justify-between">
            <div class="block-title"><h2>{{ category.title }}</h2></div>
            <q-btn flat no-caps padding="4px" :to="{name: 'ProductCategory', params:{ id: category.id }}">
              <span>Lihat Semua</span>
              <q-icon name="eva-arrow-forward" size="16px"></q-icon>
            </q-btn>
          </div> 
          <div v-if="category.description" class="block-subtitle">{{ category.description }}</div>
          <div class="banner" v-if="category.banner_src">
            <router-link :to="{name: 'ProductCategory', params:{ id: category.category_id }}">
            <img :src="category.banner_src" :alt="category.title" class="cursor-pointer" />
              </router-link>
            </div>
          </div>
          <div v-if="!ready" class="row items-center justify-between q-px-xs">
            <q-skeleton type="text" width="25%" height="50px" class="text-subtitle1" />
            <q-skeleton type="text" width="25%" class="text-subtitle1" />
          </div>
          <div class="block-content">
              <!-- <product-list-section-home :products="products" :ready="ready" :title="category.title"/> -->
            <!-- <div v-if="config && config.home_view_mode == 'list'">
            </div>
            <div v-else class="auto-padding-side">
            </div> -->
              <swiper-product :products="products" :ready="ready"/>
          </div>
      </div>
    </template>
  </div>
</template>

<script>
import { mapState } from 'vuex'
import SwiperProduct from 'components/GlideProduct.vue'
import { Api } from 'boot/axios'
export default {
  props: ['category'],
  components: { 
    SwiperProduct
   },
  data() {
    return {
      loading: false,
      ready: false,
      products: [],
      success: true
    }
  },
  computed: {
    ...mapState({
      config: state => state.config,
      categories: state => state.category.categories
    })
  },
  mounted() {
   this.intersecObserve()
  },
  methods: {
    intersecObserve() {
      this.loading = true

      let el = document.getElementById('block-' + this.category.id)

      let opts = {
        rootMargin: '0px',
        threshold: 0,
      }

      let clb = (entries) => {

      entries.forEach(entry => {

        if(!entry.isIntersecting) {

          return
          
        } else {

          // console.log(entry);

          this.getProducts()

          observer.unobserve(entry.target)

        }
      });
    };
      let observer = new IntersectionObserver(clb, opts);

       observer.observe(el)

    },
    async getProducts() {
      let response = await Api().get('getProductsByCategory/' + this.category.id +'?limit=10')

     if(response.status == 200) {
      this.products = response.data.data
      this.ready = true
      this.success = true
     }else {
       this.success = false
     }
      this.loading = true
    }
  }
}
</script>

<style>

</style>