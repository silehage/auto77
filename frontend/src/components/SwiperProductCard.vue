<template>
  <div class="full-height">
    <div class="column full-height relative bg-white">
      <q-img v-if="product.assets.length" :src="product.assets[0].src" ratio="1" @click="show(product.slug)" class="cursor-pointer"/>
      <div class="relative col column q-gutter-y-xs justify-between q-px-sm q-py-md overflow-hidden full-width">
        <div>
          <q-rating 
            readonly
            v-model="rating"
            color="primary"
            icon="star_border"
            icon-selected="star"
            icon-half="star_half"
            size="17px"
          />
          <div class="full-width">
            <div class="text-subtitle ellipsis-2-lines">{{ product.title }}</div>
          </div>
        </div>
        <div class="row justify-between items-end">
          <div class="text-md text-weight-medium text-green-8">{{ moneyIDR(product.price) }}</div>
          <!-- <cart-button :product="product" /> -->
          <favorite-button :product="product" />
        </div>
      </div>
        <div class="absolute-top-right q-ma-sm">
        </div>
    </div>
  </div>
</template>
<script>
import FavoriteButton from 'components/FavoriteButton.vue'
// import CartButton from 'components/CartButton.vue'
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
      this.$router.push({name: 'ProductShow', params: {slug: slug}})
    },
  }
}
</script>