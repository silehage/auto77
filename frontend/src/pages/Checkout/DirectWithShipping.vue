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
    <div id="checkout" v-if="carts && carts.items.length" ref="top" class="q-pb-xl">
      <shipping-address  
        :isModalAddress="isAvailableOldAddress"
        @closeModal="isAvailableOldAddress = false"
        @checkStep="checkStepOk"
      />
    </div>
    <q-inner-loading :showing="loading">
        <q-spinner-facebook size="50px" color="primary"/>
    </q-inner-loading>
    <q-footer class="bg-white q-pa-md">
      <q-btn :disabled="!isOk" @click="checkout" no-caps unelevated label="Proses Pesanan" color="primary" class="full-width"></q-btn>
    </q-footer>
  </q-page>
</template>

<script>
import ShippingAddress from './ShippingAddress.vue'
export default {
	components: { ShippingAddress },
  name: 'CheckoutDirectWithShipping',
  data () {
    return {
      isAvailableOldAddress: false,
      isOk: false,
      formLoading: false,
    }
  },
  computed: {
    carts() {
      return this.$store.getters['cart/getCarts']
    },
    formOrder() {
      return this.$store.getters['order/getFormOrder']
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
    },
    coupon_discount() {
      return this.$store.state.coupon.coupon_discount
    }
  },
  mounted() {
    if(!this.carts.items.length) {
      this.$router.back()
    }
    this.checkDataUser()
    this.collectOrder()
  },
  methods: {
    collectOrder() {
      this.$store.commit('order/COLLECT_ORDER', this.carts)
    },
    checkDataUser() {
      if(localStorage.getItem('user_data')) {
        this.isAvailableOldAddress = true
      }
    },
    handleBackButton() {
      this.$router.push({ name: 'Cart'})
    },
    
    checkStepOk() {
      if(this.formOrder.customer_name 
          && this.formOrder.customer_phone
          && this.formOrder.shipping_courier_name
          && this.formOrder.address) {
            this.isOk = true
          } else {
            this.isOk = false
          }
    },
    
    formatPhoneNumber(number) {
      let formatted = number.replace(/\D/g,'')

      if(formatted.startsWith('0')) {
        formatted = '62' + formatted.substr(1)
      }

      return formatted;
    },
    formatAddressCod(addr) {
      let arr = addr.split('<br>')
      return arr.join('\n')
    },
    checkout() {

      this.checkStepOk()

      if(!this.isOk) return

      let whatsappUrl = 'https://api.whatsapp.com'
      
      let whatsapp = this.formatPhoneNumber(this.shop.phone)

      let str = `Halo kak, saya mau order:\n\n`
    
      let items = this.formOrder.items
      let numb = 1;
      items.forEach(el => {
        str +=  `*${numb}. ${el.name}*\n`
        if(el.note){
          str += `[${el.note}]\n`
        }
        str+= `Jumlah: ${el.quantity}\nHarga (@): ${this.moneyIDR(el.price)}\nHarga Total: ${this.moneyIDR(el.quantity*el.price)}\n\n`
        numb ++
      })

      str += `Subtotal: *${this.moneyIDR(this.formOrder.subtotal)}*\nOngkir: *${this.moneyIDR(this.formOrder.shipping_cost)}*\nDiskon: *${this.moneyIDR(this.formOrder.coupon_discount)}*\nTotal: *${this.moneyIDR(this.formOrder.total)}*\n-----------------------------------\n\n*Nama:*\n ${this.formOrder.customer_name} (${this.formOrder.customer_phone})\n\n*Alamat:*\n${this.formatAddressCod(this.formOrder.address)}\n\nKurir: ${this.formOrder.shipping_courier_name}\nServis: ${this.formOrder.shipping_courier_service}\n`

      let link = whatsappUrl+'/send?phone=' + whatsapp + '&text=' + encodeURI(str);
      setTimeout(() => {
        this.$router.push({ name: 'Cart'})
      }, 1000)
      setTimeout(() => {
        this.$store.dispatch('cart/clearCart', this.session_id)
      }, 5000)
      window.open(link, '_blank');

    },
    toTop() {
      setTimeout(() => {
        var elem = this.$refs.top;
        elem.scrollIntoView({behavior: 'smooth'})
      }, 500)
    },
  }
}
</script>
