<template>
  <div class="col-6 q-px-xs q-pb-xs q-mb-sm">
    <div class="column full-height relative bg-white box-shadow">
      <q-img v-if="product.assets.length" :src="product.assets[0].src" ratio="1" @click="show(product.slug)" class="cursor-pointer">
         <template v-slot:error>
          <div class="absolute-full flex flex-center bg-grey-6 text-white">
            Cannot load image
          </div>
        </template>
      </q-img>
      <q-img v-else src="/static/no_image.png" ratio="1" @click="show(product.slug)" class="cursor-pointer"></q-img>
      <div class="relative col column justify-between q-pb-md q-px-sm q-pt-sm border-box">
        <div>
           <q-rating 
          readonly
          v-model="rating"
          color="primary"
          icon="star_border"
          icon-selected="star"
          icon-half="star_half"
        />
        <div class="text-subtitle2 ellipsis-2-lines">{{ product.title }}</div>
        </div>
        <div class="card-price-container">
          <div class="card-price text-weight-medium">{{ moneyIDR(product.pricing.current_price) }} </div>
          <div v-if="product.pricing.is_discount" class="card-discount text-subtitle2 text-weight-medium text-strike text-red-6">{{ moneyIDR(product.pricing.default_price) }}</div>
        </div>
      </div>
       <div class="absolute-top-right q-pa-xs">
        <favorite-button outline :product="product" />
      </div>
       <!-- <div v-if="product.pricing.is_discount" class="discount-badge">{{ product.pricing.discount_percent }}%</div> -->
    </div>
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
      rating: this.product.rating? parseFloat(this.product.rating) : 0.0
    }
  },
  methods: {
    show(slug) {
      this.$router.push({name: 'ProductShow', params: {slug: slug}})
    },
  }
}
</script>