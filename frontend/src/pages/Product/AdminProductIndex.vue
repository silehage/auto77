<template>
  <q-page :class="{'flex flex-center' : !products.available}">
     <q-header>
      <q-toolbar>
        <q-btn :to="{name: 'Settings'}"
          flat round dense
          icon="arrow_back" />
        <q-toolbar-title>
         List Produk
        </q-toolbar-title>
        <q-btn class="gt-xs" no-caps flat icon="add_circle" :to="{name: 'ProductCreate'}" label="Tambah Produk"/>
      </q-toolbar>
    </q-header>
    <template v-if="products.available">
     <div class="q-pt-sm q-pb-xl">
      <q-list separator>
       <q-item v-for="product in products.data" :key="product.id" class="q-py-md">

         <q-item-section avatar class="q-pr-sm">
           <q-img v-if="product.assets" :src="product.assets[0].src" class="bg-white img-product-admin" ratio="1"/>
        </q-item-section>

        <q-item-section top>
          <div class="q-gutter-y-xs">
          <q-item-label lines="2">{{ product.title }}</q-item-label>
          <q-item-label class="text-green-7 text-weight-medium">{{ moneyIDR(product.price) }}</q-item-label>
            </div>
          <q-item-label caption class="ellipsis-2-lines" v-html="product.description"></q-item-label>
          <div class="q-mt-sm text-xs">
           <q-chip v-if="product.category" dense size="12px" outline color="green-6">
            <q-avatar icon="sell" color="primary" text-color="white"/>
           {{ product.category.title }}
          </q-chip>
            <q-chip :label="product.weight + ' gram'" dense size="12px" outline color="green-6"></q-chip>
            <q-chip :label="'Stok: ' +product.stock " dense size="12px" outline color="green-6"></q-chip>
          </div>
        </q-item-section>

        <q-item-section side top>
          <div class="text-grey-8 column q-gutter-y-xs">
            <q-btn unelevated @click="remove(product.id)" size="sm" round icon="delete" glossy color="red"/>
            <q-btn unelevated :to="{ name: 'ProductEdit', params: {id: product.id }}" size="sm" round glossy color="info" icon="edit" />
            <q-btn unelevated :to="{ name: 'ProductShow', params: {slug: product.slug }}" size="sm" round glossy color="teal" icon="visibility" />
          </div>
        </q-item-section>
      </q-item>
    </q-list>
    </div>
    </template>
    <template v-else>
      <div>Tidak ada data</div>
    </template>
     <q-inner-loading :showing="!products.ready">
        <q-spinner-facebook size="50px" color="primary"/>
    </q-inner-loading>
    <q-page-sticky class="lt-sm" position="bottom-right" :offset="[18, 18]">
      <q-btn fab icon="add" color="primary" :to="{name: 'ProductCreate'}" glossy/>
    </q-page-sticky>
  </q-page>
</template>

<script>
import { mapState, mapActions } from 'vuex'
export default {
  name: 'PageAdminIndex',
  computed: {
    ...mapState({
      products: state => state.product.admin_products
    })
  },
  methods: {
    ...mapActions('product', ['getAdminProducts', 'productDelete']),
    money(number) {
      return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(number)
    },
    remove(id) {
      this.$q.dialog({
        title: 'Konfirmasi Penghapusan Item',
        message: 'Yakin akan menghapus data?',
        ok: {label: 'Hapus', flat: true, 'no-caps': true},
        cancel: {label: 'Batal', flat: true, 'no-caps': true},
      }).onOk(() => {
        this.productDelete(id)
      }).onCancel(() => {
        console.log('Cancel')
      }).onDismiss(() => {
      })
    },
    getMargin(product) {
      if(!product.buy_price || !product.price) {
        return 0
      } else {
        return product.price-product.buy_price
      }
    }
  },
  created() {
    if(!this.products.data.length) this.getAdminProducts()
  }
}
</script>
