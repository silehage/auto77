<template>
  <q-page class="romance" :class="{'flex flex-center' : loading, 'bg-dark': $q.dark.isActive, 'bg-grey-1': !$q.dark.isActive }">
    <q-header class="box-shadow" :class="{ 'bg-secondary text-grey-1': $q.dark.isActive, 'bg-white text-dark q-py-none': !$q.dark.isActive }">
    <InstallApp />
    <q-toolbar class="q-px-none flex justify-between items-center">
      <img v-if="shop" height="55px" :src="shop.logo_path? shop.logo_url : '/icon/icon-192x192.png'" />
      <q-space></q-space>
      <div class="row items-center q-pr-sm">
        <MenuRight></MenuRight>
      </div>
    </q-toolbar>
    </q-header>
    <template v-if="!loading">
      <div id="slider" class="header-romance" v-if="sliders.data.length">
        <slider :datas="sliders.data" />
      </div>
      <div id="featured" class="block-container q-pt-md text-center" v-if="blocks.featured.length">
        <featured-carousel :datas="blocks.featured" />
      </div>

      <div id="categories" v-if="categories && categories.data.length > 1" class="auto-padding block-container">
        <div class="block-heading">
          <div class="block-title"><h2>Kategori</h2></div>
        </div>
        <div class="block-content">
          <category-carousel :datas="categories.data" />
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
import InstallApp from 'components/InstallApp.vue'
import MenuRight from 'components/MenuRight.vue'
import Slider from './block/SwiperSlider.vue'
import ProductSectionObserver from './../shared-components/ProductSectionObserver.vue'
import featuredCarousel from './../shared-components/FeaturedCarousel.vue'
import categoryCarousel from './block/CategoryCarousel.vue'
import Cookies from 'js-cookie';
export default {
  name: 'PageIndex',
  components: {
    MenuRight,
    Slider, 
    ProductSectionObserver, 
    featuredCarousel,
    categoryCarousel,
    InstallApp,
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
