<template>
  <div>
    <component :is="isActiveComponent" />
  </div>
</template>

<script>

import defaultTheme from 'pages/Theme/Default/Index.vue';
import romanceTheme from 'pages/Theme/Romance/Index.vue';
import elegantTheme from 'pages/Theme/Elegant/Index.vue';

export default {
  name: 'PageIndex',
  // components: {
  //   'default': () =>  import('./Theme/base/Index.vue'),
  //   'elegant': () => import('./Theme/elegant/Index.vue'),
  //   'romance': () => import('./Theme/romance/Index.vue'),
  // },
  components: { 
    elegant: romanceTheme, 
    default: defaultTheme, 
    romance: elegantTheme
   },
  computed: {
    isActiveComponent() { 
      return this.$store.getters['getTheme']
    },
    loading() {
      return this.$store.state.loading
    }
  },
}
</script>
