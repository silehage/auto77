<template>
  <div>
    <component :is="isComponent" />
  </div>
</template>

<script>
export default {
  name: 'PageIndex',
  components: {
    'default': () =>  import('./Theme/Default/Index.vue'),
    'elegant': () => import('./Theme/Elegant/Index.vue'),
    'romance': () => import('./Theme/Romance/Index.vue'),
  },
  computed: {
    config() {
      return this.$store.state.config
    },
    isComponent: function() { 
      return this.config && this.config.theme ? this.config.theme : 'default'
    },
    loading() {
      return this.$store.state.loading
    }
  },
}
</script>
