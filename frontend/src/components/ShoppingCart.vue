<template>
  <div class="row items-center" :class="isDesktop ? 'q-gutter-x-sm' : 'q-gutter-x-xs'">
    <q-btn :to="{name: 'Cart'}" unelevated round :size="isDesktop ? '16px' : '14px'" :padding="isDesktop? '6px' : '4px'" dense color="white" text-color="grey-9" icon="shopping_cart">
      <q-badge v-if="cartCount > 0" color="pink" floating>{{ cartCount }}</q-badge>
    </q-btn>
    <q-btn :to="{name: 'ProductFavorite'}" color="white" :padding="isDesktop? '6px' : '4px'" unelevated round :size="isDesktop ? '16px' : '14px'" dense icon="favorite" text-color="grey-9" >
      <q-badge v-if="favoriteCount > 0" color="pink" floating>{{ favoriteCount }}</q-badge>
    </q-btn>
    <q-btn v-if="webShareApiSupported" :padding="isDesktop? '6px' : '4px'" @click="shareTheWeb" color="white" unelevated dense round :size="isDesktop ? '16px' : '14px'" icon="share" text-color="grey-9" >
    </q-btn>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
export default {
  props: ['imagename'],
  computed: {
    ...mapGetters('product', ['favoriteCount']),
    ...mapGetters('cart', ['cartCount']),
    isDesktop() {
      return this.$q.platform.is.desktop ? true : false
    },
    webShareApiSupported() {
      return navigator.share
    }
  },
  methods: {
    shareTheWeb() {
      const title = document.title;

      navigator.share({
        title: title,
        text: title,
        url: location.href,
      })
    }
  }
}
</script>
