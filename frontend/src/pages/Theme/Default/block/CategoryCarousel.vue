<template>
  <div class="overflow-hidden">
    <vue-glide :options="glideOptions">
      <vue-glide-slide v-for="cat in datas" :key="cat.id">
        <div class="column full-height text-center">
          <q-img v-if="cat.filename" :src="cat.src" ratio="1"  @click="openCategory(cat.id)" class="cursor-pointer rounded-borders bg-white">
          </q-img>
          <div class="text-weight-medium text-category-auto text-center q-mt-sm">{{ cat.title }}</div>
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
      glideOptions: {
        perView: 4,
        gap: 16,
        bound: true
      }
    }
  },
  created() {
    this.setGlideOptions()
  },
  methods: {
    setGlideOptions() {
      if(this.datas.length <= 3) {
      this.glideOptions.perView = 3
      }else if(window.innerWidth > 575) {
        this.glideOptions.perView = 5
      }else if(window.innerWidth < 380) {
        this.glideOptions.perView = 3
        this.glideOptions.gap = 8
      }
    },
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