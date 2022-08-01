<template>
  <div>
    <component :is="isActiveComponent" />
    <ThemeSetting />
  </div>
</template>

<script>

// import defaultTheme from 'pages/Theme/Default/Index.vue';
// import romanceTheme from 'pages/Theme/Romance/Index.vue';
// import elegantTheme from 'pages/Theme/Elegant/Index.vue';
import ThemeSetting from 'components/ThemeSetting.vue'

export default {
  name: 'PageIndex',
  components: {
    'default': () =>  import('pages/Theme/Default/Index.vue'),
    'elegant': () => import('pages/Theme/Elegant/Index.vue'),
    'romance': () => import('pages/Theme/Romance/Index.vue'),
    ThemeSetting
  },
  // components: { 
  //   elegant: elegantTheme, 
  //   // default: defaultTheme, 
  //   // romance: romanceTheme
  //  },
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
