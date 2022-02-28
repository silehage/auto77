<template>
  <q-page class="q-pb-lg bg-grey-2">
    <q-header class="text-primary bg-white no-print">
      <q-toolbar>
        <q-btn v-go-back.single
          flat round dense
          icon="arrow_back" />
        <q-toolbar-title v-if="invoice" class="text-weight-bold brand">Invoice {{ invoice.order_ref }}</q-toolbar-title>
        <q-btn @click="print">Print</q-btn>
      </q-toolbar>
    </q-header>
    <div v-if="invoice">
      <div class="q-pt-md q-gutter-y-md">
        <div class="q-mt-md q-pb-lg bg-white q-pa-md">
          <div class="flex justify-between text-grey-8">
            <div class="q-mb-sm">
              <div class="text-weight-bold">{{ shop.name }}</div>
              <div class="text-weight-regular">Phone: {{ shop.phone }}</div>
              <div class="text-weight-regular" v-html="shop.address"></div>
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
                    <div class="full-width q-px-md q-py-xs text-white text-center" :class="statusColor(invoice.order_status)">
                      {{ invoice.status_label }}
                    </div>
                  </td>
                </tr>
              </table>
            </div>
          </div>
          <div class="q-mt-lg text-grey-8">
            <table class="table-order-item" v-if="invoice.items">
              <tr>
                <th align="left">Item</th>
                <th align="left">qty</th>
                <th align="right">Harga</th>
              </tr>
              <tr v-for="(item, index) in invoice.items" :key="index">
                <td>{{ item.name }}</td>
                <td>{{ item.quantity }}</td>
                <td align="right">{{ moneyIDR(item.price) }}</td>
              </tr>
            </table>
           <div class="column justify-end items-end q-py-sm">
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
                <tr v-if="invoice.order_unique_code">
                  <td align="right">Kode Unik</td>
                  <td>:</td>
                  <td align="right">- {{ invoice.order_unique_code }}</td>
                </tr>
                <tr>
                  <td align="right">Total</td>
                  <td>:</td>
                  <td align="right">{{ moneyIDR(invoice.order_subtotal+invoice.shipping_cost) }}</td>
                </tr>
              </table>
            </div>
            <div class="flex justify-end q-py-sm bg-grey-1">
              <table class="table dense">
                <tr v-if="invoice.discount">
                  <td align="right">Diskon [-]</td>
                  <td align="right">:</td>
                  <td align="right">{{ invoice.discount? moneyIDR(invoice.discount) : 0 }}</td>
                </tr>
                <tr>
                  <th align="right">Grand Total</th>
                  <td align="right">:</td>
                  <th align="right">{{ moneyIDR(invoice.order_total) }}</th>
                </tr>
              </table>
            </div>
          </div>
        </div>
        <q-card class="no-print shadow" flat square>
            <div class="card-heading">Informasi Penerima</div>
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
        <q-card class="no-print bg-white shadow" square>
            <div class="card-heading">Informasi Ekspedisi</div>
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
        <q-card class="no-print bg-white shadow" square>
          <div class="card-heading border-b">Informasi Pembayaran</div>
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
    </div>
    <q-inner-loading :showing="loading" class="no-print">
        <q-spinner-facebook size="50px" color="primary"/>
    </q-inner-loading>
  </q-page>
</template>

<script>
import { copyToClipboard } from 'quasar'
import { mapActions, mapState } from 'vuex'

export default {
  name: 'InvoiceIndex',
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
    }),
    isPaid() {
      return this.invoice.transaction.status == 'PAID' ? true : false
    },
    isPaymentType: function() {
      return this.invoice.transaction.payment_type == 'DIRECT' ? 'DirectPayment' : 'PaymentGateway';
    },
  },
  created() {
    this.getOrder()
  },

  methods: {
    ...mapActions('order', ['getOrderById']),
    statusColor(status) {
      if(status == 'UNPAID') return 'bg-grey-7'
      if(status == 'CANCELED') return 'bg-red-6'
      if(status == 'COMPLETE') return 'bg-green-6'
      return 'bg-blue-7'
    },
    print() {
      window.print()
    },
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
      let text = `Halo ${this.shop.name},\nKonfirmasi pembayaran atas pesanan:\n*INVOICE ${this.invoice.order_ref}*\n\nReferensi Order:\n${location.href}\n\nBerikut saya sertakan bukti transfer.`
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
@media print{
  .no-print{
    display: none;
  }
}
</style>