<template>
  <q-page :class="{'flex flex-center' : !galleries.available}" padding>
     <q-header class="box-shadow" :class="{ 'bg-secondary text-grey-1': $q.dark.isActive, 'bg-white text-dark': !$q.dark.isActive }">
       <q-toolbar class="header__padding">
         <q-btn to="/"
            flat round dense
            icon="eva-arrow-back" />
          <q-toolbar-title class="text-weight-bold brand">Photo Gallery</q-toolbar-title>
          <MenuRight  />
       </q-toolbar>
    </q-header>
      <div class="galleries q-py-lg" v-if="galleries.available">
          <img v-for="asset in galleries.data" :key="asset.id" :src="asset.src" style="width:100%;height:auto;" class="thumbnail"/>
      </div>
      <div class="text-grey-5 text-center" v-if="!galleries.available">Tidak ada data</div>
    <q-inner-loading :showing="!ready">
        <q-spinner-facebook size="50px" color="primary"/>
    </q-inner-loading>
  </q-page>
</template>

<script>
import MenuRight from 'components/MenuRight.vue'
export default {
  components: { MenuRight },
  data() {
    return {
      ready: false,
    }
  },
  computed: {
    galleries() {
      return this.$store.state.gallery.galleries
    }
  },
  created() {
    if(!this.galleries.data.length) {
      this.$store.dispatch('gallery/getFront').then(response => {
        this.$store.commit('gallery/SET_GALLERY', response.data.results)
      }).finally(() => this.ready = true)
    }else {
      this.ready = true
    }
  },
  meta() {
    return {
      title: this.post?.title ,
      meta: {
        ogTitle:  { property: 'og:title', content: this.post?.title},
        ogImage:  { property: 'og:image', content: this.post?.image_url },
      }
      
    }
  }
}
</script>

<style>
.thumbnail {
  border: 2px solid rgba(161, 161, 161, 0.432);
  border-radius: 8px;
}
.galleries {
  display: column;
  columns: 2;
  gap: .5rem;
}
.galleries img {
  break-inside: avoid;
}
</style>