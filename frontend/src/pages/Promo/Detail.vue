<template>
  <q-page>
    <q-header>
      <q-toolbar>
        <q-btn :to="{name: 'PromoIndex', query: { q: 'promo'}}"
          flat round dense
          icon="eva-arrow-back" />
        <q-toolbar-title v-if="promo">
         {{ promo.label }}
        </q-toolbar-title>
          <q-btn @click="handleAddProductPromo" unelevated color="accent">
            <q-icon name="add"></q-icon>
            <span>Produk</span>
          </q-btn> 
      </q-toolbar>
    </q-header>
    <div v-if="promo">
      <q-list separator>
        <q-item>
          <q-item-section>Label</q-item-section>
          <q-item-section side>{{ promo.label }}</q-item-section>
        </q-item>
        <q-item>
          <q-item-section>Start</q-item-section>
          <q-item-section side>{{ promo.start }}</q-item-section>
        </q-item>
        <q-item>
          <q-item-section>Selesai</q-item-section>
          <q-item-section side>{{ promo.start }}</q-item-section>
        </q-item>
        <q-item>
          <q-item-section>Status</q-item-section>
          <q-item-section side>
            <span class="q-px-md q-py-xs rounded-borders text-white" :class="promo.is_active ? 'bg-green' : 'bg-grey-7'">{{ promo.is_active ? 'Active' : 'Inactive' }}</span>
          </q-item-section>
        </q-item>
      </q-list>
      <q-separator></q-separator>
    </div>
     <div v-if="promo">
      <div class="row items-center justify-between q-pa-md" :class="$q.dark.isActive? 'bg-grey-7 text-white' : 'bg-grey-2'">
         <div class="text-md text-weight-bold">Total Produk {{ products.length }}</div>
      </div>
      <div class="">
        <q-list separator v-if="products.length">
          <q-item>
            <q-item-section side>#</q-item-section>
            <q-item-section>Nama Produk</q-item-section>
            <q-item-section>Diskon</q-item-section>
            <q-item-section side>Actions</q-item-section>
          </q-item>
          <q-item v-for="(product, index) in products" :key="index">
            <q-item-section side>{{ index+1 }}</q-item-section>
            <q-item-section>{{ product.title }}</q-item-section>
            <q-item-section>{{ product.pivot.discount_type == 'PERCENT' ? product.pivot.discount_amount + ' %' : moneyIDR(product.pivot.discount_amount) }}</q-item-section>
            <q-item-section side>
              <div class="q-gutter-sm flex">
                <q-btn 
                icon="eva-edit-2" 
                unelevated 
                round
                size="11px"
                color="blue" 
                :disabled="syncLoading"
                @click="handleEdit(product)"></q-btn>
                <q-btn 
                icon="eva-trash-2" 
                size="11px"
                round
                unelevated 
                color="red" 
                :disabled="syncLoading"
                @click="handleRemoveProductPromo(product.id)">
                </q-btn>
              </div>
            </q-item-section>
          </q-item>
        </q-list>
      </div>
    </div>
    <q-dialog v-model="searchModal" persistent position="bottom">
      <q-card class="max-width">
        <div class="sticky-top">
          <div class="flex justify-between items-center card-heading">
            <div class="">Produk Promo</div>
            <q-btn icon="close" v-close-popup round padding="0px" flat></q-btn>
          </div>
          <div class="q-px-md q-py-md box-shadow">
            <q-input outlined placeholder="Cari" dense v-model="search" :debounce="700" @input="findProduct" @keyup.enter="findProduct" type="search" clearable></q-input>
          </div>
        </div>
          <q-card-section class="q-gutter-y-sm" style="min-height:250px;overflow-y:auto;">
            <div v-if="productSearch.length">
              <q-list separator>
                <q-item v-for="product in productSearch" :key="product.id" clickable @click="syncProduct(product)">
                  <q-item-section side>
                    <q-btn 
                    icon="check_box" 
                    padding="0px"
                    unelevated 
                    size="12px"
                    ></q-btn>
                  </q-item-section>
                  <q-item-section>{{ product.title }}</q-item-section>
                </q-item>
              </q-list>
            </div>
            <q-inner-loading :showing="isLoading"></q-inner-loading>
            <div v-if="!isLoading && !productSearch.length" class="text-center q-pt-lg">Tidak ada data ditemukan</div>
          </q-card-section>
      </q-card>
    </q-dialog>
    <q-dialog v-model="productPromoModal" persistent>
      <q-card class="card-medium" v-if="productSelected ">
        <q-card-section>
          <div class="text-md text-weight-bold q-mb-md">Produk {{ productSelected.title }}</div>
          <q-form @submit.prevent="submitProductPromo">
              <div class="flex items-center q-mt-sm full-width q-gutter-x-sm">
                <q-input class="col" required filled square label="Diskon Nominal" v-model="form.discount_amount" :rules="[val => !!val || 'Field is required']"></q-input>
                <q-select required filled square 
                label="Diskon Tipe"
                class="col"
                v-model="form.discount_type" 
                :options="discountTypeOptions"
                emit-value
                map-options
                :rules="[val => !!val || 'Field is required']"
                ></q-select>
              </div>
              <div class="flex justify-end q-gutter-x-sm q-mt-md">
                <q-btn v-close-popup label="Close" outline color="primary"></q-btn>
                <q-btn type="submit" label="Submit" unelevated color="primary"></q-btn>
              </div>
            </q-form>
          </q-card-section>
        </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { Api } from 'boot/axios'
export default {
  name: 'ProductPromo',
  data() {
    return {
      productPromoModal: false,
      productSelected: null,
      product_ids: [],
      promo: null,
      isLoading: false,
      syncLoading: false,
      searchModal: false,
      search: '',
      productSearch: [],
      products: [],
      form: {
        promo_id: null,
        product_id: null,
        discount_amount: 0,
        discount_type: 'PERCENT'
      },
      discountTypeOptions: [
        { value: 'PERCENT', label: 'Persen'},
        { value: 'AMOUNT', label: 'Nominal'},
      ],
      removeId: null
    }
  },
  methods: {
    getPromoDetail() {
      Api().get('getPromoDetail/'+ this.$route.params.id).then(response => {
       if(response.status == 200) {
          this.products = response.data.results.products
          this.promo = response.data.results.promo
        }
      })
    },
    getProductPromo() {
      Api().get('getProductPromo/'+ this.$route.params.id).then(response => {
       if(response.status == 200) {
          this.products = response.data.results
        }
      })
      
    },
    findProduct() {
      if(!this.search) return
      this.isLoading = true
      this.productSearch = []
      Api().get('findProductWithoutPromo/' + this.search).then(response => {
        if(response.status == 200) {
          this.productSearch = response.data.results
        }
      })
      .finally(() => {
        this.isLoading = false
      })
    },
    handleEdit(product) {
      this.productSelected = product
      this.form.product_id =  product.id
      this.form.promo_id =  this.promo.id
      this.form.discount_amount = product.pivot.discount_amount
      this.form.discount_type = product.pivot.discount_type

      this.productPromoModal = true
    },
    handleAddProductPromo() {
      this.search = ''
      this.searchModal = true
      this.productSearch = []
      // this.findProduct()
    },
    syncProduct(item) {
      this.productSelected = item
      this.form.product_id =  item.id
      this.form.promo_id =  this.promo.id

      this.searchModal = false
      this.productPromoModal = true
    },
    submitProductPromo() {
      if(this.form.discount_type == 'PERCENT') {
        if(this.form.discount_amount == 0 || this.form.discount_amount >= 99) {
          this.$q.notify({
            type: 'negative',
            message: 'Tentukan nilai nominal diskon dengan benar'
          })

          return
        }
      }
      Api().post('submitProductPromo', this.form).then(() => {
        this.getProductPromo()
      }).finally(() => this.productPromoModal = false)
    },
    handleRemoveProductPromo(id) {
      this.removeId = id
      this.$q.dialog({
        title: 'Konnfirmasi',
        message: 'Yakin akan menghapus data?',
        cancel: true
      })
      .onOk(() => {
        this.removeProductPromo()
      })
    },
    removeProductPromo() {
      Api().post('removeProductPromo', { promo_id: this.promo.id, product_id: this.removeId })
      .then(() => this.getProductPromo())
    }
  },
  mounted() {
    this.getPromoDetail()
  }
}
</script>

<style>

</style>