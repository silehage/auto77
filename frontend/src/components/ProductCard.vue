<template>
  <div class="col-6 q-px-xs q-pb-xs q-mb-sm">
    <div class="column full-height relative bg-white">
      <q-img v-if="product.assets.length" :src="product.assets[0].src" ratio="1" @click="show(product.slug)" class="cursor-pointer">
         <template v-slot:error>
          <div class="absolute-full flex flex-center bg-grey-6 text-white">
            Cannot load image
          </div>
        </template>
      </q-img>
      <q-img v-else src="/static/no_image.png" ratio="1" @click="show(product.slug)" class="cursor-pointer"></q-img>
      <div class="relative col column justify-between q-pa-sm borfer-box">
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
        <div class="flex justify-between items-end">
          <div>
            <div v-if="product.pricing.is_discount" class="text-subtitle2 text-weight-medium text-strike text-red-6">{{ moneyIDR(product.pricing.default_price) }}</div>
            <div class="text-md text-weight-medium text-green-7">{{ moneyIDR(product.pricing.current_price) }} </div>
          </div>
          <!-- <cart-button :product="product" /> -->
          <div>
          <favorite-button outline :product="product" />
          </div>
        </div>
      </div>
       <div v-if="product.pricing.is_discount" class="absolute top-0 q-pa-xs z-50 bg-red-6 text-white">{{ product.pricing.discount_percent }}%</div>
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