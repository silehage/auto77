<template>
  <div class="overflow-hidden glide-product">
    <div class="row items-center q-gutter-x-sm q-py-sm">
      <div class="text-weight-bold text-lg text-secondary q-py-xs">{{ promo.label }}</div>
        <div class="row text-sm q-gutter-x-xs text-weight-bold">
          <div class="bg-dark text-white rounded-borders q-px-xs">{{ dayEl }}</div>
          <div class="bg-dark text-white rounded-borders q-px-xs">{{ hourEl }}</div>
          <div class="bg-dark text-white rounded-borders q-px-xs">{{ minuteEl }}</div>
          <div class="bg-dark text-white rounded-borders q-px-xs">
            <transition>
              <span>{{ secondEl  }}</span>
            </transition>
          </div>
        </div>
    </div>
    <vue-glide :options="glideOptions" class="">
      <vue-glide-slide v-for="product in promo.products" :key="product.id" class="relative">
        <swiper-product-card :product="product" />
      </vue-glide-slide>
    </vue-glide>  
  </div>
</template>

<script>
import SwiperProductCard from 'components/SwiperProductCard'
import { Api } from 'boot/axios'
  export default {
    name: 'GlideProduct',
    props: {
      promo: Object
    },
    components: { SwiperProductCard },
    data() {
      return {
        glideOptions: {
          rewind: false,
          perView: 2,
          gap: 8,
          bound: true,
          peek: {
           before: 0, 
           after: 80
          }
        },
        dayEl: 0,
        hourEl: 0,
        minuteEl: 0,
        secondEl: 0,
        countDownDate: null,
        interval: null,
        pageWidth: 768
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
    mounted() {
      if(this.promo) {
        this.startCoundown()
      }
    },
    methods: {
      pageResize() {
        this.pageWidth = window.innerWidth
      },
      startCoundown() {

        clearInterval(this.interval)

        this.countDownDate = new Date(this.promo.end_date).getTime();

        if (this.countDownDate) {

          this.interval = setInterval(this.moveTimer, 1000);

          this.moveTimer();
        } else {
          this.setTimer(0, 0, 0, 0);
        }
      },
      setTimer(days, hours, minutes, seconds) {
        this.dayEl = days < 10 ? '0' + days : days;
        this.hourEl = hours < 10 ? '0' + hours : hours;
        this.minuteEl = minutes < 10 ? '0' + minutes : minutes;
        this.secondEl = seconds < 10 ? '0' + seconds : seconds;
      },
      reInitialProduct() {
        Api().get('clear-initial-cache').then(() => {
         this.$store.dispatch('getInitialData')
        })
      },
      moveTimer() {
        var now = new Date().getTime();
        var distance = this.countDownDate - now;
        var days = Math.floor(distance / 86400000);
        var hours = Math.floor((distance % 86400000) / 3600000);
        var minutes = Math.floor((distance % 3600000) / 60000);
        var seconds = Math.floor((distance % 60000) / 1000);
 
         if (distance < 0) {
           clearInterval(this.interval);
           this.setTimer(0, 0, 0, 0);
           this.reInitialProduct()
          } else {
            
           this.setTimer(days, hours, minutes, seconds);
         }

      }
    },
    beforeDestroy(){
      clearInterval(this.interval)
      window.removeEventListener('resize', this.pageResize)
    }
  }
</script>
