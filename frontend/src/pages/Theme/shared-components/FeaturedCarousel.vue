<template>
  <div class="overflow-hidden">
    <swiper class="swiper" ref="mySwiper" :options="swiperOptions">
      <swiper-slide v-for="feature in datas" :key="feature.id">
       <div class="column col items-center text-center q-gutter-y-xs featured">
          <img v-if="feature.image" :src="feature.image_url" alt="" width="60" height="60" style="object-fit:contain;"/>
          <div class="text-sm text-weight-medium">{{ feature.label }}</div>
          <div v-if="feature.description" class="text-grey-7 text-auto description">{{ feature.description }}</div>
        </div>
      </swiper-slide>
    </swiper>
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
      swiperOptions: {
        slidesPerView: 3,
        spaceBetween: 5,
        freeMode: true,
      }
    }
  },
  created() {
    if(this.datas.length <= 3) {
      this.swiperOptions.slidesPerView = 3
    }
  },
  methods: {
    openCategory(id) {
      if(id) {
        this.$router.push({name: 'ProductCategory', params: {id:id}})
      }
    }
  }

}
</script>

<style>

</style>