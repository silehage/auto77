<template>
  <div class="overflow-hidden">
    <swiper ref="mySwiper" :options="swiperOptions">
      <swiper-slide v-for="cat in datas" :key="cat.id">
        <div class="column full-height bg-white text-center">
            <q-img v-if="cat.filename" :src="cat.src" ratio="1"  @click="openCategory(cat.id)" class="cursor-pointer">
                <div class="text-weight-medium text-auto absolute-full flex flex-center">{{ cat.title }}</div>
            </q-img>
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
        slidesPerView: 4,
        spaceBetween: 5,
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