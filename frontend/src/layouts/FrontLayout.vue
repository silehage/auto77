<template>
  <q-layout view="hHh lpR fFf" class="front overflow-x-hidden">
    <q-page-container>
      <router-view />
    </q-page-container>
      <q-footer 
      class="footer-tab box-shadow-top"
      :class="{ 'text-white bg-secondary' : $q.dark.isActive , 'bg-white text-primary' : !$q.dark.isActive}"
      >
        <q-tabs
          :active-color="$q.dark.isActive ? 'white' : 'primary'"
          class="text-xs"
          :class="$q.dark.isActive ? 'text-grey-4' : 'text-grey-8'"
          no-caps
          dense
          switch-indicator
          :indicator-color="$q.dark.isActive ? 'text-grey-1' : 'primary'"
        >
          <q-route-tab
            icon="eva-home"
            label="Beranda"
            :to="{ name: 'Home'}"
            exact
          />

            <q-route-tab 
            icon="eva-search"
            :to="{name: 'ProductSearch'}"
            label="Cari"
            exact
          />

          <q-route-tab 
            icon="eva-shopping-bag"
            :to="{name: 'ProductIndex'}"
            label="Katalog"
            exact
          />

          <q-route-tab 
            icon="photo_library"
            :to="{name: 'GalleryShow'}"
            label="Gallery"
            exact
          />
   
          <q-route-tab 
            :to="{name: 'FrontPostIndex'}" 
            icon="eva-browser-outline" 
            exact
            label="Artikel" >
          </q-route-tab>

           <q-tab 
           icon="eva-person-outline" 
           @click="toDashboard"
           exact
           label="Akun" />
        </q-tabs>
      </q-footer>
  </q-layout>
</template>

<script>
import { mapGetters, mapState } from 'vuex'
import Cookies from 'js-cookie';
export default {
  name: 'FrontLayout',
  data () {
    return {
      tab: 'images',
      onsearch: false,
      search: '',
    }
  },
  computed: {
    ...mapGetters('cart', ['cartCount']),
    ...mapGetters('product', ['favoriteCount']),
    ...mapState({
      isCheckLogin: state => state.user.isCheckLogin,
      shop: state =>  state.shop,
      config: state => state.config,
      user: state => state.user.user,
      customer_services: state => state.customer_services
    }),
    logoWidth() {
      if(this.shop && this.shop.name) {
        return 'width:35px;height:35px;object:cover'
      } else {
        return 'width:auto;height:35px;object:contain'
      }
    }
  },
  methods: {
    toDashboard() {
      if(this.user){
        if(this.user.role == 'admin') {
          this.$router.push({name: 'Settings'})
        } else {
          this.$router.push({name: 'CustomerAccount'})
        }
      } else {
        this.$router.push({name: 'Login'})

      }
    }
  },
  mounted() {
    if(! this.shop) {
      this.$store.dispatch('getShop')
    }
    if(! this.config) {
      this.$store.dispatch('getConfig')
    } 
    if(Cookies.get('__token')) {
      if(!this.user) {
        this.$store.dispatch('user/getUser')
      }
    }

    if(!this.customer_services.length || this.$route.query.load) {

      setTimeout(() => {
         this.$store.dispatch('getCustomerService').then(response => {
           if(response.status == 200) {
            let cs = []
            cs = response.data.results

            if(!cs.length) {
              cs = [{
                name: 'Admin',
                phone: this.shop.phone
              }]
            }
            this.$store.commit('SET_CS', cs)
          }
          })
        }, 5000)
      
    }
  },
  created() {
    if(this.config) {
      this.$store.commit('SET_THEME_COLOR', this.config.theme_color)
    }
    this.$q.dark.set(true)
  },
  meta() {
    return {
      meta: {
        ogUrl:  { property: 'og:url', content: location.href },
        ogImage1:  { property: 'og:image', content: this.shop?.logo },
      }
      
    }
  }
}
</script>