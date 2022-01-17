<template>
  <div class="q-gutter-y-md q-pa-sm">
    <q-select 
    filled
    label="Provinsi"
    v-model="province" 
    readonly
    />
    <q-select 
    filled
    label="Kota / Kabupaten"
    v-model="city" 
    readonly
    />
    <q-select 
    filled
    label="Kecamatan"
    v-model="subdistrict" 
    readonly
    />
     <div class="q-pa-md bg-grey-2">
      <div class="label-text q-mb-sm">Alamat</div>
      <div v-html="address"></div>
    </div>
  </div>
</template>

<script>
export default {
  props: ['user'],
  data () {
    return {

      province: '',
      city: '',
      subdistrict: '',
      address: ''
    }
  },
  mounted() {
    if(this.user) {
      this.province = this.user.province
      this.city = this.user.city
      this.subdistrict = this.user.subdistrict
      this.address = this.user.address
    }
  }
}
</script>

<style>

</style>