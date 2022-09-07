<template>
  <q-page class="bg-grey-2 q-pb-lg">
    <q-header>
      <q-toolbar>
        <q-btn v-go-back.single
          flat round dense
          icon="eva-arrow-back" />
        <q-toolbar-title>
         Konfigurasi Aplikasi
        </q-toolbar-title>
      </q-toolbar>
      </q-header>
      <div class="q-gutter-y-lg q-pt-md" v-if="config">
        <tampilan />
        <theming />
        <checkout-config  />
        <service-fee />
        <shipping />
        <LocalShipping />
        <notification />
        <tripay />
        <system-update />
      </div>
      <q-inner-loading :showing="loading">
      </q-inner-loading>
  </q-page>
</template>

<script>
import Shipping from './Shipping.vue'
import Tampilan from './Tampilan.vue'
import Theming from './Theming.vue'
import CheckoutConfig from './CheckoutConfig.vue'
import Notification from './Notification.vue'
import Tripay from './Tripay.vue'
import SystemUpdate from './SystemUpdate.vue'
import LocalShipping from './LocalShipping.vue'
import ServiceFee from './ServiceFee.vue'
 export default {
  name: 'AdminConfig',
  components: { Shipping, Tampilan, Theming, Notification, Tripay, SystemUpdate, CheckoutConfig, LocalShipping, ServiceFee },
  computed: {
    loading() {
      return this.$store.state.loading
    },
    config() {
      return this.$store.state.config
    }
  },
  created() {
    if(!this.config) {
      this.$store.dispatch('getAdminConfig')
    }
  },
}
</script>
