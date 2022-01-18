<template>
  <q-page class="bg-grey-1 romance" :class="{'flex flex-center' : loading }">
    <template v-if="!loading">
      <q-header reveal :reveal-offset="10" class="bg-white box-shadow">
        <q-toolbar class="q-py-md">
          <img v-if="shop" class="logo" :src="shop.logo? shop.logo : '/icon/logo.png'" />
          <div class="col q-ml-md row items-center">
            <div class="col">
              <q-input ref="input" color="grey-4" standout="bg-primary text-white" dense class="text-xs" v-model="search" @keyup.enter="searchNow" placeholder="ketik nama produk..."
                >
                <template v-slot:append>
                  <q-icon
                    name="search"
                    class="cursor-pointer"
                    @click="searchNow"
                  />
                </template>
              </q-input>
            </div>
            <div class="q-pl-sm">
              <shopping-cart />
            </div>
            </div>
        </q-toolbar>
      </q-header>
      <div class="header-romance">
        <div v-if="sliders.data.length">
          <swipe-slider :datas="sliders.data" />
        </div>
      </div>
      <div id="featured" class="auto-padding-side block-container" v-if="blocks.featured.length">
        <featured-carousel :datas="blocks.featured" class="q-pt-md"/>
      </div>
      <div id="categories" v-if="categories && categories.data.length > 1" class="auto-padding-side block-container">
        <div class="block-heading">
          <div class="block-title"><h2>Kategori</h2></div>
        </div>
        <div class="q-pb-sm">
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
    </template>
      <q-inner-loading :showing="loading">
        <q-spinner-facebook size="50px" color="primary"/>
      </q-inner-loading>
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
      config: state => state.config,
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
    goToPost(block) {
      if(block.post_id) {
        this.$router.push({name: 'FrontPostShow', params: { slug: block.post.slug }})
      }
    },
    searchNow() {
      if(!this.search || this.search == '') return
        this.$router.push({name: 'ProductSearch', query: {q: this.search }})
    }
  },
  created() {
  if(!this.shop || !this.products.data.length || this.$route.query.load) {
      this.getInitialData()
    }
  }
}
</script>
