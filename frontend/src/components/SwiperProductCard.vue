<template>
  <div class="full-height">
    <div class="column full-height relative bg-white">
      <q-img v-if="product.assets.length" :src="product.assets[0].src" ratio="1" @click="show(product.slug)" class="cursor-pointer">
        <template v-slot:error>
          <div class="absolute-full flex flex-center bg-grey-6 text-white">
            Cannot load image
          </div>
        </template>
      </q-img>
      <q-img v-else src="/static/no_image.png" ratio="1" @click="show(product.slug)" class="cursor-pointer">
      </q-img>
      <div class="relative col column q-gutter-y-xs justify-between q-pb-md q-px-sm q-pt-sm overflow-hidden full-width">
        <div>
          <q-rating 
            readonly
            v-model="rating"
            color="primary"
            icon="star_border"
            icon-selected="star"
            icon-half="star_half"
            size="xs"
          />
          <div class="full-width q-mt-xs">
            <div class="product-card-title ellipsis-2-lines">{{ product.title }}</div>
          </div>
        </div>
        <div class="flex justify-between items-end">
          <div>
            <div v-if="product.pricing.is_discount" class="text-subtitle2 text-weight-medium text-strike text-red-6">{{ moneyIDR(product.pricing.default_price) }}</div>
            <div class="text-md text-weight-medium">{{ moneyIDR(product.pricing.current_price) }} </div>
          </div>
          <div>
          <favorite-button outline :product="product" />
          </div>
        </div>
      </div>
      <div v-if="product.pricing.is_discount" class="discount-badge">{{ product.pricing.discount_percent }}%</div>
    </div>
  </div>
</template>
<script>
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