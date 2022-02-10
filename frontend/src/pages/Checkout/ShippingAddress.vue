<template>
  <div>
    <div id="shipping-address">
      <div class="text-md q-px-sm q-pb-xs">Penerima</div>
      <div class="q-gutter-y-md q-pa-sm">
        <q-input
        filled
        square
        stack-label
        label="Nama Penerima"
        v-model="form.customer_name"
        @input="inputFormUser"
        debounce="500"
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
        debounce="500"
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
        debounce="500"
        />
        </div>
        <div class="q-px-sm q-pt-md q-pb-xs">
            <div class="text-md q-pb-md">Pengiriman</div>
            <template v-if="!readyAddressBlock">
              <div class="q-gutter-y-md q-mb-md">
                <q-list v-if="subdistrictSelected">
                  <q-item class="bg-grey-2">
                    <q-item-section>{{ subdistrictSelected.subdistrict_name }} - {{ subdistrictSelected.type }} {{ subdistrictSelected.city }}, {{ subdistrictSelected.province }}</q-item-section>
                    <q-item-section side>
                      <q-btn flat no-caps color="red" @click="changeAddress">
                  <q-icon name="close" size="19px"></q-icon>
                  <span>Ganti</span>
                  </q-btn>
                    </q-item-section>
                  </q-item>
                </q-list>
                <div v-else>
                  
                  <q-input filled placeholder="Ketik kecamatan tujuan" ref="search" v-model="searchSubdistrictKey" debounce="500" @input="findSubdistrict" :loading="isSearching"></q-input>
                  <transition
                      appear
                      enter-active-class="animated fadeIn"
                      leave-active-class="animated fadeOut"
                    >
                    <div class="relative bg-grey-1" v-show="isSearching || searchReady">
                      <q-list style="min-height:70px;max-height:300px;overflow-y:auto;" v-if="searchAvailable">
                        <q-item v-for="item in subdistrictOptionsData" :key="item.id" clickable @click="selectSubdistrict(item)">
                          <q-item-section>
                            <q-item-label>{{ item.subdistrict_name }} - {{ item.type }} {{ item.city }}</q-item-label>
                          </q-item-section>
                        </q-item>
                      </q-list>
                      <div v-else class="text-red-7 q-pa-md">kecamatan {{ searchSubdistrictKey }} tidak ditemukan</div>
                      <q-inner-loading :showing="isSearching"></q-inner-loading>
                    </div>
                  </transition>
                </div>
                <q-input v-if="subdistrictSelected" filled square stack-label v-model="addressTemp" label="Alamat jalan / dusun / desa / Rt / Rw" @input="setAddress" debounce="200"/>

              </div>
            </template>
            <transition
              appear
              enter-active-class="animated fadeIn"
              leave-active-class="animated fadeOut"
            >
            <div v-if="form.address">
              <div class="flex justify-between items-center q-pb-sm"> 
                <div class="text-grey-7">Detil Alamat Pengiriman</div>
                <q-btn flat v-if="readyAddressBlock" no-caps unelevated color="blue-7" padding="2px 12px" label="Edit Alamat" @click="changeNewAddress"></q-btn>
              </div>
              <div class="q-pa-md bg-grey-2" v-html="form.address"></div>
            </div>
            </transition>
        </div>
     
    </div>
    <div class="text-md q-px-sm q-pt-md q-pb-xs">Kurir</div>
    <div id="cod" v-if="codItem" class="q-mb-md">
      <div class="q-px-sm q-pb-xs text-grey-7">Pengiriman via COD tersedia</div>
      <q-list class="q-pa-sm">
        <q-item @click="selectCostCod(codItem)" clickable>
          <q-item-section avatar>
            <q-icon :name="isSelectedCostCod ? 'radio_button_checked' : 'radio_button_unchecked'" :color="isSelectedCostCod ? 'green-6' : 'grey-6'"></q-icon>
          </q-item-section>
          <q-item-section>
            <q-item-label>{{ codItem.subdistrict_name }} - {{ codItem.type }} {{ codItem.city }} {{ parseInt(codItem.price) > 0 }}</q-item-label>
            <q-item-label>Ongkir : {{ (codItem.price && parseInt(codItem.price) > 0) ? moneyIDR(parseInt(codItem.price)) : 'Gratis'}} </q-item-label>
          </q-item-section>
        </q-item>
      </q-list>
    </div>
    <div class="q-px-sm q-pb-xs text-grey-7">Pengiriman via Ekspedisi</div>
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
      <q-list v-if="shippingCost.ready">
        <template v-if="shippingCost.costs.length">
        <q-item v-for="item in shippingCost.costs" :key="item.service" v-ripple @click="selectCost(item)" clickable>
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
        </template>
        <q-item v-else>
          <q-item-section>
            <q-item-label class="text-red-5 q-pa-lg">Ongkos kirim tidak ditemukan, silahkan ganti dengan kurir yang lain</q-item-label>
          </q-item-section>
        </q-item>
      </q-list>
      <div ref="courier_skeleton">
      <q-list v-if="loading" >
        <q-item v-for="i in 3" :key="i">
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
  data() {
    return {
      costNotFound: false,
      readyAddressBlock: false,
      useDataUserPrompt: false,
      isSelectedCost: null,
      isSelectedCostCod: null,
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
        costs: [],
        ready: false
      },
      addressTemp: '',
      userAddressData: {
        address: '',
        destination: '',
      },
      searchSubdistrictKey: '',
      isSearching: false,
      searchAvailable: true,
      searchReady:false,
      subdistrictOptionsData: [],
      subdistrictSelected: null,
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
      }
      
      return n
      
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
    },
    codItem() {
      if(this.subdistrictSelected) {
        if(this.config && this.config.cod_list && this.config.cod_list.length) {
          let h = this.config.cod_list.find(el => el.subdistrict_id == this.subdistrictSelected.subdistrict_id)
          if(h != undefined) {
            return h
          } else {
            return null
          }
        } else {
          return null
        }

      } else {
        return null
      }
    },
  },
  mounted() {
    this.setDataGetCost()
    if(this.user) {
      this.form.user_id = this.user.id
      this.form.customer_name = this.user.name
      this.form.customer_email = this.user.email
      this.form.customer_whatsapp = this.user.phone ? this.user.phone : ''
    }
    this.collectOrder()
  },
  methods: {
    inputFormUser() {
      this.saveDataUser()
      this.emitForm()
    },
    selectCostCod(item) {
      this.isSelectedCost = null
      this.form.shipping_courier_name = 'COD'
      this.form.shipping_courier_service = 'COD'
      this.form.shipping_cost = item.price? parseInt(item.price) : 0
      this.isSelectedCostCod = item
      this.collectOrder()
    },
    selectCost(item) {
      this.isSelectedCostCod = null
      this.form.shipping_courier_name = this.shippingCost.name
      this.form.shipping_courier_service = item.service
      this.form.shipping_cost = item.cost[0].value
      this.isSelectedCost = item
      this.collectOrder()
    },
    changeNewAddress() {
      this.formGetCost.courier = ''
      this.formGetCost.destination = ''
      this.form.address = ''
      this.readyAddressBlock = false
      this.clearSelectedCost()
      this.emitForm()
      setTimeout(() => {
        this.$refs.search.focus()
      },300)
    },
    closeModalAddress() {
      this.useDataUserPrompt = false
      this.$emit('closeModal')
    },
    changeAddress() {
      this.form.address = '';
      this.searchSubdistrictKey = '';
      this.subdistrictSelected = null
      this.subdistrictOptionsData = []
      this.clearSelectedCost()
      this.emitForm()
      setTimeout(() => {
        this.$refs.search.focus()
      },300)

    },
    selectSubdistrict(item) {

      console.log(item);
  
      this.subdistrictSelected = item
      this.searchSubdistrictKey = ''

      this.formGetCost.destination = item.city_id
      this.userAddressData.destination = item.city_id

      if(this.config.rajaongkir_type == 'pro') {
        this.formGetCost.destination = item.subdistrict_id
        this.userAddressData.destination = item.subdistrict_id
      }

      // this.getCost()
      // this.setAddress()
    },
    findSubdistrict() {
      this.subdistrictOptionsData = []
      this.searchAvailable = true
      this.searchReady = false
      if(this.searchSubdistrictKey.length < 3) return
      this.isSearching = true
      Api().get('shipping/findSubdistrict/'+ this.searchSubdistrictKey)
      .then(response => {
        if(response.status == 200) {
          if(response.data.success) {

            this.subdistrictOptionsData = response.data.results
            this.searchAvailable = response.data.results.length ? true : false

          } else {
            this.$q.notify({
              type: 'negative',
              message: response.data.message
            })
          }
        }
      })
      .finally(() => {
         this.isSearching = false
         this.searchReady = true
      })
    },

    setDataUser() {
      
      let data = JSON.parse(localStorage.getItem('user_data'))

      this.form.address = data.address
      this.userAddressData.address = data.address
      this.userAddressData.destination = data.destination
      this.formGetCost.destination = data.destination

      this.useDataUserPrompt = false
      this.readyAddressBlock = true

    },

    courierSelected(evt) {
      if(!evt) {
        
        this.clearSelectedCost()
        this.emitForm()
      }
     
      if(evt == 'cod') {
        this.form.shipping_courier_name = 'COD'
        this.form.shipping_courier_service = 'COD'
        this.form.shipping_cost = 0
        this.clearSelectedCost()
        this.emitForm()
      } else {
        this.form.shipping_courier_name = this.formGetCost.courier
        this.getCost()
      }
    },
    clearSelectedCost() {
      this.shippingCost.code = ''
      this.shippingCost.name = ''
      this.shippingCost.costs = []
      this.form.shipping_courier_name = ''
      this.form.shipping_cost =  0
      this.form.shipping_courier_service = ''
      this.isSelectedCost = null
      this.isSelectedCostCod = null
    },
    getCost() {
      this.shippingCost.ready = false
      this.costNotFound = false
      this.clearSelectedCost() 
      this.emitForm()
      if(this.canGetCost) {
        this.scrollToBottom()
        this.$store.commit('SET_LOADING', true)
        Api().post('shipping/getCost', this.formGetCost).then(response => {
          if(response.status == 200) {
            let data = response.data.results[0];
              this.shippingCost.code = data.code
              this.shippingCost.name = data.name
              this.shippingCost.costs = data.costs

            if(!data.costs.length) {
              this.costNotFound = true
            }
             this.$store.commit('SET_LOADING', false)
             this.emitForm()
          }
        }).catch(() => {
          this.$store.commit('SET_LOADING', false)
          this.costNotFound = true
          this.emitForm()

        })
        .finally(() => {
           this.shippingCost.ready = true
        })
      }
    },
    emitForm() {
      this.$emit('place', this.form)
    },
    collectOrder() {
      this.form.items = this.carts
      this.form.subtotal = this.sumSubtotal()
      this.form.total = this.sumGrandTotal()
      this.form.quantity = this.sumQty()
      this.form.weight = this.sumWeight()
      this.emitForm()

    },
    setDataGetCost() {
      this.formGetCost.weight = this.sumWeight();
      if(this.config && this.config.can_shipping){
        this.formGetCost.origin = this.config.warehouse_id
      } 
      this.emitForm()
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
        return this.sumSubtotal() + parseInt(this.form.shipping_cost)
      } else {
         return this.sumSubtotal()
      }
    },
    sumQty() {
      if(this.carts.length > 1) {
        let q = [];
        this.carts.forEach(el => {
          q.push(parseInt(el.quantity))
        });
        return q.reduce((a,b) => a + b)
      }
      return parseInt(this.carts[0].quantity)
    },
    sumSubtotal() {
      if(this.carts.length > 1) {
        let j = [];
        this.carts.forEach(el => {
          j.push(parseInt(el.quantity)*parseInt(el.price))
        });
        return j.reduce((a,b) => a + b)
      }
      return this.carts[0].quantity * parseInt(this.carts[0].price)
    },
    sumWeight() {
      if(this.carts.length > 1) {
        let w = [];
        this.carts.forEach(el => {
          w.push(parseInt(el.weight)*parseInt(el.quantity))
        });
        return w.reduce((a,b) => a + b)
      }
      return parseInt(this.carts[0].quantity) * parseInt(this.carts[0].weight)
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
      if(this.addressTemp && this.subdistrictSelected) {
        let addr =  `${this.addressTemp} <br> ${this.subdistrictSelected.subdistrict_name} - ${this.subdistrictSelected.type} ${this.subdistrictSelected.city} <br> ${this.subdistrictSelected.province}`
        this.form.address = addr
        this.userAddressData.address = addr

        this.saveDataUser()

        this.emitForm()
      }

    },
    saveDataUser() { 
      if(this.userAddressData.address
      && this.userAddressData.warehouse_id) {
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