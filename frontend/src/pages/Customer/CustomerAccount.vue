<template>
  <q-page>
    <q-header class="bg-primary" dark>
      <q-toolbar>
        <q-toolbar-title>
           Akun
        </q-toolbar-title> 
        <q-btn to="/"
        flat dense padding="xs"
        label="Beranda" no-caps
        icon-right="eva-arrow-forward" />
      </q-toolbar>
    </q-header>
    <div class="header">
        <div class="header-inner q-pt-sm q-pb-lg large">
          <q-card class="bg-primary q-py-lg" flat dark square>
            <q-list>
              <q-item>
                <q-item-section avatar>
                  <q-avatar color="pink" text-color="white" size="70px">{{ initialName }}</q-avatar>
                </q-item-section>
                <q-item-section>
                  <q-item-label class="text-h5 text-weight-bold">{{ user.name }}</q-item-label>
                  <q-item-label class="text-grey-1">{{ user.phone }}</q-item-label>
                  <q-item-label class="text-grey-4">{{ user.email }}</q-item-label>
                </q-item-section>
                <q-item-section side top >
                  <div class="column q-gutter-y-sm">
                    <q-btn icon="eva-edit-2" round size="11px" color="grey-4" text-color="dark" :to="{ name: 'CustomerAccountEdit' }">
                      <q-tooltip content-class="bg-indigo" :offset="[10, 10]">Edit Akun</q-tooltip>
                    </q-btn>
                    <q-btn icon="eva-log-out" round size="11px" color="grey-4" text-color="dark" @click="logout">
                     <q-tooltip content-class="bg-amber text-black" :offset="[10, 10]">Keluar</q-tooltip>
                    </q-btn>
                  </div>
                </q-item-section>
              </q-item>
            </q-list>
          </q-card>
        </div>
      </div>
      <div class="q-mt-lg q-pa-sm">
        <div class="flex justify-between q-pa-sm items-center">
          <div class="text-weight-bold text-md">Transaksi Terbaru</div>
          <q-btn color="primary" label="Selengkapnya" flat no-caps icon-right="eva-chevron-right" :to="{ name: 'CustomerOrder' }"></q-btn>
        </div>

        <q-list separator>
          <q-item>
            <q-item-section side>#</q-item-section>
            <q-item-section>Invoice</q-item-section>
            <q-item-section class="mobile-hide">Total</q-item-section>
            <q-item-section>Status</q-item-section>
            <q-item-section side>Detail</q-item-section>
          </q-item>
          <q-item v-for="(order, index) in currentOrder" :key="order.id">
            <q-item-section side top>
              {{ index+1 }}
            </q-item-section>
            <q-item-section>
              <q-item-label>{{ order.order_ref }}</q-item-label>
              <q-item-label class="desktop-hide">{{ moneyIDR(order.grand_total) }}</q-item-label>
              <q-item-label caption>{{ order.created }}</q-item-label>
            </q-item-section>
            <q-item-section class="mobile-hide">{{ moneyIDR(order.grand_total) }}</q-item-section>
            <q-item-section>
              <div class="row">
                 <q-badge class="text-center justify-center" style="min-width:90px;padding:4px;" :color="changeBadgeColor(order.order_status)">{{ order.status_label }}</q-badge>  
              </div>
            </q-item-section>
            <q-item-section side>
              <q-btn icon="eva-external-link-outline" round flat :to="{name: 'UserInvoice', params: {order_ref: order.order_ref}}"></q-btn>
            </q-item-section>
          </q-item>
        </q-list>
      </div>
  </q-page>
</template>

<script>
import { mapState, mapActions} from 'vuex'
import { Api } from 'boot/axios'
export default {
  data () {
    return {
      isPwd: true,
      isPwd1: true,
      changePassword: false,
      form: {
        name:'',
        email:'',
        phone: '',
        password: '',
        password_confirmation: '',
      }
    }
  },
  computed: {
    ...mapState({
      user: state => state.user.user,
      loading: state => state.loading,
      orders: state => state.order.customer_order,
    }),
    currentOrder() {
      if(this.orders.data.length) {
        return this.orders.data.slice(0, 5)
      }
      return []
    },
    initialName() {
      if(this.user) {
        let named = this.user.name.split(' ').map(el => el.slice(0,1)).join('')
        return named.slice(0,2).toUpperCase()
      }
      return 'SW'
    }
  },
  created() {
    if(!this.orders.data.length) {
      this.$store.dispatch('order/getCustomerOrders')
    }
    if(!this.user) {
      Api().get('user').then(response => {
        if(response.status == 200) {
          this.form.name = response.data.results.name
          this.form.email = response.data.results.email
          this.form.phone = response.data.results.phone
          this.$store.commit('user/SET_USER', response.data.results)
        }
      })
    } else {
      this.form.name = this.user.name
      this.form.email = this.user.email
      this.form.phone = this.user.phone
    }
  },
  methods: {
    ...mapActions('user', ['getUser', 'updateUser']),
    submit() {
      this.$store.commit('SET_LOADING', true)
      this.updateUser(this.form)
    },
    btnChangePassword() {
      this.changePassword = !this.changePassword
      this.form.password_confirmation = ''
      this.form.password = ''
    },
    changeBadgeColor(type) {
      if(type == 'PAID' || type == 'SHIPPING') return 'teal'
      if(type == 'PROCESS') return 'blue'
      if(type == 'COMPLETE') return 'green'
      if(type == 'CANCELED') return 'red'
      return 'grey-7'
    },
    messageButtonLabel(status) {
      if(status == 'UNPAID' || status == 'OVERDUE') return 'Follow Up Order'
      return 'Kirim Pesan'
    },
    logout() {
      this.$store.dispatch('user/logout')
    },
  },

}
</script>

<style>

</style>