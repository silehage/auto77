<template>
  <div class="overflow-hidden glide-product">
    <vue-glide :options="glideOptions">
      <vue-glide-slide v-for="product in products" :key="product.id" class="relative box-shadow">
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
      products: Array
    },
    components: { SwiperProductCard },
    data() {
      return {
        pageWidth: 768,
        glideOptions: {
          rewind: false,
          perView: 2,
          gap: 8,
          bound: true,
          peek: {
           before: 0, 
           after: 50
          }
        }
      }
    },
    methods: {
      pageResize() {
        this.pageWidth = window.innerWidth
        console.log(this.pageWidth);
      }
    },
    created() {
      this.pageWidth = window.innerWidth
      
      window.addEventListener('resize', this.pageResize)

     if(this.pageWidth >= 768) {

        this.glideOptions.perView = 3
        this.glideOptions.gap = 12
        this.glideOptions.peek.after = 0

      } else if(this.pageWidth > 600) {

        this.glideOptions.perView = 2
        this.glideOptions.peek.after = 50


      }else if(this.pageWidth > 480) {

        this.glideOptions.gap = 5
        this.glideOptions.peek.after = 20

      }else if(this.pageWidth > 301) {

        this.glideOptions.gap = 5
        this.glideOptions.peek.after = 0

      }else {

        this.glideOptions.perView = 1
        this.glideOptions.gap = 4
        this.glideOptions.peek.after = 100
      }
    },
    beforeDestroy() {
      window.removeEventListener('resize', this.pageResize)
    },
  }
</script>
