<template>
  <div>
    <div class="row items-center" :class="isDesktop ? 'q-gutter-x-sm' : 'q-gutter-x-xs'">
      <q-btn @click="darkMode" :padding="isDesktop? '6px' : '5px'" flat round :size="isDesktop ? '16px' : '15px'" dense 
      :icon="$q.dark.isActive? 'eva-sun-outline' : 'eva-moon-outline'" 
      >
      </q-btn>
      <!-- <q-btn :to="{ name: 'ProductSearch' }" :padding="isDesktop? '6px' : '5px'" flat round :size="isDesktop ? '16px' : '15px'" dense icon="eva-search" >
      </q-btn> -->
      <q-btn v-if="showFavorite" :to="{name: 'ProductFavorite'}" :padding="isDesktop? '6px' : '5px'" flat round :size="isDesktop ? '16px' : '15px'" dense icon="eva-heart-outline" >
        <q-badge v-if="favoriteCount > 0" color="pink" floating>{{ favoriteCount }}</q-badge>
      </q-btn>
      <q-btn :padding="isDesktop? '6px' : '5px'" @click="shareModal = true" flat dense round :size="isDesktop ? '16px' : '15px'" icon="eva-share" >
      </q-btn>
    </div>
    <q-dialog v-model="shareModal" position="bottom">
      <q-card class="card-lg">
         <q-linear-progress :value="1" color="pink" />
        <q-card-section>
          <div class="flex justify-between items-center q-pb-md">
            <div class="row items-center">
              <q-avatar>
                <q-icon name="eva-share"></q-icon>
              </q-avatar>
              <div class="text-lg">Share</div>
            </div>
            <q-btn icon="eva-close" flat padding="xs" v-close-popup></q-btn>
          </div>
          <div style="min-height:100px;">
            <q-list>
              <q-item clickable  @click="shareNow('whatsapp')">
                <q-item-section avatar>
                  <q-avatar text-color="white" color="green-6">
                     <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 50 50" width="20px" height="20px">
                      <g id="surface1441897">
                      <path style=" stroke:none;fill-rule:nonzero;fill:currentColor;fill-opacity:1;" d="M 25 2 C 12.316406 2 2 12.316406 2 25 C 2 28.960938 3.023438 32.855469 4.964844 36.289062 L 2.035156 46.730469 C 1.941406 47.074219 2.035156 47.441406 2.28125 47.695312 C 2.472656 47.894531 2.734375 48 3 48 C 3.078125 48 3.160156 47.988281 3.238281 47.972656 L 14.136719 45.273438 C 17.464844 47.058594 21.210938 48 25 48 C 37.683594 48 48 37.683594 48 25 C 48 12.316406 37.683594 2 25 2 Z M 36.570312 33.117188 C 36.078125 34.476562 33.71875 35.722656 32.585938 35.886719 C 31.566406 36.035156 30.277344 36.101562 28.863281 35.65625 C 28.007812 35.386719 26.90625 35.027344 25.496094 34.429688 C 19.574219 31.902344 15.707031 26.011719 15.410156 25.625 C 15.117188 25.234375 13 22.464844 13 19.59375 C 13 16.726562 14.523438 15.3125 15.066406 14.730469 C 15.609375 14.144531 16.246094 14 16.640625 14 C 17.035156 14 17.429688 14.003906 17.773438 14.019531 C 18.136719 14.039062 18.625 13.882812 19.101562 15.023438 C 19.59375 16.191406 20.777344 19.058594 20.921875 19.351562 C 21.070312 19.644531 21.167969 19.984375 20.972656 20.375 C 20.777344 20.761719 20.679688 21.007812 20.382812 21.347656 C 20.085938 21.6875 19.761719 22.105469 19.496094 22.367188 C 19.199219 22.660156 18.894531 22.976562 19.238281 23.558594 C 19.582031 24.144531 20.765625 26.050781 22.523438 27.597656 C 24.777344 29.585938 26.679688 30.199219 27.269531 30.492188 C 27.859375 30.785156 28.203125 30.734375 28.550781 30.347656 C 28.894531 29.957031 30.023438 28.644531 30.417969 28.058594 C 30.8125 27.476562 31.203125 27.574219 31.746094 27.769531 C 32.289062 27.960938 35.191406 29.371094 35.78125 29.664062 C 36.371094 29.957031 36.765625 30.101562 36.914062 30.34375 C 37.0625 30.585938 37.0625 31.753906 36.570312 33.117188 Z M 36.570312 33.117188 "/>
                      </g>
                    </svg>
                  </q-avatar>
                </q-item-section>
                <q-item-section>
                  Share Whatsapp
                </q-item-section>
              </q-item>
              <q-item clickable  @click="shareNow('facebook')">
                <q-item-section avatar>
                  <q-avatar text-color="white" icon="eva-facebook" color="fb"></q-avatar>
                </q-item-section>
                <q-item-section>
                  Share facebook
                </q-item-section>
              </q-item>
              <q-item clickable  @click="shareNow('twitter')">
                <q-item-section avatar>
                  <q-avatar text-color="white" icon="eva-twitter" color="twitter"></q-avatar>
                </q-item-section>
                <q-item-section>
                  Share Twitter
                </q-item-section>
              </q-item>
              <q-item clickable  @click="copyLink">
                <q-item-section avatar>
                  <q-avatar text-color="white" icon="eva-copy" color="pink"></q-avatar>
                </q-item-section>
                <q-item-section>
                  Salin Link
                </q-item-section>
              </q-item>
            </q-list>
            <!-- <q-btn size="18px" @click="shareNow('facebook')" icon="eva-facebook" round color="fb"></q-btn>
            <q-btn size="18px" @click="shareNow('twitter')" icon="eva-twitter" round color="twitter"></q-btn>
            <q-btn size="18px" @click="shareNow('whatsapp')" round color="green">
               <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 50 50" width="20px" height="20px">
              <g id="surface1441897">
              <path style=" stroke:none;fill-rule:nonzero;fill:currentColor;fill-opacity:1;" d="M 25 2 C 12.316406 2 2 12.316406 2 25 C 2 28.960938 3.023438 32.855469 4.964844 36.289062 L 2.035156 46.730469 C 1.941406 47.074219 2.035156 47.441406 2.28125 47.695312 C 2.472656 47.894531 2.734375 48 3 48 C 3.078125 48 3.160156 47.988281 3.238281 47.972656 L 14.136719 45.273438 C 17.464844 47.058594 21.210938 48 25 48 C 37.683594 48 48 37.683594 48 25 C 48 12.316406 37.683594 2 25 2 Z M 36.570312 33.117188 C 36.078125 34.476562 33.71875 35.722656 32.585938 35.886719 C 31.566406 36.035156 30.277344 36.101562 28.863281 35.65625 C 28.007812 35.386719 26.90625 35.027344 25.496094 34.429688 C 19.574219 31.902344 15.707031 26.011719 15.410156 25.625 C 15.117188 25.234375 13 22.464844 13 19.59375 C 13 16.726562 14.523438 15.3125 15.066406 14.730469 C 15.609375 14.144531 16.246094 14 16.640625 14 C 17.035156 14 17.429688 14.003906 17.773438 14.019531 C 18.136719 14.039062 18.625 13.882812 19.101562 15.023438 C 19.59375 16.191406 20.777344 19.058594 20.921875 19.351562 C 21.070312 19.644531 21.167969 19.984375 20.972656 20.375 C 20.777344 20.761719 20.679688 21.007812 20.382812 21.347656 C 20.085938 21.6875 19.761719 22.105469 19.496094 22.367188 C 19.199219 22.660156 18.894531 22.976562 19.238281 23.558594 C 19.582031 24.144531 20.765625 26.050781 22.523438 27.597656 C 24.777344 29.585938 26.679688 30.199219 27.269531 30.492188 C 27.859375 30.785156 28.203125 30.734375 28.550781 30.347656 C 28.894531 29.957031 30.023438 28.644531 30.417969 28.058594 C 30.8125 27.476562 31.203125 27.574219 31.746094 27.769531 C 32.289062 27.960938 35.191406 29.371094 35.78125 29.664062 C 36.371094 29.957031 36.765625 30.101562 36.914062 30.34375 C 37.0625 30.585938 37.0625 31.753906 36.570312 33.117188 Z M 36.570312 33.117188 "/>
              </g>
            </svg>
            </q-btn> -->
          </div>
        </q-card-section>
      </q-card>
    </q-dialog>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import { copyToClipboard } from 'quasar'
export default {
  name: 'MenuRight',
  props: {
    noFavorite: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      shareModal: false
    }
  },
  computed: {
    ...mapGetters('product', ['favoriteCount']),
    ...mapGetters('cart', ['cartCount']),
    shop() {
      return this.$store.state.shop
    },
    fb_init() {
      return this.$store.state.fb_init
    },
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
    },
    linkPath() {
       if(this.$route.name == 'ProductShow' && this.shop) {
        return this.shop.app_url + this.$route.fullPath
       }

      return this.shop ? this.shop.app_url : ''
    }

  },
  methods: {
    copyLink() {
      copyToClipboard(this.linkPath)
        .then(() => {
         this.$q.notify({
          type: 'positive',
          message: 'Berhasil menyalin link'
         })
        })
        .catch(() => {
          // fail
        })
    },
    darkMode() {
      this.$q.dark.toggle()
    },
    shareNow(type) {

      let url = ''
      let text = ''

      if(type == 'facebook') {
        if(window.innerWidth <= 768 && this.shop.facebook_app_id) {

          url = `https://www.facebook.com/dialog/feed?app_id=${this.shop.facebook_app_id}&display=popup&link=${this.linkPath}&redirect_uri=${this.linkPath}`

        }else {
          
          url = 'https://facebook.com/share.php'
  
          url += `?u=${this.shop.app_url}&t=${this.linkPath}`
        }
      }
      if(type == 'twitter') {
        text = encodeURI(this.shop.name + '\n') + this.linkPath
        url = `https://twitter.com/home?status=${text}`
      }
      if(type == 'whatsapp') {
        text = encodeURI(this.shop.name + '\n') + this.linkPath
        url = `https://api.whatsapp.com/send?text=${text}`
      }

      // console.log(url); return

      window.open(url, '_blank')
      this.shareModal = false
    }
  }
}
</script>

<style >
  .text-fb {
    color: #4267B3;
  }
  .bg-fb {
    background: #4267B3;
  }
  .text-twitter {
    color: #1DA1f2;
  }
  .bg-twitter {
    background: #1DA1f2;
  }
</style>
