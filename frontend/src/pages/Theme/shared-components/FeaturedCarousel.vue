<template>
  <div class="overflow-hidden">
    <vue-glide :options="carouselOptions">
      <vue-glide-slide v-for="feature in datas" :key="feature.id">
        <div class="column col items-center text-center q-gutter-y-xs featured cursor-pointer" @click="showPost(feature)">
          <img v-if="feature.image" :src="feature.image_url" alt="" width="100" height="100" style="object-fit:cover;border-radius:50%;border:2px solid #fff;"/>
          <div class="text-sm text-weight-medium q-mt-sm">{{ feature.label }}</div>
          <div v-if="feature.description" class="text-grey-7 text-auto description">{{ feature.description }}</div>
        </div>
      </vue-glide-slide>
    </vue-glide>
  </div>
</template>

<script>
export default {
  name: 'CategoryCarousel',
  props: {
    datas: Array
  },
  data () {
    return {
      carouselOptions: {
        rewind: false,
        perView: 3,
        gap: 5,
        bound: true
      }
    }
  },
  methods: {
    showPost(featured) {
      if(featured.post) {
        this.$router.push({ name: 'FrontPostShow', params: { slug: featured.post.slug }})
      }
    }
  }
}
</script>

<style>

</style>