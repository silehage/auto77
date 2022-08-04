<template>
  <div>
    <div class="q-mb-md" v-if="formOrder.payment_method == 'OVO'">
      <div class="bg-yellow-2 text-grey-7 q-pa-sm">
        <div class="text-weight-bold">Note:</div>
        <div>
          Anda memilih metode pembayaran OVO, <br>Pastikan nomor ponsel dan email yang anda inputkan sama dengan yang terdaftar di OVO milik anda.
        </div>
      </div>
    </div>
    <fieldset>
      <legend class="q-pa-sm">Info Pengiriman</legend>
        <table class="table dense">
          <tr>
            <th align="left">Penerima</th>
            <td>:</td>
            <td>{{ formOrder.customer_name  }}</td>
          </tr>
          <tr>
            <th align="left">Ponsel</th>
            <td>:</td>
            <td>{{ formOrder.customer_phone}}</td>
          </tr>
          <tr>
            <th align="left">Alamat</th>
            <td>:</td>
            <td><div v-html="formOrder.customer_address"></div></td>
          </tr>
           <tr>
            <th align="left">Kurir</th>
            <td>:</td>
            <td>{{ formOrder.shipping_courier_name }}</td>
          </tr>
           <tr v-if="formOrder.shipping_courier_service">
            <th align="left">Servis</th>
            <td>:</td>
            <td>{{ formOrder.shipping_courier_service }}</td>
          </tr>
        </table>
    </fieldset>
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
    <fieldset class="q-mt-lg">
      <legend class="q-pa-sm">Pembayaran</legend>
        <div class="row q-gutter-sm">
          <template v-if="formOrder.payment_type == 'COD'">
          <div class="box-shadow payment-list is-selected text-primary">
            <div class="text-weight-bold text-lg text-center">COD</div>   
            <div class="text-center text-sm q-pa-xs">Bayar Ditempat</div>   
          </div>
          </template>
          <template v-else>
            <div class="box-shadow payment-list is-selected">
               <div class="image" v-if="payment.icon_url">
                <img  :src="payment.icon_url" />
              </div>
              <div class="" v-if="formOrder.payment_type== 'BANK_TRANSFER'" style="margin:auto;">
                 <div>
                  <div class="text-center text-weight-bold text-md">{{ payment.bank_name }}</div>
                  <div class="text-center text-weight-medium" style="font-size:12px;">Kcp {{ payment.bank_office }}</div>
                  <div class="text-center text-weight-medium" style="font-size:12px;">Bank Transfer</div>
                </div>
              </div>
              <div v-else class="text-center name">
                {{ payment.name }}
              </div>
            </div>
          </template>
        </div>
    </fieldset>
  </div>
</template>

<script>
export default {
  name: 'ReviewOrder',
  props: {
    payment: Object,
    noPayment: Boolean
  },
  computed: {
    formOrder() {
      return this.$store.getters['order/getFormOrder']
    }
  },
  methods: {
    money(number) {
     return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR'}).format(number)
    },
    
  }
}
</script>
