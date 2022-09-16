<template>
  <q-page class="q-pb-lg">
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
      </div>
      <q-inner-loading :showing="loading">
      </q-inner-loading>
  </q-page>
</template>

<script>
import Tampilan from './Tampilan.vue'
 export default {
  name: 'AdminConfig',
  components: { Tampilan},
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
