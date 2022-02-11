<template>
  <q-page class="q-pa-sm">
    <q-header class="text-primary bg-white" reveal :reveal-offset="10">
        <q-toolbar>
          <q-btn @click="handleBackButton"
            flat round dense
            icon="arrow_back" />
          <q-toolbar-title class="text-weight-bold brand">Checkout</q-toolbar-title>
        </q-toolbar>
    </q-header>
    <div id="checkout" v-if="carts && carts.length" ref="top" class="q-pb-xl">
      <shipping-address 
      @place="placeOrder"
      :isModalAddress="isAvailableOldAddress"
      @closeModal="isAvailableOldAddress = false"
      />
    </div>
    <q-inner-loading :showing="loading">
        <q-spinner-facebook size="50px" color="primary"/>
    </q-inner-loading>
    <q-footer class="bg-white q-pa-md">
      <q-btn :disabled="!isOk" @click="directChekout" no-caps unelevated label="Proses Pesanan" color="primary" class="full-width"></q-btn>
    </q-footer>
  </q-page>
</template>

<script>
import ShippingAddress from './ShippingAddress.vue'
export default {
	components: { ShippingAddress },
  name: 'DirectWithShipping',
  data () {
    return {
      isAvailableOldAddress: false,
      isOk: false,
      formLoading: false,
      form: {
        reference: '',
        customer_name:'',
        customer_email: '',
        customer_whatsapp: '',
        address: '',
        items: [],
        subtotal: 0,
        total: 0,
        quantity: 0,
        weight: 0,
        shipping_courier_name:'COD',
        shipping_cost: '',
        shipping_courier_service: ''
      },
    }
  },
  computed: {
    carts() {
      return this.$store.state.cart.carts
    },
    shop() {
      return this.$store.state.shop
    },
    config() {
      return this.$store.state.config
    },
    loading() {
      return this.$store.state.loading
    },
    session_id() {
      return this.$store.state.session_id
    }
  },
  mounted() {
    if(!this.carts.length) {
      this.$router.back()
    }
    this.checkDataUser()
  },
  methods: {
    placeOrder(data) {
      this.form = data
      this.checkStepOk()
    },
    checkDataUser() {
      if(localStorage.getItem('user_data')) {
        this.isAvailableOldAddress = true
      }
    },
    handleBackButton() {
      this.$router.push({ name: 'Cart'})
    },
    toTop() {
      setTimeout(() => {
        var elem = this.$refs.top;
        elem.scrollIntoView({behavior: 'smooth'})
      }, 500)
    },
    checkStepOk() {
      if(this.form.customer_name 
          && this.form.customer_whatsapp 
          && this.form.shipping_courier_name
          && this.form.address) {
            this.isOk = true
          } else {
            this.isOk = false
          }
    },
    directChekout() {

      this.checkStepOk()

      if(!this.isOk) return

      let whatsappUrl = 'https://api.whatsapp.com'
      
      let whatsapp = this.formatPhoneNumber(this.shop.phone)

      let str = `Halo kak, saya mau order:\n\n`
    
      let items = this.carts
      let numb = 1;
      items.forEach(el => {
        str +=  `*${numb}. ${el.name}*\nJumlah: ${el.quantity}\nHarga (@): ${this.moneyIDR(el.price)}\nHarga Total: ${this.moneyIDR(el.quantity*el.price)}\n\n`
        numb ++
      })

      str += `Subtotal: *${this.moneyIDR(this.subtotal())}*\nOngkir: *${this.moneyIDR(this.form.shipping_cost)}*\nTotal: *${this.moneyIDR(this.total())}*\n-----------------------------------\n\n*Nama:*\n ${this.form.customer_name} (${this.form.customer_whatsapp})\n\n*Alamat:*\n${this.formatAddressCod(this.form.address)}\n\nKurir: ${this.form.shipping_courier_name}\nServis: ${this.form.shipping_courier_service}\n`

      let link = whatsappUrl+'/send?phone=' + whatsapp + '&text=' + encodeURI(str);
      setTimeout(() => {
        this.$router.push({ name: 'Cart'})
      }, 1000)
      setTimeout(() => {
        this.$store.dispatch('cart/clearCart', this.session_id)
      }, 5000)
      window.open(link, '_blank');

    },
    formatPhoneNumber(number) {
      let formatted = number.replace(/\D/g,'')

      if(formatted.startsWith('0')) {
        formatted = '62' + formatted.substr(1)
      }

      return formatted;
    },
    subtotal() {
      if(this.carts.length > 1) {
        let j = [];
        this.carts.forEach(element => {
          j.push(element.quantity*element.price)
        });
        return j.reduce((a,b) => a + b)
      }
      return this.carts[0].quantity * this.carts[0].price
    },
    total () {
      return this.subtotal()+this.form.shipping_cost
    },
    money(number) {
     return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR'}).format(number)
    },
    formatAddressCod(addr) {
      let arr = addr.split('<br>')
      return arr.join('\n')
    },
  }
}
</script>
