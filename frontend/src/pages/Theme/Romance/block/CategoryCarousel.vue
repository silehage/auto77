<template>
  <div class="overflow-hidden">
    <swiper ref="mySwiper" :options="swiperOptions">
      <swiper-slide v-for="cat in datas" :key="cat.id">
        <div class="column full-height relative bg-white wisata-card">
          <q-img v-if="cat.filename" :src="cat.src" ratio="1" @click="openCategory(cat.id)" class="cursor-pointer rounded-borders">
            <div class="content">
              <div>
                <div class="text-weight-medium text-auto">{{ cat.title }}</div>
              </div>
            </div>
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
        spaceBetween: 15,
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