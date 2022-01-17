<template>
  <q-list class="relative bg-white">
    <q-item class="q-pa-md relative">
      <q-item-section avatar top @click.prevent="$router.push({name: 'ProductShow', params:{id: product.id}})" class="cursor-pointer column items-center q-gutter-y-sm">
        <q-img v-if="product.assets && product.assets.length" :src="product.assets[0].src" ratio="1" class="image-list rounded-borders"/>
        <q-rating 
          readonly
          v-model="rating"
          color="primary"
          icon="star_border"
          icon-selected="star"
          icon-half="star_half"
        />
      </q-item-section>
      <q-item-section top>
        <div class="cursor-pointer" @click.prevent="$router.push({name: 'ProductShow', params:{id: product.id}})">
          <q-item-label class="ellipsis-2-lines text-subtitle2 text-weight-medium">{{ product.title }}</q-item-label>
            <q-item-label caption class="ellipsis-2-lines q-mt-xs" v-html="getTeaser(product.description)"></q-item-label>
            <div class="text-subtitle2 q-pa-xs">{{ moneyIDR(product.price) }}</div>
        </div>
        <div style="margin-top:auto;">
          <div class="flex justify-between items-end">
            <q-chip size="sm" v-if="product.category">
              <q-avatar icon="local_offer" color="primary" text-color="white"></q-avatar>
              {{ product.category.title }}
            </q-chip>
            <cart-button :product="product" />
          </div>
        </div>
      </q-item-section>
      <div class="absolute-top-left z-50 q-pa-sm">
        <favorite-button :product="product" />
      </div>
    </q-item>
  </q-list>
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
    getTeaser(html) {
      if(html) {
        let strippedString = html.replace(/(<([^>]+)>)/gi, "");
        return strippedString.substr(0, 120)
      } else {
        return ''
      }
    },
  }
}
</script>