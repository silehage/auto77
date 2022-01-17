<template>
  <div class="col-6 q-px-xs q-pb-xs q-mb-sm">
    <div class="column full-height relative bg-white">
      <q-img v-if="product.assets.length" :src="product.assets[0].src" ratio="1" @click="show(product.id)"/>
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
        <div class="column q-gutter-y-xs">
          <div class="flex justify-between items-center">
            <div class="text-subtitle2 text-weight-medium q-mt-sm">{{ moneyIDR(product.price) }}</div>
          </div>
          <cart-button :product="product" />
        </div>
      </div>
      <div class="absolute-top-right q-pa-xs z-50">
        <favorite-button :product="product" />
      </div>
    </div>
    </div>
</template>

<script>
import CartButton from 'components/CartButton.vue'
import FavoriteButton from 'components/FavoriteButton.vue'
export default {
  name: 'ProductCard',
  props: { product: Object},
  components: { CartButton, FavoriteButton },
  data() {
    return {
      rating: this.product.rating? parseFloat(this.product.rating) : 0.0
    }
  },
  methods: {
    money(number) {
      return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(number)
    },
    show(id) {
      this.$router.push({name: 'ProductShow', params: {id: id}})
    },
  }
}
</script>