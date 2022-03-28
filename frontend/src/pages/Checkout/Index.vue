<template>
  <q-page class="">
    <q-header class="text-primary bg-white" reveal :reveal-offset="10">
        <q-toolbar>
          <q-btn @click="handleBackButton"
            flat round dense
            icon="arrow_back" />
          <q-toolbar-title class="text-weight-bold brand">{{ title }}</q-toolbar-title>
        </q-toolbar>
    </q-header>
    <div id="checkout" v-if="carts && carts.items.length" ref="top" class="q-pb-xl">
      <q-stepper
        v-model="step"
        keep-alive
        flat
        color="primary"
        alternative-labels
        animated
        >
          <q-step
          :name="1"
          title="Pengiriman"
          :done="step > 1"
          icon="local_shipping"
        >
          <shipping-address 
            canEmail 
            @place="placeAddress" 
            :isModalAddress="isAvailableOldAddress"
            @closeModal="isAvailableOldAddress = false"
            :formData="form"
          />

        </q-step>

          <q-step
          :name="2"
          title="Pembayaran"
          :done="step > 2"
          icon="payments"
          v-if="form.shipping_courier_name != 'COD'"
          >
         
          <payment-list 
            ref="paymentList" 
            :isCod="isCod" 
            :paymentSelected="paymentSelected" 
            :payments="paymentChanels" 
            @onSelect="onSelectPayment"
            :order="form"
          />
          </q-step>

          <q-step
          :name="3"
          title="Review"
          :done="step > 3"
          icon="playlist_add_check"
          >
         <review-order 
         :form="form" 
         :carts="carts" 
         :paymentSelected="paymentSelected" 
         :noPayment="isCantPaymentStep"/>
          </q-step>

      </q-stepper>
    </div>
    <q-inner-loading :showing="loading">
        <q-spinner-facebook size="50px" color="primary"/>
    </q-inner-loading>
    <div class="bg-white q-pa-md q-gutter-y-sm column" :class="{'sticky-bottom': $q.platform.is.desktop }">
      <q-btn :disabled="!isOk" v-if="step != 3 && form.shipping_courier_name != 'COD'" @click="next" no-caps unelevated label="Langkah Selanjutnya" color="primary"></q-btn>
      <q-btn :disabled="!isOk" v-if="form.shipping_courier_name == 'COD' && step != 3" @click="step = 3" no-caps unelevated label="Langkah Selanjutnya" color="primary"></q-btn>
      <q-btn :disabled="loading" v-if="step == 3" @click="submitOrder" no-caps unelevated label="Proses Pesanan" color="primary"></q-btn>
      <!-- <q-btn :disabled="step == 1" @click="prev" no-caps outline label="Kembali" color="primary"></q-btn> -->
    </div>
  </q-page>
</template>

<script>
import { Api } from 'boot/axios'
import { mapActions } from 'vuex'
import ShippingAddress from './ShippingAddress.vue'
import PaymentList from './PaymentList.vue'
import ReviewOrder from './ReviewOrder.vue'
export default {
	components: { ShippingAddress, PaymentList, ReviewOrder },
  name: 'CheckoutPage',
  data () {
    return {
      isAvailableOldAddress: false,
      isOk: false,
      step1Ok: false,
      step2Ok: false,
      formLoading: false,
      paymentChanels: {
        localbanks: [],
        paymentGateway: []
      },
      invoiceTemp: null,
      step: 1,
      paymentSelected: null,
      form: {
        reference: '',
        customer_name:'',
        customer_email: '',
        customer_whatsapp: '',
        payment_method: '',
        payment_name: '',
        payment_type: '',
        payment_code: '',
        payment_fee: 0,
        address: '',
        items: [],
        subtotal: 0,
        total: 0,
        quantity: 0,
        weight: 0,
        shipping_courier_name:'',
        shipping_cost: 0,
        shipping_courier_service: '',
        coupon_discount: 0
      },
    }
  },
  watch: {
    step: function() {
      this.checkStepOk()
      this.toTop()
    }
  },
  computed: {
    isOVO() {
      return this.form.payment_method == 'OVO'
    },
    isCod() {
      return this.form.shipping_courier_name == 'COD'
    },
    carts() {
        return this.$store.getters['cart/getCarts']
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
    isCodPayment() {
      return this.form.payment_method == 'COD'
    },
    isCantPaymentStep() {
      return this.form.shipping_courier_name == 'COD' || !this.config.is_payment_gateway? true : false
    },
    title() {
      if(this.step == 1) return 'Pengiriman'
      if(this.step == 2) return 'Pembayaran'
      if(this.step == 3) return 'Review Order'
      return 'Checkout'
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
      this.$router.push({ name: 'Cart'})
    }
    if(this.config && this.config.is_payment_gateway && ! this.paymentChanels.paymentGateway.length) {
      this.getPaymentChanel()
    }
    if(! this.paymentChanels.localbanks.length) {
      this.getLocalBanks()
    }
    this.checkDataUser()
    this.collectOrder()
  },
  methods: {
    ...mapActions('order', ['storeOrder']),
    placeAddress(data) {

      this.paymentSelected = null
      if(data.shipping_courier_name == 'COD') {
        this.form.payment_type = 'COD'
        this.form.payment_method = 'COD'
        this.form.payment_name = 'COD'
      } else {
        this.form.payment_type = ''
        this.form.payment_method = ''
        this.form.payment_name = ''
      }
      this.form.user_id = data.user_id 
      this.form.customer_email = data.customer_email 
      this.form.customer_name = data.customer_name 
      this.form.customer_whatsapp = data.customer_whatsapp 
      this.form.shipping_cost = data.shipping_cost 
      this.form.shipping_courier_name = data.shipping_courier_name 
      this.form.shipping_courier_service = data.shipping_courier_service 
      this.form.address = data.address 

      this.checkDiscount()
      this.collectOrder()
      this.checkStepOk()

    },
    collectOrder() {
      this.form.items = this.carts.items
      this.form.subtotal = this.carts.subtotal
      this.form.total = this.sumTotal()
      this.form.quantity = this.carts.qty
      this.form.weight = this.carts.weight
      if(this.coupon_discount) {
        this.form.coupon_discount = this.getDiscountAmount()
      }
    },
    checkDataUser() {
      if(localStorage.getItem('user_data')) {
        this.isAvailableOldAddress = true
      }
    },
    handleBackButton() {
      if(this.step > 1) {
        if(this.isCod) {
          this.step = 1
          } else {
          this.step -= 1
        }
      } else {
        this.$router.push({ name: 'Cart'})
      }
    },
    toTop() {
      setTimeout(() => {
        var elem = this.$refs.top;
        elem.scrollIntoView({behavior: 'smooth'})
      }, 500)
    },
    checkStepOk() {
      if(this.step == 1) { 
        if(this.form.customer_name 
            && this.form.customer_email 
            && this.form.customer_whatsapp 
            && this.form.shipping_courier_name
            && this.form.address) {
               if(this.isCod) {
                 this.isOk = true
              } else {
                this.form.shipping_cost ?  this.isOk = true :  this.isOk = false
              }
            } else {
              this.isOk = false
            }
      } else if(this.step == 2) {
        if(this.form.payment_name 
            && this.form.address 
            && this.form.payment_type) {
              this.isOk = true
          } else {
              this.isOk = false
          }
        } else {
              this.isOk = true
        }
      
    },
    onSelectPayment(item) {
      this.paymentSelected = item

      if(item.payment_fee) {
        this.form.payment_fee = item.payment_fee
      } else {
        this.form.payment_fee = 0
      }

      if(item.payment_type == 'DIRECT') {
        this.form.payment_method = 'BANK_TRANSFER'
        this.form.payment_name = item.bank_name + ' - ' + item.bank_office + ' ( a.n ' + item.account_name + ' )'
        this.form.payment_code = item.account_number
        this.form.payment_type = item.payment_type
      }
      if(item.payment_type == 'GATEWAY') {
        this.form.payment_method = item.code
        this.form.payment_name = item.name
        this.form.payment_code = ''
        this.form.payment_type = item.payment_type
      }
      if(item.payment_type == 'COD') {
        this.form.payment_method = item.payment_method
        this.form.payment_name = item.payment_name
        this.form.payment_code = ''
        this.form.payment_type = 'COD'
      }

      this.checkDiscount()
      this.collectOrder()
      this.checkStepOk()
    },
    checkDiscount() {
      if(this.coupon_discount) {
        this.form.coupon_discount = this.getDiscountAmount()
      }
    },
    
    submitOrder() {
      this.$store.commit('SET_LOADING', true)
      this.storeOrder(this.form)
      .then(response => {
          this.$store.commit('SET_LOADING', false)

          if(response.status == 200) {

            this.$store.commit('order/SET_INVOICE', response.data.results)
            this.$store.commit('coupon/SET_COUPON_DISCOUNT', null)
            
            setTimeout(() => {
              this.$store.dispatch('cart/clearCart', this.session_id)
            }, 8000)

            if(this.isCod) {
  
              this.directChekout(response.data.results)
              
            }
            
            this.$router.push({ name: 'UserInvoice', params: { order_ref: response.data.results.order_ref }})

            this.sendMessageNotification(response.data.results.order_ref)
          }
        })
        .catch(() => {
          this.ready = false
          this.$store.commit('SET_LOADING', false)
        })
    },
    sendMessageNotification(order_ref) {
      setTimeout(() => {
        Api().post('sendNotify', {url: this.getRoutePath(order_ref), order_ref: order_ref})
      },12000)
    },
    formatAddressCod(addr) {
      let arr = addr.split('<br>')
      return arr.join('\n')
    },
    directChekout(data) {

      let whatsappUrl = 'https://api.whatsapp.com'
      
      let whatsapp = this.formatPhoneNumber(this.shop.phone)

      let str = `Halo kak, saya mau order:\n\n`
    
      let items = data.items
      let numb = 1;
      items.forEach(el => {
        str +=  `*${numb}. ${el.name}*\n`
        if(el.note){
          str += `[${el.note}]\n`
        }
        str+= `Jumlah: ${el.quantity}\nHarga (@): ${this.moneyIDR(el.price)}\nHarga Total: ${this.moneyIDR(el.quantity*el.price)}\n\n`
        numb ++
      })

      str += `Subtotal: *${this.moneyIDR(data.order_subtotal)}*\nOngkir: *${this.moneyIDR(data.shipping_cost)}*\nDiskon: *${this.moneyIDR(data.discount)}*\nTotal: *${this.moneyIDR(data.order_total)}*\n------------------------\n\n*Nama:*\n ${data.customer_name} (${data.customer_whatsapp})\n\n*Alamat:*\n${this.formatAddressCod(data.shipping_address)}\n\n`

      str += `Metode Pembayaran: ${data.transaction.payment_name}\n\n`

      str += `Referensi Order:\n${this.getRoutePath(data.order_ref)}`

      let link = whatsappUrl+'/send?phone=' + whatsapp + '&text=' + encodeURI(str);

      window.open(link, '_blank');

    },
    formatPhoneNumber(number) {
      let formatted = number.replace(/\D/g,'')

      if(formatted.startsWith('0')) {
        formatted = '62' + formatted.substr(1)
      }

      return formatted;
    },
    getRoutePath(ref) {
      let props = this.$router.resolve({ 
        name: 'UserInvoice',
        params: { order_ref: ref },
      });

      return location.origin + props.href;
    },
    getDiscountPercent() {
      if(this.coupon_discount) {
        if(this.coupon_discount.discount.unit == 'percent') {
          return parseInt(this.coupon_discount.discount.value)
        } 
        return (parseInt(this.coupon_discount.discount.value)/parseInt(this.carts.subtotal))*100
      }
      return 0
    },
    getDiscountAmount() {
      if(this.coupon_discount) {
        if(this.coupon_discount.discount.unit == 'percent') {
          return (parseInt(this.coupon_discount.discount.value)/ 100)*parseInt(this.carts.subtotal)
        }
        return parseInt(this.coupon_discount.discount.value)
      }
      return 0
    },
    sumTotal() {
      if(this.coupon_discount) {
        return (this.carts.subtotal-this.getDiscountAmount())+parseInt(this.form.shipping_cost)+ parseInt(this.form.payment_fee)
      }
      return this.carts.subtotal + parseInt(this.form.shipping_cost) + parseInt(this.form.payment_fee)

    },
    getLocalBanks() {
      let self = this
      Api().get('banks').then(response => {
        if(response.status == 200) {
          self.paymentChanels.localbanks = response.data.results
        }
      })
    },
    getPaymentChanel() {
      let self = this
      Api().get('tripay/payment-chanel').then(response => {
        if(response.status == 200) {
          self.paymentChanels.paymentGateway = response.data.data
        }
      })
    },
    next() {
      if(this.isCod) {
        this.step = 3
        } else {
        this.step += 1
      }
    },
    prev() {
      if(this.isCod) {
        this.step = 1
        } else {
        this.step -= 1
      }
    },
  
  },
  meta() {
    return {
      title: 'Checkout'
    }
  }
}
</script>
