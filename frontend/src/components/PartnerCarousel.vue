<template>
<div class="overflow-hidden">
    <swiper ref="mySwiper" :options="swiperOptions">
      <swiper-slide v-for="(item, index) in datas" :key="index" class="text-center">
       <div  class="cursor-pointer column items-center">
          <div class="partner-img">
            <img v-if="item.image" :src="item.image_url" />
          </div>
          <div class="text-sm q-mt-sm text-weight-medium text-grey-9" v-if="item.label">
            {{ item.label }}
          </div>
        </div>
      </swiper-slide>
    </swiper>
  </div>
</template>

<script>
export default {
  name: 'PartnerCarousel',
  props: {
    datas: Array
  },
  data () {
    return {
       swiperOptions: {
        slidesPerView: 5,
        spaceBetween: 5,
        freeMode: true,
      }
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