<template>
  <div class="col-6 q-px-xs q-pb-xs q-mb-sm">
    <div class="column full-height relative bg-white">
      <q-img v-if="product.assets.length" :src="product.assets[0].src" :ratio="1" @click="show(product.slug)"/>
      <div class="relative col column justify-between q-pa-sm borfer-box">
        <div>
          <q-rating 
          readonly
          size="17px"
          v-model="rating"
          color="green-8"
          icon="star_border"
          icon-selected="star"
          icon-half="star_half"
        />
        <div class="text-subtitle2 ellipsis-2-lines">{{ product.title }}</div>
        </div>
        <div class="column q-gutter-y-xs q-py-xs">
          <div class="flex justify-between items-end">
            <div class="text-md text-weight-medium q-mt-sm text-green-7">{{ moneyIDR(product.price) }}</div>
            <favorite-button :product="product" />
          </div>
          <!-- <cart-button :product="product" /> -->
        </div>
      </div>
      <!-- <div class="absolute-top-right q-pa-xs z-50">
      </div> -->
    </div>
    </div>
</template>

<script>
// import CartButton from 'components/CartButton.vue'
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
    money(number) {
      return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(number)
    },
    show(slug) {
      this.$router.push({name: 'ProductShow', params: { slug: slug }})
    },
  }
}
</script>