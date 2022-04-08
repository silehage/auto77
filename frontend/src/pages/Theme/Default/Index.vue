<template>
  <q-page class="bg-grey-2 default">
    <q-header class="bg-white text-dark">
      <q-toolbar class="items-center sans">
        <img v-if="shop" class="logo" :src="shop.logo? shop.logo : '/icon/logo.png'" />
        <q-toolbar-title v-if="shop && shop.name" class="text-weight-bold text-primary">{{ shop.name }}</q-toolbar-title>
        <shopping-cart />
      </q-toolbar>
    </q-header>

      <div id="slider" class="q-pt-sm" v-if="sliders.data.length">
        <swipe-slider :datas="sliders.data" />
      </div>
      <div id="featured" class="auto-padding-side block-container q-pt-md" v-if="blocks.featured.length">
        <featured-carousel :datas="blocks.featured" />
      </div>
      <div id="categories" v-if="categories && categories.data.length > 1" class="auto-padding-side block-container">
        <div class="block-heading">
          <div class="block-title"><h2>Kategori</h2></div>
        </div>
        <div class="block-content q-pb-sm">
          <category-carousel :datas="categories.data" />
        </div>
      </div>
      <div v-if="blocks.banner.length" class="banner auto-padding-side block-container">
        <img :src="blocks.banner[0].image_url" @click="goToPost(blocks.banner[0])">
      </div>
      
      <product-block :products="products" />

      <div v-if="blocks.partner.length" class="partner auto-padding-side block-container">
        <div class="block-heading">
          <div class="block-title"><h2>Partners</h2></div>
        </div>
        <div class="block-content">
          <partner-carousel :datas="blocks.partner" />
        </div>
      </div>
      
      <div v-if="blocks.banner.length > 1" class="banner auto-padding-side block-container">
        <img :src="blocks.banner[1].image_url" @click="goToPost(blocks.banner[1])">
      </div>

      <post-block :posts="posts" />

      <div v-if="blocks.banner.length > 2" class="banner auto-padding block-container">
        <img :src="blocks.banner[2].image_url" @click="goToPost(blocks.banner[2])">
      </div>

      <install-app />

      <footer-block />

      <!-- <q-inner-loading :showing="loading">
        <q-spinner-facebook size="50px" color="primary"/>
      </q-inner-loading> -->
  </q-page>
</template>
<script>
import { mapActions, mapState } from 'vuex'
import ShoppingCart from 'components/ShoppingCart.vue'
import SwipeSlider from './block/SwipeSlider.vue'
import FeaturedCarousel from './../shared-components/FeaturedCarousel.vue'
import CategoryCarousel from './block/CategoryCarousel.vue'
import ProductBlock from './../shared-components/ProductBLock.vue'
import PostBlock from './../shared-components/PostBlock.vue'
import PartnerCarousel from 'components/PartnerCarousel.vue'
import InstallApp from 'components/InstallApp.vue'
import FooterBlock from './../shared-components/FooterBlock.vue'

export default {
  name: 'PageIndex',
  components: {
    ShoppingCart,
    SwipeSlider, 
    InstallApp, 
    ProductBlock, 
    PostBlock, 
    CategoryCarousel,
    FeaturedCarousel,
    PartnerCarousel,
    FooterBlock
  },
  data() {
    return {
      viewMode: 'grid',
      search: '',
      slide: 0,
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
      config: state => state.config
    }),
    cheight: function() {
      let n =(this.$q.screen.width /1.7)
      if(this.$q.screen.width > 600) {
        return 400 +'px'
      } else {
        return (this.$q.screen.width /1.5) +'px'
      }
    }
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
      if(block.post_id) {
        this.$router.push({name: 'FrontPostShow', params: { slug: block.post.slug }})
      }
    }
  },
  mounted() {
    if(this.config) {
      this.viewMode = this.config.home_view_mode
    }
  },
created() {
  if(!this.shop || !this.products.data.length || this.$route.query.load) {
      this.getInitialData()
    }
  }
}
</script>
