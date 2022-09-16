<template>
  <div class="q-px-xs q-pb-xs q-mb-sm" :class="pageWidth >= 800 ? 'col-4' : 'col-6'">
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
            <div v-if="product.pricing.is_discount" class="absolute z-50 bg-red-6 text-white" style="top:0px;left:0px;padding:2px;font-size:13px;">{{ product.pricing.discount_percent }}%</div>
        </div>
        <q-card-section class="relative ovrflow-hidden">
          <div class="text-md ellipsis-2-lines q-mb-sm">{{ product.title }}</div>
          <div class="card-price-container">
            <div v-if="product.pricing.is_discount" class="card-discount text-strike text-red-5">
              <span class="prefix">Rp</span>
              <span class="amount">{{ $money(product.pricing.default_price) }}</span>
            </div>
            <div class="card-price">
              <span class="prefix">Rp</span>
              <span class="amount">{{ $money(product.pricing.current_price) }}</span>
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