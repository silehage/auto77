<template>
  <q-list class="relative bg-white">
    <q-item class="q-pa-md relative">
      <q-item-section avatar top @click.prevent="$router.push({name: 'ProductShow', params:{id: product.id}})" class="cursor-pointer column items-center q-gutter-y-sm">
        <q-img v-if="product.assets && product.assets.length" :src="product.assets[0].src" ratio="1" class="image-list rounded-borders">
          <template v-slot:error>
          <div class="absolute-full flex flex-center bg-grey-6 text-white text-center">
            Cannot load image
          </div>
        </template>
        </q-img>
        <q-img v-else src="/static/no_image.png" ratio="1" class="image-list rounded-borders"/>
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
        <div class="cursor-pointer" @click.prevent="$router.push({name: 'ProductShow', params:{slug: product.slug}})">
          <q-item-label class="ellipsis-2-lines text-subtitle2 text-weight-medium">{{ product.title }}</q-item-label>
            <q-item-label caption class="ellipsis-2-lines q-mt-xs" v-html="getTeaser(product.description)"></q-item-label>
            <div class="flex items-center q-gutter-x-md q-mt-md">
            <div class="text-subtitle1 text-green-6 text-weight-bold">{{ moneyIDR(product.pricing.current_price) }}</div>
            <div v-if="product.pricing.is_discount" class="text-subtitle2 text-weight-medium text-strike text-red-6">{{ moneyIDR(product.pricing.default_price) }}</div>
            </div>
        </div>
        <div style="margin-top:auto;">
          <div class="flex justify-between items-end">
            <q-chip size="sm" v-if="product.category">
              <q-avatar icon="local_offer" color="primary" text-color="white"></q-avatar>
              {{ product.category.title }}
            </q-chip>
            <div class="q-gutter-sm">
              <q-btn unelevated size="sm" round color="green-6" icon="visibility" :to="{name: 'ProductShow', params:{slug: product.slug}}"></q-btn>
              
              <favorite-button outline :product="product" />
            </div>
          </div>
        </div>
      </q-item-section>
       <div v-if="product.discount" class="absolute top-0 q-pa-xs z-50 bg-red-6 text-white">{{ product.display_discount }}</div>
    </q-item>
  </q-list>
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