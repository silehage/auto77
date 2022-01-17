<template>
<div class="overflow-hidden">
    <swiper class="swiper" :options="swiperOptions">
      <swiper-slide v-for="(img, index) in datas" :key="index">
        <img :src="img.src" style="width:100%;height:auto;"/>
      </swiper-slide>
      <div class="swiper-pagination swiper-pagination-white" slot="pagination"></div>
      <div class="swiper-button-prev swiper-button-white" slot="button-prev"></div>
      <div class="swiper-button-next swiper-button-white" slot="button-next"></div>
    </swiper>
  </div>
</template>

<script>
export default {
  name: 'Front',
  props: {
    datas: Array
  },
  data () {
    return {
      swiperOptions: {
        spaceBetween: 30,
        centeredSlides: true,
        autoHeight: true, //enable auto height
        autoplay: {
          delay: 4500,
          disableOnInteraction: false
        },
        pagination: {
          el: '.swiper-pagination',
          clickable: true,
          dynamicBullets: true
        },
        navigation: {
          nextEl: '.swiper-button-next',
          prevEl: '.swiper-button-prev'
        }
      }
    }
  }

}
</script>
