<template>
  <q-page class="romance" :class="{'flex flex-center' : loading, 'bg-grey-9': $q.dark.isActive, 'bg-grey-1': !$q.dark.isActive }">
    <q-header reveal :reveal-offset="10" class="box-shadow" 
    :class="{'bg-dark': $q.dark.isActive, 
    'bg-white text-dark': !$q.dark.isActive 
    }"
    >
      <div v-if="showApp" class="row justify-between items-center q-py-xs bg-white text-grey-8 q-px-md">
          <q-btn @click="closeApp" round icon="close" flat dense padding="0px"></q-btn>
          <div class="row q-gutter-x-md items-center">
          <div class="text-md text-weight-bold">Buka di Aplikasi</div>
            <q-btn unelevated no-caps unzelevated color="primary" flat dense padding="0px" @click="openInApp">
            <img width="110px" src="/static/app.svg" alt="">
            </q-btn>
          </div>
      </div>
      <q-toolbar class="header__padding flex justify-between items-center">
        <img v-if="shop" height="50px" :src="shop.logo_path? shop.logo_url : '/icon/icon-192x192.png'" />
        <div class="row items-center q-ml-sm">
          <MenuRight  />
        </div>
      </q-toolbar>
    </q-header>
    <template v-if="!loading">
      <div id="slider" class="header-romance" v-if="sliders.data.length">
        <slider :datas="sliders.data" />
      </div>
      <div id="featured" class="block-container q-pt-md" v-if="blocks.featured.length">
        <featured-carousel :datas="blocks.featured" />
      </div>

      <div class="page__padding q-pb-xl">
        <div id="categories" v-if="categories && categories.data.length > 1" class="block-container">
          <div class="block-heading">
            <div class="block-title"><h2>Kategori</h2></div>
          </div>
          <div class="block-content">
            <category-carousel :datas="categories.data" />
          </div>
        </div>

        <div id="product-promo" v-if="productPromo.length" >
          <product-promo :product_promo="productPromo" />
        </div>

        </div>

        <div v-if="banner1" class="banner block-container">
          <img :src="banner1.image_url" @click="goToPost(banner1)">
        </div>
        
        <div class="page__padding">
          <ProductSectionObserver />
        </div>

        <div v-if="banner2" class="banner auto-padding-side block-container">
          <img :src="banner2.image_url" @click="goToPost(banner2)">
        </div>

        <post-block :posts="posts" />

        <div v-if="banner3" class="banner auto-padding block-container">
          <img :src="banner3.image_url" @click="goToPost(banner3)">
        </div>
      <footer-block />
    </template>

      <q-inner-loading :showing="loading">
        <q-spinner-facebook size="50px" color="primary"/>
      </q-inner-loading>
  </q-page>
</template>
<script>
import { mapActions, mapState } from 'vuex'
import MenuRight from 'components/MenuRight.vue'
import Slider from './block/Slider.vue'
import ProductSectionObserver from './../shared-components/ProductSectionObserver.vue'
import featuredCarousel from './../shared-components/FeaturedCarousel.vue'
import categoryCarousel from './block/CategoryCarousel.vue'
import productPromo from './../shared-components/ProductPromo.vue'
import Cookies from 'js-cookie';
export default {
  name: 'PageIndex',
  components: {
    MenuRight,
    Slider, 
    ProductSectionObserver, 
    featuredCarousel,
    categoryCarousel,
    productPromo,
    'post-block': () => import('./../shared-components/PostBlock.vue'), 
    'footer-block': () => import('./../shared-components/FooterBlock.vue'),
  },
  data() {
    return {
      viewMode: 'grid',
      search: '',
      slide: 0,
      showApp: false
    }
  },
  computed: {
    ...mapState({
      blocks: state => state.block.blocks,
      sliders: state => state.slider.sliders ,
      categories: state => state.category.categories ,
      products: state => state.product.initial_products,
      shop: state => state.shop,
      loading: state => state.loading,
      posts: state => state.post.initialPost,
      config: state => state.config,
      productPromo: state => state.product.product_promo,
    }),
    cheight: function() {
      let n =(this.$q.screen.width /1.7)
      if(this.$q.screen.width > 600) {
        return 400 +'px'
      } else {
        return (this.$q.screen.width /1.5) +'px'
      }
    },
    banner1() {
      if(this.blocks.banner.length) {
        let banner = this.blocks.banner.find(b => b.weight == 1)
        if(banner != undefined) {
          return banner
        }
      }
      return null
    },
    banner2() {
      if(this.blocks.banner.length) {
        let banner = this.blocks.banner.find(b => b.weight == 2)
        if(banner != undefined) {
          return banner
        }
      }
      return null
    },
    banner3() {
      if(this.blocks.banner.length) {
        let banner = this.blocks.banner.find(b => b.weight == 3)
        if(banner != undefined) {
          return banner
        }
      }
      return null
    },
  },
  methods: {
    ...mapActions(['getInitialData']),
    showProductByCategory(id) {
      this.$router.push({name: 'ProductCategory', params: { id:id }})
    },
    searchNow() {
      if(!this.search || this.search == '') return
        this.$router.push({name: 'ProductSearch', query: {q: this.search }})
    },
    goToPost(block) {
      if(block.post) {
        this.$router.push({name: 'FrontPostShow', params: { slug: block.post.slug }})
      }
    },
    closeApp() {
      this.showApp = false
      Cookies.set('hideApp', true, { expires: 3 })
    },
    openInApp() {
      window.open(this.shop.google_play_url)
    }
  },
  mounted() {
    if(this.config) {
      this.viewMode = this.config.home_view_mode
    }
    setTimeout(() => {
      if(this.shop && this.shop.google_play_url) {

        if(!Cookies.get('hideApp')) {
            this.showApp = true
        }
      }
    }, 5000)
  },
created() {
  if(!this.shop || !this.products.data.length || this.$route.query.load) {
      this.getInitialData()
    }
  }
}
</script>
