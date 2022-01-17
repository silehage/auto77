<template>
  <q-page class="q-pb-lg bg-grey-2">
    <q-header class="text-primary bg-white">
      <q-toolbar>
        <q-btn :to="{name: 'CustomerOrder'}"
          flat round dense
          icon="arrow_back" />
        <q-toolbar-title v-if="invoice" class="text-weight-bold brand">Invoice {{ invoice.order_ref }}</q-toolbar-title>
      </q-toolbar>
    </q-header>
    <div v-if="invoice">
      <div class="q-pt-md q-gutter-y-md">
        <div class="q-mt-md q-pb-lg bg-white q-pa-md">
          <div class="flex justify-between text-grey-8">
            <div class="q-mb-sm">
              <div class="text-weight-bold q-mb-xs">{{ shop.name }}</div>
              <div class="">{{ shop.phone }}</div>
              <div class="text-weight-thin" v-html="shop.address"></div>
            </div>
            <div>
              <table>
                <tr>
                  <td>Invoice</td>
                  <td>: {{ invoice.order_ref }}</td>
                </tr>
                <tr>
                  <td>Referensi</td>
                  <td>: {{ invoice.transaction.payment_ref }}</td>
                </tr>
                <tr>
                  <td>Dibuat</td>
                  <td>: {{ invoice.created_at }}</td>
                </tr>
                <tr>
                  <td colspan="2">
                    <div class="full-width q-px-md q-py-xs text-white text-center" :class="isPaid? 'bg-green-7' : 'bg-grey-7'">
                      {{ isPaid? 'DIBAYAR' : 'BELUM BAYAR' }}
                    </div>
                  </td>
                </tr>
              </table>
            </div>
          </div>
          <div class="q-mt-lg text-grey-8">
            <table class="table-order-item" v-if="invoice.items">
              <tr>
                <th align="left" colspan="2">Item</th>
                <th align="left">qty</th>
                <th align="right">Harga</th>
              </tr>
              <tr v-for="(item, index) in invoice.items" :key="index">
                <td>
                  <q-img :src="item.image_url" width="50px" ratio="1" class="rounded-corners"></q-img>
                </td>
                <td class="">{{ item.name }}</td>
                <td>{{ item.quantity }}</td>
                <td align="right">{{ moneyIDR(item.price) }}</td>
              </tr>
            </table>
            <div class="column justify-end bg-grey-1 items-end q-pa-sm">
              <table class="dense">
                <tr>
                  <td align="right">SubTotal</td>
                  <td>:</td>
                  <td align="right">{{ moneyIDR(invoice.order_subtotal) }}</td>
                </tr>
                <tr>
                  <td align="right">Ongkos Kirim</td>
                  <td>:</td>
                  <td align="right">{{ moneyIDR(invoice.shipping_cost) }}</td>
                </tr>
                <tr>
                  <td align="right">Kode Unik</td>
                  <td>:</td>
                  <td align="right">- {{ invoice.order_unique_code ? invoice.order_unique_code : '-' }}</td>
                </tr>
                <tr>
                  <th align="right">Total</th>
                  <td>:</td>
                  <th align="right">{{ moneyIDR(invoice.order_total) }}</th>
                </tr>
              </table>
            </div>
          </div>
        </div>
        <q-card class="shadow" flat square>
            <div class="card-heading">Customer</div>
          <q-card-section>
            <div class="text-grey-9">
              <table class="dense">
                <tr>
                  <td>Nama</td>
                  <td>:</td>
                  <td>{{ invoice.customer_name }}</td>
                </tr>
                <tr>
                  <td>Whatsapp</td>
                  <td>:</td>
                  <td>{{ invoice.customer_whatsapp }}</td>
                </tr>
                <tr>
                  <td style="vertical-align:top;">Alamat</td>
                  <td style="vertical-align:top;">:</td>
                  <td> <div v-html="invoice.shipping_address"></div> </td>
                </tr>
              </table>
            </div>
          </q-card-section>
        </q-card>
        <q-card class="bg-white shadow" square>
            <div class="card-heading">Ekspedisi</div>
          <q-card-section>
            <div class="text-grey-9">
              <table class="dense">
                <tr>
                  <td>Kurir</td>
                  <td>:</td>
                  <td>{{ invoice.shipping_courier_name }}</td>
                </tr>
                <tr>
                  <td>Service</td>
                  <td>:</td>
                  <td>{{ invoice.shipping_courier_service ? invoice.shipping_courier_service : '-' }}</td>
                </tr>
                <tr>
                  <td>No Resi</td>
                  <td>:</td>
                  <td>{{ invoice.shipping_courier_code? invoice.shipping_courier_code : '-'  }}</td>
                </tr>
              </table>
            </div>
          </q-card-section>
        </q-card>
        <q-card class="bg-white shadow" square>
          <div class="card-heading border-b">Pembayaran</div>
          <q-card-section>
              <table class="dense">
                <tr>
                  <td>Metode</td>
                  <td>:</td>
                  <td>{{ invoice.transaction.payment_method.split('_').join(' ') }}</td>
                </tr>
                <tr>
                  <td style="vertical-align:top;">Detil</td>
                  <td style="vertical-align:top;">:</td>
                  <td><div v-html="invoice.transaction.payment_name"></div></td>
                </tr>
                <tr>
                  <td>Referensi</td>
                  <td>:</td>
                  <td>{{ invoice.transaction.payment_ref }}</td>
                </tr>
              </table>
          </q-card-section>
        </q-card>
      </div>
      <q-dialog 
      v-model="modalPayment"
      persistent
      maximized
      seamless
      transition-show="slide-up"
      transition-hide="slide-down"
      >
        <q-card flat square class="max-width bg-grey-2">
            <q-toolbar class="bg-white sticky-top border-b">
              <q-toolbar-title><span class="text-weight-bold card-heading">Pembayaran</span></q-toolbar-title>
              <q-btn flat round dense icon="close" v-close-popup />
            </q-toolbar>
            <keep-alive>
            <component v-bind:is="isPaymentType" v-bind:transaction="invoice.transaction" @kirimBukti="kirimBuktiTransfer"></component>
            </keep-alive>
        </q-card>
      </q-dialog>
      <q-footer class="bg-grey-2 q-pa-md" v-if="!isPaid">
        <div class="text-center text-grey-7 column q-gutter-y-sm">
            <q-btn v-if="invoice && invoice.transaction.payment_method != 'COD'" label="Instruksi Pembayaran" no-caps icon="payments" @click.prevent="handlePaymentModal" color="green-7"></q-btn>
            <q-btn ref="chatAdmin" label="Chat admin" icon="chat" no-caps  @click="chatToAdmin" color="blue-7"></q-btn>
        </div>
      </q-footer>
    </div>
    <q-inner-loading :showing="loading">
        <q-spinner-facebook size="50px" color="primary"/>
    </q-inner-loading>
  </q-page>
</template>

<script>
import { copyToClipboard } from 'quasar'
import { mapActions, mapState } from 'vuex'
import PaymentGateway from './PaymentGateway.vue'
import DirectPayment from './DirectPayment.vue'
export default {
  name: 'InvoiceIndex',
  components: { PaymentGateway, DirectPayment },
  data () {
    return {
      modalPayment: false,
      throtle: 1
    }
  },
  computed: {
    ...mapState({
      loading: state => state.loading,
      shop: state => state.shop,
      invoice: state => state.order.invoice,
      invoice: state => state.order.invoice,
    }),
    isPaid() {
      return this.invoice.transaction.status == 'PAID' ? true : false
    },
    isPaymentType: function() {
      return this.invoice.transaction.payment_type == 'DIRECT' ? 'DirectPayment' : 'PaymentGateway';
    },
  },
  created() {
    if(!this.invoice){
      this.getOrder()
    } else {
      if(this.invoice.order_ref != this.$route.params.order_ref) {
        this.getOrder()
      } else {
        this.ready = true
      }
    }
  },

  methods: {
    ...mapActions('order', ['getOrderById']),
    getOrder() {
      let self = this;
      self.$store.commit('SET_LOADING', true)
      if(this.$route.params.order_ref) {
        this.getOrderById(this.$route.params.order_ref).then(response => {
          if(response.status == 200) {
            self.$store.commit('order/SET_INVOICE', response.data.results)
          }
          self.$store.commit('SET_LOADING', false)
        }).catch(() => {
          this.$router.push({name: 'Cart'})
        })
      } else {
        this.$router.push({name: 'Cart'})
      }
    },
    money(number) {
     return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR'}).format(number)
    },
    chatToAdmin() {
      let text = `Halo ${this.shop.name},\nMohon pesanan saya untuk segera di proses.\nTerima Kasih.\n\nReferensi Order:\n${location.href}`
      let url = `https://api.whatsapp.com/send?phone=${this.formatPhoneNumber(this.shop.phone)}&text=${encodeURI(text)}`

      window.open(url, '_blank')
    },
    kirimBuktiTransfer() {
      this.modalPayment = false
      let text = `Halo kak, saya akan mengkonfirmasi pembayaran atas pesanan\nInvoice ID: *${this.invoice.order_ref}*\nReferensi Order:\n${location.href}\n`
      let url = `https://api.whatsapp.com/send?phone=${this.formatPhoneNumber(this.shop.phone)}&text=${encodeURI(text)}`

      window.open(url, '_blank')
    },
    formatPhoneNumber(number) {
      let formatted = number.replace(/\D/g,'')

      if(formatted.startsWith('0')) {
        formatted = '62' + formatted.substr(1)
      }

      return formatted;
    },
    copy(str) {
      copyToClipboard(str)
      .then(() => {
        this.$q.notify({
          type: 'positive',
          message: 'Berhasil menyalin nomor rekening'
        })
      })
      .catch(() => {
        this.$q.notify({
          type: 'negative',
          message: 'Browser anda tidak support copy to clipboard'
        })
      })
    },
    handlePaymentModal() {
      this.modalPayment = true
    }
  },

}
</script>

<style lang="scss">
.table-order-item {
  width: 100%;
  border-spacing: inherit;
  tr {
    th, td {
      padding:.5rem;
    }
    th {
      background-color: #666;  
      color: #ccc;
    }
    td {
      border-bottom: 1px solid #eee;
    }
  }
}
</style>