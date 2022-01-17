<template>
  <q-page class="q-pb-xl">
    <q-header>
      <q-toolbar>
        <q-btn :to="{name: 'Settings'}"
          flat round dense
          icon="arrow_back" />
        <q-toolbar-title>
         List Pesanan
        </q-toolbar-title>
      </q-toolbar>
    </q-header>
    <div class="q-pa-md ">
      <div class="row items-center q-gutter-x-sm">
        <q-btn color="primary" :icon="isFilter? 'search' : 'sort'" unelevated readonly @click="handleSelectMode"></q-btn>
        <div class="col">
          <q-select v-if="isFilter" v-model="filter" class="bg-grey-3 q-px-sm" dense borderless :options="options" @input="handleFilterOrder" placeholder="Filter Order">
          </q-select>
          <q-input v-model="search" v-if="!isFilter" placeholder="Ketik no invoice atau whatsapp" dense borderless class="bg-grey-3 q-px-sm" @keypress.enter="handleSearchOrder"></q-input>
        </div>
        <div>
          <q-btn v-if="!isFilter" label="Cari" @click="handleSearchOrder" unelevated color="primary"></q-btn>
        </div>
      </div>
    </div>
    <template v-if="orders.data.length">
    <div>
      <q-list separator>
        <q-item>
          <q-item-section side class="xs-hide">
           #
          </q-item-section>
          <q-item-section>
            <q-item-label>Detail</q-item-label>
          </q-item-section>
          <q-item-section side>
            <q-item-label>Actions</q-item-label>
          </q-item-section>
        </q-item>
        <q-item v-for="(order, index) in orders.data" :key="index">
          <q-item-section side top class="xs-hide">
            {{ index+1 }}
          </q-item-section>
          <q-item-section top>
            <div class="text-sm1">
              <table class="dense">
                <tr>
                  <td>INVOICE</td>
                  <td>{{ order.order_ref}}</td>
                </tr>
                <tr>
                  <td>Nama</td>
                  <td>{{ order.customer_name }}</td>
                </tr>
                <tr>
                  <td>WA</td>
                  <td>{{ order.customer_whatsapp }}</td>
                </tr>
                <tr>
                  <td>Total</td>
                  <td>{{ moneyIDR(order.order_total) }}</td>
                </tr>
                <tr>
                  <td>Dibuat</td>
                  <td>{{ order.created_at }}</td>
                </tr>
                <tr>
                  <td>Pengiriman</td>
                  <td>{{ order.shipping_courier_name }}</td>
                </tr>
                <tr>
                  <td>Pembayaran</td>
                  <td>{{ order.transaction? order.transaction.payment_name : '' }}</td>
                </tr>
                <tr>
                  <td>Status</td>
                  <td>
                    <q-badge :color="changeBadgeColor(order.order_status)">{{ order.status_label }}</q-badge>
                  </td>
                </tr>
              </table>
            </div>
          </q-item-section>
          <q-item-section side top>
            <div class="column q-gutter-y-sm">
              <q-btn unelevated @click="handleFollowUp(order)" no-caps size="12px" :label="messageButtonLabel(order.order_status)" color="green-7"></q-btn>
              <q-btn unelevated v-if="canConfirm(order)" no-caps size="12px" label="Konfirmasi" color="blue-7" @click="handleConfirmationOrder(order.id)"></q-btn>
              <q-btn unelevated no-caps size="12px" label="Hapus" color="red-7" @click="handleDeleteOrder(order.id)"></q-btn>
              <q-btn unelevated no-caps size="12px" label="Detail" color="purple-7" :to="{name: 'AdminOrderShow', params: {order_ref: order.order_ref}}"></q-btn>
              <q-btn unelevated v-if="canInputResi(order)" no-caps size="12px" label="Input Resi" color="orange" @click="handleInputResi(order)"></q-btn>
              <q-btn unelevated v-if="canShip(order)" no-caps size="12px" label="Kirim COD" color="orange" @click="handleKirimCod(order)"></q-btn>
              <q-btn unelevated v-if="canComplete(order)" no-caps size="12px" label="Order Selesai" color="blue-6" @click="handleCompletionOrder(order)"></q-btn>
            </div>
          </q-item-section>
        </q-item>
      </q-list>
    </div>
   <div class="flex justify-center q-py-xl">
     <q-btn outline :loading="orders.isLoadMore" v-if="orders.canLoadMore && isFilter" label="loadmore..." @click="loadMore" unelevated color="primary"></q-btn>
   </div>
    </template>
     <template v-else>
      <div class="text-center q-pt-xl">Tidak ada data</div>
    </template>
    <q-dialog v-model="followUpModal">
      <follow-up @close="followUpModal= false" :order="currentOrder" />
    </q-dialog>

    <q-dialog v-model="inputResiModal">
      <q-card square style="width:100%;max-width:420px;">
        <div class="q-px-md q-py-sm bg-blue-7 text-white text-weight-bold">Input Resi  <span v-if="orderSelected"># {{ orderSelected.code }}</span></div>
       <q-form @submit.prevent="submitResi" >
        <q-card-section>
          <q-input 
          filled
          square
          label="No Resi"
          class="text-sm"
          v-model="form.resi"
          :rules="[val => val && val.length > 0 || 'Wajib diisi']"
          />
        </q-card-section>
        <q-card-actions class="justify-end">
          <q-btn flat label="Batal" @click.prevent="closeModal" color="primary"></q-btn>
          <q-btn unelevated type="submit" label="Simpan" color="primary"></q-btn>
        </q-card-actions>
       </q-form>
      </q-card>
    </q-dialog>
    <q-inner-loading :showing="loading">
        <q-spinner-facebook size="50px" color="primary"/>
    </q-inner-loading>
  </q-page>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import FollowUp from './FollowUp.vue'
export default {
  name: 'OrderIndex',
  components: { FollowUp },
  data() {
    return {
      isFilter: true,
      options: ['ALL', 'UNPAID', 'PAID', 'PROCESS', 'SHIPPING', 'COMPLETE', 'CANCELED'],
      inputResiModal: false,
      orderSelected: '',
      followUpModal: false,
      currentOrder: null,
      search: '',
      filter: 'ALL',
      form: {
        order_id: '',
        resi: '',
        status: ''
      },
    }
  },
  computed: {
    ...mapState({
      orders: state => state.order.orders,
      loading: state => state.loading
    }),
  },
  created() {
    this.getOrders()
  },
  methods: {
    ...mapActions('order', ['getOrders', 'getPaginateOrder', 'getPaginateFilterOrder', 'destroyOrder', 'acceptPayment', 'inputResi', 'updateStatusOrder', 'searchOrder', 'filterOrder']),
    loadMore() {
      this.getPaginateOrder({ filter: this.filter, skip: this.orders.data.length })
    },
    handleSelectMode(evt) {
      this.isFilter = !this.isFilter
      this.search = ''
      this.filter = ''
    },
    handleKirimCod(order) {
      this.$q.dialog({
        title: 'Konfirmasi',
        message: 'Akan mengirim pesanan sekarang?, ini akan merubah status pesanan menjadi "sedang dikirim"',
        cancel: true,
      }).onOk(() => {
        this.form.status = 'SHIPPING'
        this.form.order_id = order.id
        this.updateStatusOrder(this.form)
      })
    },
    canShip(order) {
      if(order.shipping_courier_name == 'COD') {
        if(order.order_status == 'PROCESS' || order.order_status == 'UNPAID') {
          return true
        } else {
          return false
        }
      } else {
        return false
      }
    },
    canComplete(order) {
      return order.order_status == 'SHIPPING' ? true : false
    },
    handleCompletionOrder(order) {
      this.form.status = 'COMPLETE'
      this.form.order_id = order.id

      this.$q.dialog({
        title: 'Konfirmasi',
        message: 'Ini akan merubah status pesanan menjadi "selesai"',
        cancel: true,
      }).onOk(() => {
        this.updateStatusOrder(this.form)
      })
      
    },
    handleSearchOrder() {
      this.$store.commit('SET_LOADING', true)
      this.searchOrder(this.search)
    },
    handleFilterOrder() {
      this.filterOrder(this.filter)
    },
    resetOrder() {
      this.orderFiltered = []
      this.isFilter = false
      this.search = ''
      this.filter = ''
    },
    changeBadgeColor(type) {
      if(type == 'PAID' || type == 'SHIPPING') return 'teal'
      if(type == 'PROCESS') return 'blue-7'
      if(type == 'COMPLETE') return 'green-6'
      if(type == 'UNPAID') return 'grey-6'
      return 'grey-7'
    },
    canConfirm(order) {
      if(order.shipping_courier_name != 'COD') {
        if(order.order_status == 'UNPAID' || order.order_status == 'OVERDUE') return true

        return false
      } else {
        return false

      }
    },
    canInputResi(order) {
      if(order.shipping_courier_name == 'COD') {
        return false
      } else {
        if(order.order_status == 'PROCESS' || order.order_status == 'PAID') { 
          return true
        } else {
          return false
        }
      }

    },
    messageButtonLabel(status) {
      if(status == 'UNPAID' || status == 'OVERDUE') return 'Follow Up Order'
      return 'Kirim Pesan'
    },
    money(number) {
     return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR'}).format(number)
    },
    handleDeleteOrder(id) {
      this.$q.dialog({
        title: 'Yakin akan menghapus data?',
        message: 'data yang dihapus tidak dapat dikembalikan.',
        ok: {label: 'Hapus', flat: true, 'no-caps': true, 'color' :'red-7'},
        cancel: {label: 'Batal', flat: true, 'no-caps': true},
        }).onOk(() => {
          this.destroyOrder(id)
        }).onCancel(() => {
        }).onDismiss(() => {
      })
    },
    handleConfirmationOrder(id) {
      this.$q.dialog({
        title: 'Konfirmasi pembayaran',
        message: 'Pastikan pembayaran telah anda terima dengan baik',
        ok: {label: 'Konfirmasi', flat: true, 'no-caps': true, 'color' :'green-7'},
        cancel: {label: 'Batal', flat: true, 'no-caps': true},
        }).onOk(() => {
          this.acceptPayment(id)
        }).onCancel(() => {
        }).onDismiss(() => {
      })
    },
    handleFollowUp(data) {
      this.currentOrder = data
      this.followUpModal = true
    },
    handleInputResi(order) {
      this.form.resi = ''
      this.orderSelected = order
      this.form.order_id = order.id
      this.inputResiModal = true
    },
    closeModal() {
      this.inputResiModal = false
      this.orderSelected = ''
      this.form.order_id = ''
    },
    submitResi() {
      this.inputResi(this.form)
      this.closeModal()
    }
  },
  
}
</script>
