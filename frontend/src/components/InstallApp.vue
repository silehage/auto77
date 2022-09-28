<template>
  <div>
    <div class="bg-white q-pa-xs" v-show="isShowBanner">
      <q-list dense>
        <q-item>
          <q-item-section side>
            <q-btn icon="eva-close" flat color="dark" dense round padding="xs" @click="closeBanner"></q-btn>
          </q-item-section>
          <q-item-section class="text-dark">
            <q-item-label class="text-grey-9 text-right text-weight-bold">Unduh Aplikasi</q-item-label>
          </q-item-section>
          <q-item-section side>
              <img class="cursor-pointer" src="/static/app.svg" alt="Unduh Aplikasi" height="30" @click="installNow">
          </q-item-section>
        </q-item>
      </q-list>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex'
import Cookies from 'js-cookie'
export default {
  data() {
    return {
      isShowBanner: false
    }
  },
  mounted() {
    setTimeout(() => {
      this.checkBanner()
    }, 2000)
  },
  computed: {
    ...mapState({
      shop: state => state.shop,
    }),
  },  
 
  methods: {
    checkBanner() {
       if(this.shop && this.shop.google_play_url) {
        let isHidden = Cookies.get('__hideInstallBanner')
        if(isHidden == undefined || isHidden == 0) {
          this.isShowBanner = true
        }
       }
    },
    installNow() {
      window.open(this.shop.google_play_url, '_blank')
    },
    closeBanner() {
      this.isShowBanner = false
      Cookies.remove('__hideInstallBanner')
      Cookies.set('__hideInstallBanner', 1, { expires: 3 })
    }
  },

}
</script>
