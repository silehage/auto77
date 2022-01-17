<template>
  <q-page class="bg-white" :class="{'flex flex-center' : !ready}">
    <q-header class="text-primary bg-white">
       <q-toolbar>
         <q-btn v-go-back.single
            flat round dense
            icon="arrow_back" />
          <q-toolbar-title class="text-weight-bold brand">Post</q-toolbar-title>
       </q-toolbar>
    </q-header>
    <template v-if="ready">
      <template v-if="post">
      <q-img :src="post.image_url" class="bg-grey-2"></q-img>
      <div class="q-pa-md">
        <div v-if="post.tags" class="text-weight-bold text-orange-7">{{ post.tags }}</div>
        <h2 class="text-lg q-mt-sm q-mb-md text-weight-medium">{{ post.title }}</h2>
        <div class="text-weight-medium text-grey-7">{{ post.created_locale }}</div>
        <div class="q-my-md">
          <div v-html="post.body"></div>
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
export default {
  data() {
    return {
      post: null,
      ready: false
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
   this.getPost()
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

</style>