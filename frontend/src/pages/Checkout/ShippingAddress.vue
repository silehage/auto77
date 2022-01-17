<template>
  <div>
    <div id="shipping-address">
      <div class="text-md q-px-sm q-pt-md q-pb-xs">Penerima</div>
      <div class="q-gutter-y-md q-pa-sm">
        <q-input
        filled
        square
        stack-label
        label="Nama Penerima"
        v-model="form.customer_name"
        @input="inputFormUser"
        />
        <q-input
        v-if="canEmail"
        filled
        square
        stack-label
        type="email"
        required
        label="Alamat Email"
        v-model="form.customer_email"
        @input="inputFormUser"
        />
        <q-input
        label="No ponsel / Whatsapp"
        filled
        square
        stack-label
        v-model="form.customer_whatsapp"
        @change="checkInputPhone"
        placeholder="08XXXXXXXXXXXX"
        @input="inputFormUser"
        />
        </div>
        <div class="flex justify-between q-px-sm q-pt-md q-pb-xs items-center">
          <div class="text-md">Alamat</div>
          <q-btn v-if="readyAddressBlock" no-caps unelevated rounded color="blue-7" size="12px" padding="4px 12px" label="Edit Alamat" @click="changeNewAddress"></q-btn>
        </div>
        <template v-if="!readyAddressBlock">
        <div class="q-gutter-y-md q-pa-sm">
        <q-select 
        filled
        square
        stack-label
        label="Pilih Provinsi"
        :options="provinceOptions"  
        v-model="province_id" 
        emit-value
        map-options
        @input="provinceSelected"
        />
        <q-select 
          filled
          square
          stack-label
          :disable="!province_id"
          label="Pilih Kabupaten / Kota"
          :options="cityOptions"  
          v-model="city_id" 
          emit-value
          map-options
          @input="citySelected"
        />
        <q-select 
          v-if="config && config.rajaongkir_type == 'pro'"
          filled
          square
          stack-label
          :disable="!city_id"
          label="Pilih Kecamatan / Subdistrict"
          :options="subdistrictOptions"  
          v-model="subdistrict_id" 
          emit-value
          map-options
          @input="subdistrictSelected"
        />
        <q-input v-if="config && config.rajaongkir_type == 'starter' || config && config.rajaongkir_type == 'basic'" filled
        square
        stack-label v-model="subdistrictText" label="Kecamatan" @input="setAddress"/>
        <q-input filled
        square
        stack-label v-model="address1" label="Alamat" @input="setAddress"/>

        <div class="q-pa-md bg-grey-2" v-if="form.address">
          <div class="label-text q-mb-sm">Alamat</div>
          <div v-html="form.address"></div>
        </div>
        </div>
      </template>
      <template v-if="readyAddressBlock">
        <ready-address :user="userAddressData"/>
      </template>
     
    </div>
    <div class="text-md q-px-sm q-pt-md q-pb-xs">Kurir</div>
    <div id="courier" ref="courier" class="q-pa-sm">
      <q-select 
        :disable="!canSelectCourier"
        filled
        square
        stack-label
        label="Pilih Kurir"
        :options="couriers"  
        v-model="formGetCost.courier" 
        emit-value
        map-options
        @input="courierSelected"
        :rules="[val => val && val.length > 0 || 'Wajib dipilih']"
        />
      <q-list v-if="shippingCost.costs.length">
        <q-item tag="label" v-for="item in shippingCost.costs" :key="item.service" v-ripple @click="selectCost(item)">
          <q-item-section avatar>
            <q-icon :name="isSelectedCost && isSelectedCost.service == item.service? 'radio_button_checked' : 'radio_button_unchecked'" :color="isSelectedCost && isSelectedCost.service == item.service? 'green-6' : 'grey-6'"></q-icon>
          </q-item-section>
          <q-item-section>
            <q-item-label>Servis : {{ item.service }}</q-item-label>
            <q-item-label>Deskripsi : {{ item.description }}</q-item-label>
            <q-item-label>Ongkir : {{ moneyIDR(item.cost[0].value)}}</q-item-label>
            <q-item-label>Etd: {{ item.cost[0].etd }} day</q-item-label>
          </q-item-section>
        </q-item>
      </q-list>
      <div ref="courier_skeleton">
      <q-list v-if="loading" >
        <q-item tag="label" v-for="i in 3" :key="i">
          <q-item-section avatar top>
            <div class="q-pa-sm">
            <q-skeleton width="20px" height="20px" class="round"></q-skeleton>
            </div>
          </q-item-section>
          <q-item-section>
            <q-skeleton type="text" width="80px"></q-skeleton>
            <q-skeleton type="text" width="180px"></q-skeleton>
            <q-skeleton type="text" width="110px"></q-skeleton>
            <q-skeleton type="text" width="90px"></q-skeleton>
          </q-item-section>
        </q-item>
      </q-list>
      </div>
    </div>
    <q-dialog v-model="useDataUserPrompt">
        <q-card style="max-width:400px;">
          <q-card-section>
            <div style="font-size:17px;font-weight:500;margin-bottom:6px;">Halo kak,</div>
            <div>Kami menemukan data alamat tersimpan dari order sebelumnya. Apakah akan menggunakan data tersebut?</div>
          </q-card-section>
          <q-card-actions class="justify-end">
            <q-btn size="12px" outline color="primary" label="Tidak" @click="closeModalAddress"></q-btn>
            <q-btn size="12px" unelevated color="primary" label="Ya Gunakan" @click="setDataUser"></q-btn>
          </q-card-actions>
        </q-card>
      </q-dialog>
  </div>
</template>

<script>
import { Api } from 'boot/axios'
import readyAddress from './ReadyAddress.vue'
export default {
  name: 'ShippingAddressBlock',
  props: {
    canEmail: {
      type: Boolean,
      default: false
    },
    isModalAddress: {
      type: Boolean,
      default: false
    }
  },
  components: { readyAddress },
  data() {
    return {
      costNotFound: false,
      readyAddressBlock: false,
      useDataUserPrompt: false,
      isSelectedCost: null,
      form: {
        user_id: '',
        customer_name:'',
        customer_email: '',
        customer_whatsapp: '',
        address: '',
        items: [],
        subtotal: 0,
        total: 0,
        quantity: 0,
        weight: 0,
        shipping_courier_name:'',
        shipping_cost: 0,
        shipping_courier_service:''
      },
      formGetCost: {
        origin: '',
        destination: '',
        weight: '',
        courier: ''
      },
      shippingCost: {
        code:'',
        name: '',
        costs: []
      },
      subdistrictText: '',
      address1: '',
      provinceOptions: [],
      province_id: '',
      cityOptions: [],
      city_id: '',
      subdistrictOptions: [],
      subdistrict_id: '',
      userAddressData: {
        address: '',
        province: '',
        city:'',
        city_id: '',
        subdistrict: '',
        destination: '',
      },
    }
  },
  watch: {
    isModalAddress: function(evt) {
      this.useDataUserPrompt = evt
    }
  },
  computed: {
    user() {
      return this.$store.state.user.user
    },
    couriers() {
      let n = [];
      n.push({label: 'Pilih', value: ''})

      if(this.config) {

        let av = this.config.rajaongkir_couriers.split(',')
        av.forEach(function(el) {
          n.push({label: el.toUpperCase(), value: el})
        })
        if(this.isCanCod) {
          n.push( {label: 'COD ( Dikirim langsung oleh penjual )', value: 'cod'})
        } 
      }
      
      return n
      
    },
    isCanCod() {
      if(this.config && this.config.cod_list && this.config.cod_list.length) {
        let h = this.config.cod_list.filter(el => el.value == this.city_id)
        if(h.length) {
          return true
        } else {
          return false
        }
      } else {
        return false
      }
    },
    config() {
     return this.$store.state.config
   },
   carts() {
     return this.$store.state.cart.carts
   },
   loading() {
     return this.$store.state.loading
   },
   canSelectCourier() {
      if(this.formGetCost.destination && this.formGetCost.weight && this.formGetCost.origin){
      return true
    } else {
      return false
    }
   },
   canGetCost() {
    if(this.formGetCost.destination && this.formGetCost.courier && this.formGetCost.weight && this.formGetCost.origin){
      return true
    } else {
      return false
    }
   }
  },
  mounted() {
    if(!this.provinceOptions.length) {
      this.getProvince()
    }
    this.setDataGetCost()
    if(this.user) {
      this.form.user_id = this.user.id
      this.form.customer_name = this.user.name
      this.form.customer_email = this.user.email
      this.form.customer_whatsapp = this.user.phone ? this.user.phone : ''
    }
  },
  methods: {
    inputFormUser() {
      this.saveDataUser()
      this.collectOrder()
    },
    selectCost(item) {
      this.form.shipping_courier_name = this.shippingCost.name
      this.form.shipping_courier_service = item.service
      this.form.shipping_cost = item.cost[0].value
      this.isSelectedCost = item
      this.collectOrder()
    },
    changeNewAddress() {
      this.formGetCost.courier = ''
      this.formGetCost.destination = ''
      this.city_id = ''
      this.form.address = ''
      this.readyAddressBlock = false
      this.clearSelectedCost()
      this.form.shipping_courier_name =''
      this.form.shipping_cost = 0
      this.form.shipping_courier_service = ''
      this.collectOrder()
    },
    closeModalAddress() {
      this.useDataUserPrompt = false
      this.$emit('closeModal')
    },
    getProvince() {
      this.provinceOptions = []
      this.clearSelectedCost() 
      Api().get('shipping/getProvince').then(response => {
       if(response.status == 200) {
         if(response.data.success) {
           this.setProvinceOption(response.data.results)
         }else {
            this.$q.notify({
              type: 'negative',
              message: response.data.message
            })
          }
        }
      })
    },
    getCity() {
      let self = this
       self.$store.commit('SET_LOADING', true)
       Api().get('shipping/getCity/'+ this.province_id).then(response => {
        if(response.status == 200) {
            if(response.data.success) {
              this.setCityOption(response.data.results)
            }else {
                this.$q.notify({
                  type: 'negative',
                  message: response.data.message
                })
              }
          }
          self.$store.commit('SET_LOADING', false)
          self.setAddress()
        }).catch(() => {
          self.$store.commit('SET_LOADING', false)
          self.setAddress()
        })
    },
    setProvinceOption(res){
      res.forEach(el => {
          let opts = { label: el.province, value: el.province_id }
          this.provinceOptions.push(opts)
        });
    },
    setCityOption(res) {
      res.forEach(el => {
          let opts = { label:el.type + ' ' + el.city_name, value: el.city_id }
          this.cityOptions.push(opts)
      });
    },
    setSubdistrictOption(res){
      res.forEach(el => {
          let opts = { label:el.subdistrict_name, value: el.subdistrict_id }
          this.subdistrictOptions.push(opts)
      });
    },
    getSubdistict() {
      let self = this
       self.$store.commit('SET_LOADING', true)
       Api().get('shipping/getSubdistict/'+ this.city_id).then(response => {
        if(response.status == 200) {
          if(response.data.success) {
           this.setSubdistrictOption(response.data.results)
         }else {
            this.$q.notify({
              type: 'negative',
              message: response.data.message
            })
          }
          }
          self.$store.commit('SET_LOADING', false)
          self.setAddress()
        }).catch(() => {
          self.$store.commit('SET_LOADING', false)
          self.setAddress()
        })
    },

    setDataUser() {
      
      let data = JSON.parse(localStorage.getItem('user_data'))

      this.form.address = data.address

      this.city_id = data.city_id

      this.userAddressData.province = data.province
      this.userAddressData.city = data.city
      this.userAddressData.subdistrict = data.subdistrict
      this.userAddressData.address = data.address
      this.userAddressData.destination = data.destination

      this.formGetCost.destination = data.destination

      this.province_id = data.province_id
      this.city_id = data.city_id

      this.useDataUserPrompt = false
      this.readyAddressBlock = true

    },

    provinceSelected() {
      this.cityOptions = []
      this.city_id = ''
      this.clearSelectedCost() 
      if(this.province_id) { 
        this.getCity();
      }
    },
    citySelected() {
     if(this.city_id) {
       this.formGetCost.destination = this.city_id
       this.userAddressData.destination = this.city_id
       this.userAddressData.city_id = this.city_id
     }
     if(this.config && this.config.rajaongkir_type == 'pro') {
       this.getSubdistict()
     } else {
       this.getCost()
     }
     this.setAddress()
    },
    subdistrictSelected() {
      if(this.subdistrict_id) {
       this.formGetCost.destination = this.subdistrict_id
       this.userAddressData.destination = this.subdistrict_id
     }
     this.getCost()
     this.setAddress()
    },
    courierSelected(evt) {
      if(!evt) {
        this.form.shipping_courier_name = '',
        this.form.shipping_cost =  0,
        this.form.shipping_courier_service = ''
        this.clearSelectedCost()
        this.collectOrder()
      }
     
      if(evt == 'cod') {
        this.form.shipping_courier_name = 'COD'
        this.form.shipping_courier_service = 'COD'
        this.form.shipping_cost = 0
        this.clearSelectedCost()
        this.collectOrder()
      } else {
        this.form.shipping_courier_name = this.formGetCost.courier
        this.getCost()
      }
    },
    clearSelectedCost() {
      this.shippingCost.code = ''
      this.shippingCost.name = ''
      this.shippingCost.costs = []
    },
    getCost() {
      let self = this
      this.costNotFound = false
      this.clearSelectedCost() 
      if(this.canGetCost) {
        this.scrollToBottom()
        self.$store.commit('SET_LOADING', true)
        Api().post('shipping/getCost', this.formGetCost).then(response => {
          if(response.status == 200) {
            let data = response.data.results[0];
              this.shippingCost.code = data.code
              this.shippingCost.name = data.name
              this.shippingCost.costs = data.costs

            if(!data.costs.length) {
              this.costNotFound = true
            }
             self.$store.commit('SET_LOADING', false)
             this.collectOrder()
          }
        }).catch(() => {
          self.$store.commit('SET_LOADING', false)
          this.costNotFound = true
          this.collectOrder()

        })
      }
    },
    collectOrder() {
      this.form.items = this.carts
      this.form.subtotal = this.sumSubtotal()
      this.form.total = this.sumGrandTotal()
      this.form.quantity = this.sumQty()
      this.form.weight = this.sumWeight()

      this.$emit('place', this.form)

    },
    setDataGetCost() {
      this.formGetCost.weight = this.sumWeight();
      if(this.config && this.config.can_shipping){
        this.formGetCost.origin = this.config.warehouse_id
      } 
      this.$emit('place', this.form)
    },
    money(number) {
     return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR'}).format(number)
    },
    checkInputPhone() {
      this.form.customer_whatsapp = this.form.customer_whatsapp.replace(/\D/g,'')
      if(!this.checkPhoneNumber(this.form.customer_whatsapp)) {
        this.$q.dialog({
          message: 'Silahkan masukkan nomor whatsapp yang benar.'
        })
        this.form.customer_whatsapp = ''
      }
    },
    sumGrandTotal() {
      if(this.form.shipping_cost) {
        return this.sumSubtotal() + this.form.shipping_cost
      } else {
         return this.sumSubtotal()
      }
    },
    sumQty() {
      if(this.carts.length > 1) {
        let q = [];
        this.carts.forEach(el => {
          q.push(el.quantity)
        });
        return q.reduce((a,b) => a + b)
      }
      return this.carts[0].quantity
    },
    sumSubtotal() {
      if(this.carts.length > 1) {
        let j = [];
        this.carts.forEach(el => {
          j.push(el.quantity*el.price)
        });
        return j.reduce((a,b) => a + b)
      }
      return this.carts[0].quantity * this.carts[0].price
    },
    sumWeight() {
      if(this.carts.length > 1) {
        let w = [];
        this.carts.forEach(el => {
          w.push(el.weight*el.quantity)
        });
        return w.reduce((a,b) => a + b)
      }
      return this.carts[0].quantity * this.carts[0].weight
    },
    sumTotal () {
      return this.sumSubtotal()
    },
    checkPhoneNumber(formatted) {

      if(formatted.length > 9 ) {
        
        if(formatted.startsWith('08') || formatted.startsWith('628')) {
          return true
        } 
      }
    },
    setAddress() {
      let province = '', city = '', subdistrict = '';
      if(this.province_id) {
        province = this.provinceOptions.find(el => el.value == this.province_id).label
      }
      if(this.city_id) {
        city = this.cityOptions.find(el => el.value == this.city_id).label
      }
      if(this.subdistrict_id) {
        subdistrict = this.subdistrictOptions.find(el => el.value == this.subdistrict_id).label
      }
      if(this.subdistrictText) {
        subdistrict = this.subdistrictText
      }
      if(province && city && subdistrict && this.address1) {
        let addr = `${this.address1} <br>${subdistrict}, ${city}<br>${province}`
        this.form.address = addr
        this.userAddressData.address = addr
        this.userAddressData.province = province
        this.userAddressData.city = city
        this.userAddressData.subdistrict = subdistrict
        this.userAddressData.customer_name = this.form.customer_name
        this.userAddressData.customer_whatsapp = this.form.customer_whatsapp
        this.userAddressData.customer_email = this.form.customer_email

      } else {
        this.form.address = ''
      }
      this.saveDataUser()

      this.collectOrder()
    },
    saveDataUser() { 
      if(this.userAddressData.address
      && this.userAddressData.province
      && this.userAddressData.city
      && this.userAddressData.city_id) {
        localStorage.setItem('user_data', JSON.stringify(this.userAddressData))
      }
    },
    scrollToBottom () {
      setTimeout(() => {
        var elem = this.$refs.courier_skeleton;
        elem.scrollIntoView({behavior: 'smooth'})
      }, 300)
    }
  }
}
</script>

<style>

</style>