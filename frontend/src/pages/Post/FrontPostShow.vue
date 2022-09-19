<template>
  <q-page :class="{'flex flex-center' : !ready}">
     <q-header class="box-shadow" :class="{ 'bg-dark text-grey-1': $q.dark.isActive, 'bg-white text-dark': !$q.dark.isActive }">
       <q-toolbar class="header__padding">
         <q-btn :to="{ name: 'FrontPostIndex' }"
            flat round dense
            icon="eva-arrow-back" />
          <q-toolbar-title class="text-weight-bold brand">Post</q-toolbar-title>
          <MenuRight  />
       </q-toolbar>
    </q-header>
    <template v-if="ready">
      <template v-if="post">
        <q-img :src="post.image_url" class="bg-grey-2 box-shadow"></q-img>
      <div class="auto-padding">
        <div v-if="post.tags" class="text-weight-bold text-orange-7">{{ post.tags }}</div>
        <h1 class="text-lg text-weight-medium q-mb-sm">{{ post.title }}</h1>
        <div class="text-grey-6 text-caption">{{ post.created_locale }}</div>
        <p class="q-mb-lg" v-html="post.body"></p>
      </div>
      <div class="q-my-lg q-pa-md" v-if="post.galleries.length">
        <div class="galleries">
            <img v-for="asset in post.galleries" :key="asset.id" :src="asset.src" style="width:100%;height:auto;" class="thumbnail"/>
        </div>
      </div>
      </template>
      <template v-else>
        Data tidak ditemukan
      </template>
    </template>
    <q-inner-loading :showing="!ready">
        <q-spinner-facebook size="50px" color="primary"/>
    </q-inner-loading>
  </q-page>
</template>

<script>
import { mapActions } from 'vuex'
import MenuRight from 'components/MenuRight.vue'
export default {
  components: { MenuRight },
  data() {
    return {
      ready: false,
      post: null
    }
  },
  methods: {
    ...mapActions('post', ['getSinglePostBySlug']),
    async getPost() {
      let { data } = await this.getSinglePostBySlug(this.$route.params.slug)
      this.post = data.results
      this.ready = true
    }
  },
  created() {
    if(!this.post || this.post.slug != this.$route.params.slug) {
      this.getPost()
    } else {
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