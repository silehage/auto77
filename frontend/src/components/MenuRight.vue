<template>
  <div class="row items-center" :class="isDesktop ? 'q-gutter-x-sm' : 'q-gutter-x-xs'">
    <q-btn @click="darkMode" :padding="isDesktop? '6px' : '5px'" flat round :size="isDesktop ? '16px' : '15px'" dense 
    :icon="$q.dark.isActive? 'eva-sun-outline' : 'eva-moon-outline'" 
    >
    </q-btn>
    <q-btn :to="{ name: 'ProductSearch' }" :padding="isDesktop? '6px' : '5px'" flat round :size="isDesktop ? '16px' : '15px'" dense icon="eva-search" >
    </q-btn>
    <q-btn v-if="showFavorite" :to="{name: 'ProductFavorite'}" :padding="isDesktop? '6px' : '5px'" flat round :size="isDesktop ? '16px' : '15px'" dense icon="eva-heart-outline" >
      <q-badge v-if="favoriteCount > 0" color="pink" floating>{{ favoriteCount }}</q-badge>
    </q-btn>
    <q-btn v-if="webShareApiSupported" :padding="isDesktop? '6px' : '5px'" @click="shareTheWeb" flat dense round :size="isDesktop ? '16px' : '15px'" icon="eva-share" >
    </q-btn>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
export default {
  name: 'MenuRight',
  props: {
    noFavorite: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    ...mapGetters('product', ['favoriteCount']),
    ...mapGetters('cart', ['cartCount']),
    isDesktop() {
      return this.$q.platform.is.desktop ? true : false
    },
    webShareApiSupported() {
      return navigator.share
    },
    showFavorite() {
      if(window.innerWidth < 400 && this.noFavorite) {
        return false
      }
      return true
    }

  },
  methods: {
    darkMode() {
      this.$q.dark.toggle()
    },
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
