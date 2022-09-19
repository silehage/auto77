<template>
  <div class="overflow-hidden">
    <swiper ref="mySwiper" :options="swiperOptions">
      <swiper-slide  v-for="(img, index) in datas" :key="index">
        <img :src="img.src" style="width:100%;height:auto;"/>
      </swiper-slide>
    </swiper>
  </div>
</template>

<script>
  export default {
    name: 'SwipperProduct',
    props: {
     datas: Array
    },
    data() {
      return {
        swiperOptions: {
          slidesPerView: 1,
          autoHeight: true,
          loop: true
        }
      }
    },
  }
</script>
