<template>
  <div class="q-px-xs q-pb-xs q-mb-sm col-6">
    <q-card class="box-shadow full-height product-card">
        <div class="relative">
          <q-img v-if="product.assets.length" :src="product.assets[0].src" ratio="1" @click="show(product.slug)" class="cursor-pointer">
            <template v-slot:error>
              <div class="absolute-full flex flex-center">
                Cannot load image
              </div>
            </template>
          </q-img>
          <q-img v-else src="/static/no_image.png" ratio="1" @click="show(product.slug)" class="cursor-pointer">
          </q-img>
          <div class="absolute" style="bottom:5px;right:5px;z-index:99;">
            <favorite-button :product_id="product.id" />
          </div>
        </div>
        <q-card-section class="relative ovrflow-hidden">
          <div class="text-md ellipsis-2-lines q-mb-sm">{{ product.title }}</div>
          <div class="card-price-container">
            <div class="card-price">
              <span class="amount">Rp {{ product.price }}</span>
            </div>
          </div>  
        </q-card-section>
      </q-card>
    </div>
</template>

<script>
import CartButton from 'components/CartButton.vue'
import FavoriteButton from 'components/FavoriteButton.vue'
export default {
  name: 'ProductCard',
  props: { product: Object},
  components: { FavoriteButton },
  data() {
    return {
      rating: this.product.rating? parseFloat(this.product.rating) : 0.0,
      pageWidth: 800
    }
  },
  methods: {
    show(slug) {
      this.$router.push({name: 'ProductShow', params: {slug: slug}})
    },
    pageResize() {
      this.pageWidth = window.innerWidth
    }
  },
  created() {
    this.pageWidth = window.innerWidth
    window.addEventListener('resize', this.pageResize)
  },
  beforeDestroy() {
    window.removeEventListener('resize', this.pageResize)
  }
}
</script>