<template>
  <q-page padding>
    <q-header class="text-primary bg-white box-shadow">
        <q-toolbar>
          <q-btn @click="handleBackButton"
            flat round dense
            icon="eva-arrow-back" />
          <q-toolbar-title class="text-weight-bold brand">Form Pemesanan</q-toolbar-title>
        </q-toolbar>
    </q-header>
    <div id="checkout" v-if="carts && carts.items.length" ref="top" class="q-pb-xl">
      <shipping-address  
        :isModalAddress="isAvailableOldAddress"
        @closeModal="isAvailableOldAddress = false"
      />
      <div class="">
        <fieldset class="q-mt-lg">
          <legend class="q-pa-sm">Ringkasan Order</legend>
            <div v-if="formOrder.items.length" class="q-mb-md">
              <q-separator></q-separator>
              <q-list separator>
                <q-item class="bg-grey-1" dense>
                  <q-item-section>
                    <q-item-label>Produk</q-item-label>
                  </q-item-section>
                  <q-item-section side>
                    <q-item-label>Subtotal</q-item-label>
                  </q-item-section>
                </q-item>
                <q-item v-for="cart in formOrder.items" :key="cart.id">
                  <q-item-section avatar top>
                  <q-img :src="cart.image_url" style="width:50px;height:50px;"></q-img>
                  </q-item-section>
                  <q-item-section>
                    <div class="col">
                      <div class="text-weight-medium">{{ cart.name }}</div>
                      <div class="text-caption text-grey-7">{{ cart.note }}</div>
                      <div class="text-grey-7">{{ cart.quantity + 'X ' + moneyIDR(cart.price) }}</div>
                    </div>
                  </q-item-section>
                  <q-item-section side>
                    <q-item-label>{{ moneyIDR(cart.price*cart.quantity) }}</q-item-label>
                  </q-item-section>
                </q-item>
              </q-list>
            </div>
            <q-separator></q-separator>
            <div class="flex justify-end q-py-sm">
              <table class="table dense">
                <tr>
                  <td align="right">Jumlah</td>
                  <td align="right">:</td>
                  <td align="right">{{ moneyIDR(formOrder.subtotal) }}</td>
                </tr>
                <tr>
                  <td align="right">Ongkos Kirim</td>
                  <td align="right">:</td>
                  <td align="right">{{ formOrder.shipping_cost? moneyIDR(formOrder.shipping_cost) : 0 }}</td>
                </tr>
                <tr style="border-bottom:1px solid">
                  <td align="right">Total</td>
                  <td align="right">:</td>
                  <td align="right">{{ moneyIDR(formOrder.total) }}</td>
                </tr>
              </table>
            </div>
            <div class="flex justify-end q-mt-sm bg-grey-1 q-py-sm" v-if="formOrder.payment_fee">
              <table class="table dense">
                <tr v-if="formOrder.payment_fee">
                  <td align="right" class="text-xs">Payment Fee [ {{ formOrder.payment_name }} ]</td>
                  <td align="right">:</td>
                  <td align="right">{{ moneyIDR(formOrder.payment_fee) }}</td>
                </tr>
                <tr>
                  <th align="right">Total Tagihan</th>
                  <td align="right">:</td>
                  <th align="right">{{ moneyIDR(formOrder.total+formOrder.payment_fee) }}</th>
                </tr>
              </table>
            </div>
        </fieldset>
      </div>
    </div>
    <q-inner-loading :showing="loading">
        <q-spinner-facebook size="50px" color="primary"/>
    </q-inner-loading>
    <q-footer class="bg-white q-pa-md">
       <q-btn @click="checkout" color="green-6" class="full-width q-mt-sm" no-caps>
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 50 50" width="20px" height="20px">
          <g id="surface1441897">
          <path style=" stroke:none;fill-rule:nonzero;fill:currentColor;fill-opacity:1;" d="M 25 2 C 12.316406 2 2 12.316406 2 25 C 2 28.960938 3.023438 32.855469 4.964844 36.289062 L 2.035156 46.730469 C 1.941406 47.074219 2.035156 47.441406 2.28125 47.695312 C 2.472656 47.894531 2.734375 48 3 48 C 3.078125 48 3.160156 47.988281 3.238281 47.972656 L 14.136719 45.273438 C 17.464844 47.058594 21.210938 48 25 48 C 37.683594 48 48 37.683594 48 25 C 48 12.316406 37.683594 2 25 2 Z M 36.570312 33.117188 C 36.078125 34.476562 33.71875 35.722656 32.585938 35.886719 C 31.566406 36.035156 30.277344 36.101562 28.863281 35.65625 C 28.007812 35.386719 26.90625 35.027344 25.496094 34.429688 C 19.574219 31.902344 15.707031 26.011719 15.410156 25.625 C 15.117188 25.234375 13 22.464844 13 19.59375 C 13 16.726562 14.523438 15.3125 15.066406 14.730469 C 15.609375 14.144531 16.246094 14 16.640625 14 C 17.035156 14 17.429688 14.003906 17.773438 14.019531 C 18.136719 14.039062 18.625 13.882812 19.101562 15.023438 C 19.59375 16.191406 20.777344 19.058594 20.921875 19.351562 C 21.070312 19.644531 21.167969 19.984375 20.972656 20.375 C 20.777344 20.761719 20.679688 21.007812 20.382812 21.347656 C 20.085938 21.6875 19.761719 22.105469 19.496094 22.367188 C 19.199219 22.660156 18.894531 22.976562 19.238281 23.558594 C 19.582031 24.144531 20.765625 26.050781 22.523438 27.597656 C 24.777344 29.585938 26.679688 30.199219 27.269531 30.492188 C 27.859375 30.785156 28.203125 30.734375 28.550781 30.347656 C 28.894531 29.957031 30.023438 28.644531 30.417969 28.058594 C 30.8125 27.476562 31.203125 27.574219 31.746094 27.769531 C 32.289062 27.960938 35.191406 29.371094 35.78125 29.664062 C 36.371094 29.957031 36.765625 30.101562 36.914062 30.34375 C 37.0625 30.585938 37.0625 31.753906 36.570312 33.117188 Z M 36.570312 33.117188 "/>
          </g>
        </svg>
        <span class="q-ml-sm">
          {{ isOk? 'Proses Pesanan' : 'Lengkapi data untuk lanjut' }}
        </span>
      </q-btn>
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
    errors() {
      return this.$store.state.errors
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
    },
    isOk() {
      if(this.formOrder.customer_name 
          && this.formOrder.customer_phone
          && this.formOrder.shipping_courier_name
          && this.formOrder.customer_address) {
            return true
          }

      return false
    },
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
    validateData() {
      this.$store.commit('CLEAR_ERRORS')
      let validationCustomer = ['customer_name', 'customer_phone', 'customer_address']
      let validationShipping = [ 'shipping_destination', 'shipping_courier_name', 'shipping_courier_service']

       for(let x in this.formOrder) {
        if(validationCustomer.includes(x) || validationShipping.includes(x)) {
          if(!this.formOrder[x] || this.formOrder[x] == '') {
            this.$store.commit('PUSH_ERRORS', { key: x, value: true })
          }
        }
      }
       if(Object.keys(this.errors).length > 0) {
        for(let i in this.errors) {
          if(i == 'shipping_destination') {
            this.jumpTo('shipping')
            this.$q.notify({
              type: 'negative',
              message: 'Tujuan pengiriman belum diisi'
            })
            return false
          }

          if(i == 'shipping_courier_service') {
            this.jumpTo('courier')
            this.$q.notify({
              type: 'negative',
              message: 'Kurir belum dipilih'
            })
            return false
          }

          if(validationCustomer.includes(i)) {
            this.jumpTo('customer')
            this.$q.notify({
              type: 'negative',
              message: 'Data penerima belum lengkap'
            })
            return false
          }

        }

        return false
      }
      return true
    },
    checkout() {
      if(!this.validateData()) return
      if(!this.isOk) return

      let whatsappUrl = 'https://api.whatsapp.com'
      if(this.$q.platform.is.desktop) {
        whatsappUrl = 'https://web.whatsapp.com'
      }
      
      let whatsapp = this.formatPhoneNumber(this.shop.phone)

      let str = `Halo kak, saya mau order:\n\n`
    
      let items = this.formOrder.items
      let numb = 1;
      items.forEach(el => {
        str +=  `*${numb}. ${el.name}*\n`
        if(el.note){
          str += `${el.note}\n`
        }
        str+= `Jumlah: ${el.quantity}\nHarga (@): ${this.moneyIDR(el.price)}\nHarga Total: ${this.moneyIDR(el.quantity*el.price)}\n\n`
        numb ++
      })

      str += `Subtotal: *${this.moneyIDR(this.formOrder.subtotal)}*\n`
      str += `Ongkir: *${this.moneyIDR(this.formOrder.shipping_cost)}*\n`
      str += `Diskon: *${this.moneyIDR(this.formOrder.coupon_discount)}*\n`
      str += `Total: *${this.moneyIDR(this.formOrder.total)}*\n`
      str += `-----------------------------------\n\n`
      str += `*Nama:*\n ${this.formOrder.customer_name} (${this.formOrder.customer_phone})\n\n`
      str += `*Alamat:*\n${this.formatAddressCod(this.formOrder.customer_address)}\n\n`
      str += `Kurir: ${this.formOrder.shipping_courier_name}\n`
      str += `Servis: ${this.formOrder.shipping_courier_service}\n`

      // console.log(str); return

      let link = whatsappUrl+'/send?phone=' + whatsapp + '&text=' + encodeURI(str);
      setTimeout(() => {
        this.$router.push({ name: 'Cart'})
      }, 1000)
      setTimeout(() => {
        this.$store.dispatch('cart/clearCart', this.session_id)
      }, 5000)
      window.open(link, '_blank');

    },
  }
}
</script>
