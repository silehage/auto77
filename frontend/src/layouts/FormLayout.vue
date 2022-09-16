<template>
  <q-layout view="hHh lpR fFf">
    <q-page-container style="padding-bottom:4rem;">
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script>
export default {
  computed: {
    config() {
      return this.$store.state.config
    }
  },
  created() {
    // this.$q.dark.set(false)
    if(this.config) {
      this.$store.commit('SET_THEME_COLOR', this.config.theme_color)
    }else {
       this.$store.dispatch('getConfig')
    }
  },
}
</script>