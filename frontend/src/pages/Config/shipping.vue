<template>
  <div>
    <q-card flat>
      <q-card-section>
        <div class="flex items-center justify-between">
          <div class="text-md text-weight-bold">Ekspedisi</div>
          <div class="q-px-sm rounded-borders text-white" :class="config && config.can_shipping? 'bg-green-7' : 'bg-grey-6'">{{ config && config.can_shipping? 'Active' : 'Disabled' }}</div>
        </div>
        <div class="text-caption text-grey-7">
          <div>Pengaturan ongkir otomatis by Rajaongkir</div>
          <div>Silahkan daftar di rajaongkir.com untuk mendapatkan apikey</div>
        </div>
        <div class="q-gutter-y-sm q-py-md">
          <q-select label="Raja Ongkir Tipe" filled :options="rajaongkirtypes" v-model="form.rajaongkir_type" @input="selectCourierType"></q-select>
          <q-input
          filled
          v-model="form.rajaongkir_apikey"
          label="Raja Ongkir API KEY"
          >
          </q-input>
        </div>
        <div v-if="config && config.is_shippable" class="q-mt-lg">
          <div class="text-grey-8 text-sm q-py-sm">Pengaturan Gudang Pengiriman</div>
            <div @click="changeWarehouse" class="cursor-pointer q-pa-md full-width border q-filled">{{ form.warehouse_address ? form.warehouse_address : 'Pilih Gudang Pengiriman' }}</div>
            <div class="q-mt-md" v-if="theCouriers.length">
              <div class="text-grey-8 text-sm q-py-sm">Pilih Kurir</div>
                <div class="q-gutter-sm">
                  <q-btn unelevated rounded size="10px" v-for="(name, index) in theCouriers" :key="index" 
                  :color="isCourierActive(name)? 'green-5' : 'grey-5'" @click="handleSelectCourier(name)" :label="name"></q-btn>
                </div>
            </div>
        </div>
      </q-card-section>
      <q-card-section v-if="config && config.is_shippable">
        <div>
          <div class="text-md text-weight-bold">Pengaturan COD</div>
          <div class="q-mb-sm text-caption text-grey-7">Pengaturan kota tujuan untuk pengiriman COD <br>( Kosongkan untuk menonaktifkan ) </div>
          <div class="q-pa-sm q-gutter-y-sm">
            <q-select 
              filled
              borderless
              options-cover
              v-model="province_id"
              :options="provinceOptions"
              label="Pilih Provinsi"
              emit-value
              map-options
              @input="provinceCodSelected"
              />
            <q-select  
              options-cover
              stack-label
              filled
              borderless
              v-model="form.cod_list"
              label="Pilih Kabupaten / Kota"
              :options="cityCodOptions"
              use-chips
              multiple
              />
          </div>
        </div>
      </q-card-section>
      <q-card-actions class="q-pa-md justify-end">
        <q-btn unelevated size="12px" @click="updateData" label="Simpan Ekspedisi" color="blue-7"></q-btn>
      </q-card-actions>
    </q-card>
    <q-dialog v-model="modal">
      <q-card style="width:100%;max-width:400px;">
        <q-card-section>
          <div class="text-md text-weight-bold q-mb-sm">Pilih Gudang Pengiriman</div>
          <div class="q-pa-sm">
            <q-select 
              v-model="province_id"
              :options="provinceOptions"
              label="Pilih Provinsi"
              emit-value
              map-options
              @input="provinceSelected"
              />
            <q-select 
              v-model="city_id"
              label="Pilih Kabupaten / Kota"
              :options="cityOptions"
              @input="citySelected"
              emit-value
              map-options
              :loading="loading"
              />
          </div>
        </q-card-section>
      </q-card>
    </q-dialog>
  </div>
</template>

<script>
import { Api } from 'boot/axios'
export default {
  props: ['config'],
  data () {
    return {
      codListModal: false,
      loading: false,
      modal: false,
      rajaongkirtypes: ['starter', 'basic', 'pro'],
      provinceOptions: [],
      subdistrictOptions: [],
      cityOptions: [],
      cityCodOptions: [],
      form: {
        rajaongkir_type: '',
        rajaongkir_apikey: '',
        warehouse_id: '',
        warehouse_address: '',
        rajaongkir_couriers: '',
        cod_list: []
      },
      province_id: '',
      city_id: '',
    }
  },
  computed: {
    courierHasSelect() {
      if(this.form.rajaongkir_couriers){
        return this.form.rajaongkir_couriers.split(',')
      } else {
        return []
      }
    },
    theCouriers() {
      if(this.form.rajaongkir_type == 'pro') {
        return this.config.courier_available.pro
      } else if(this.form.rajaongkir_type == 'basic'){
        return this.config.courier_available.basic
      } else {
        return this.config.courier_available.starter
      }
    }
  },
  mounted() {
    if(this.config) {
      this.setData()
      this.getProvince()
    }
  },
  methods: {
    changeWarehouse() {
      this.modal =true
    },
    setData() {
      this.form.rajaongkir_type = this.config.rajaongkir_type
      this.form.rajaongkir_apikey = this.config.rajaongkir_apikey
      this.form.rajaongkir_couriers = this.config.rajaongkir_couriers
      this.form.warehouse_address = this.config.warehouse_address
      this.form.warehouse_id = this.config.warehouse_id
      this.form.cod_list = this.config.cod_list
    },
    selectCourierType() {
      this.form.rajaongkir_couriers = ''
    },
    isCourierActive(name) {
      if(this.courierHasSelect.length) {

        if(this.courierHasSelect.includes(name)) {
          return true

        } else {
          return false
        }
      }
      return false
    },
    handleSelectCourier(evt) {
      let courierTemp = this.courierHasSelect

      if(courierTemp.includes(evt)) {
        courierTemp = courierTemp.filter(el => el != evt)
      } else {
        courierTemp.push(evt)
      }
      if(courierTemp.length) {
        this.form.rajaongkir_couriers = courierTemp.join(',')
      }
    },
    showNotify(error = '') {
      if(error) {
        this.$q.notify({
          type: 'negative',
          message: 'Gagal memperbarui data'
        })
      } else {
        this.$q.notify({
          type: 'positive',
          message: 'Berhasil memperbarui data'
        })
      }
      
    },
    updateData() {
      let data = {...this.config, ...this.form}
      Api().post('config',  this.form).then(() => {
        this.$store.dispatch('getAdminConfig')
        if(!this.provinceOptions.length) {
          this.getProvince()
        }
        this.showNotify()
      }).catch(() => {
        this.showNotify(error)    
      })
    },
    submitWarehouse() {
      this.updateData()
    },
    setLoading(status) {
      this.$store.commit('SET_LOADING', status)
    },
    getProvince() {    
      if(!this.form.rajaongkir_apikey) return
      this.setLoading(true)
      this.provinceOptions = []
      Api().get('shipping/getProvince').then(response => {
       if(response.status == 200) {
          let res = response.data.rajaongkir.results

         res.forEach(el => {
            let opts = { label: el.province, value: el.province_id }
            this.provinceOptions.push(opts)
         });
       }
      }).finally(() => {
        this.setLoading(false)
      })
    },
    getCity() {
      this.loading = true
      this.setLoading(true)
      Api().get('shipping/getCity/'+ this.province_id)
      .then(response => {
        if(response.status == 200) {
          let res = response.data.rajaongkir.results
          res.forEach(el => {
              let opts = { label:el.type + ' ' + el.city_name, value: el.city_id }
              this.cityOptions.push(opts)
          });
        }
      })
      .finally(() => {
         this.setLoading(false)
         this.loading = false
      })
    },
    getSubdistrict() {
       this.setLoading(true)
      Api().get('shipping/getSubdistrict/'+ this.city_id)
      .then(response => {
        if(response.status == 200) {
          let res = response.data.rajaongkir.results
          res.forEach(el => {
              let opts = { label:el.subdistrict_name, value: el.subdistrict_id }
              this.subdistrictOptions.push(opts)
          });
        }
      })
      .finally(() => {
         this.setLoading(false)
      })
    },
    provinceCodSelected() {
      this.setLoading(true)
      Api().get('shipping/getCity/'+ this.province_id)
      .then(response => {
        if(response.status == 200) {
          let res = response.data.rajaongkir.results
          res.forEach(el => {
              let opts = { label:el.type + ' ' + el.city_name, value: el.city_id }
              this.cityCodOptions.push(opts)
          });
        }
      })
      .finally(() => {
         this.setLoading(false)
      })
    },
    provinceSelected() {
      this.cityOptions = []
      this.city_id = ''
      if(this.province_id) { 
        this.getCity()
      }
    },
    citySelected() {
      let province = this.provinceOptions.find(el => el.value == this.province_id)
      let city = this.cityOptions.find(el => el.value == this.city_id)

      this.form.warehouse_id = this.city_id
      this.form.warehouse_address = city.label + ', ' + province.label
      this.modal = false
    }
  }
}
</script>
