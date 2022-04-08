<template>
  <div class="overflow-hidden">
    <vue-glide>
    <vue-glide-slide v-for="product in products.items" :key="product.id">
      <swiper-product-card :product="product" />
    </vue-glide-slide>
  </vue-glide>  
  </div>
</template>

<script>
import SwiperProductCard from 'components/SwiperProductCard'
  export default {
    name: 'GlideProduct',
    props: {
      products: Object
    },
    components: { SwiperProductCard },
    data() {
      return {
        glideOptions: {
          perView: 2,
          gap: 8,
        }
      }
    },
  }
</script>
