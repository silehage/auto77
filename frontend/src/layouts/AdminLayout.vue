<template>
  <q-layout view="hHh lpR fFf">
    <q-page-container>
        <router-view />
    </q-page-container>
  </q-layout>
</template>
<script>
import { mapState } from 'vuex'
export default {
  name: 'AdminLayout',
  computed: {
        ...mapState({
      isCheckLogin: state => state.user.isCheckLogin,
      shop: state => state.shop,
      user: state => state.user
    })
  },
  created() {
    this.$store.dispatch('getAdminConfig')
    if(!this.shop) {
      
      this.$store.dispatch('getShop')
    }
    if(! this.user) {

      this.$store.dispatch('user/getUser')
    }
  }
}
</script>
