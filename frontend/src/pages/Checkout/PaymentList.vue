<template>
  <div class="q-gutter-y-lg">
     <div v-if="isCod">
        <div class="q-pb-lg">
          <fieldset>
            <legend class="q-px-sm">Bayar Ditempat</legend>
            <div class="row q-gutter-sm payment-container">
              <div 
                class="box-shadow cursor-pointer payment-list cod" 
                :class="{'is-selected' : isSelectedCod}" @click="selectCodPayment">
                  <div class="text-h5 text-weight-bold">COD</div>
                  <div class="text-center name">Bayar Ditempat</div>
              </div>
            </div>
          </fieldset>
        </div>
      </div>
    <div v-if="payments.localbanks.length">
      <fieldset>
        <legend class="q-px-sm">Bank Transfer</legend>
        <div class="row q-gutter-sm payment-container">
          <div 
            class="box-shadow cursor-pointer payment-list bank_trf" 
            :class="{'is-selected' : isSelectedBank(item.id)}"
            v-for="(item, index) in payments.localbanks" 
            :key="index" @click="selectPaymentBank(item)">
            <div>
              <div class="text-center text-weight-bold text-md">{{ item.bank_name }}</div>
              <div class="text-weight-medium">Bank Transfer</div>
            </div>
          </div>
        </div>
        <div class="q-pa-sm text-caption">*Verifikasi manual</div>
      </fieldset>
    </div>
    <div v-if="virtualAccounts.length">
       <fieldset>
        <legend class="q-px-sm">Virtual Account</legend>
        <div class="row q-gutter-sm payment-container">
          <div 
            class="box-shadow cursor-pointer payment-list" 
            :class="{'is-selected' : isSelected(item.code)}"
            v-for="(item, index) in virtualAccounts" 
            :key="index" @click="selectPayment(item)">
               <div class="image">
                <img v-if="item.icon_url" :src="item.icon_url" />
                <img v-else src="/static/no-image.png" />
              </div>
              <div class="text-center name">{{ item.name }}</div>
            </div>
        </div>
      </fieldset>
    </div>
    <div v-if="convenienceStore.length">
      <fieldset>
        <legend class="q-px-sm">Convenion Store</legend>
         <div class="row q-gutter-sm payment-container">
          <div 
          class="box-shadow cursor-pointer payment-list" 
          :class="{'is-selected' : isSelected(item.code)}"
          v-for="(item, index) in convenienceStore" 
          :key="index" @click="selectPayment(item)">
             <div class="image">
                <img v-if="item.icon_url" :src="item.icon_url" />
                <img v-else src="/static/no-image.png" />
              </div>
            <div class="text-center name">{{ item.name }}</div>
          </div>
        </div>
      </fieldset>
    </div>
    <div v-if="ewalet.length">
      <fieldset>
        <legend class="q-px-sm">E-Walet</legend>
         <div class="row q-gutter-sm payment-container">
          <div 
            class="box-shadow cursor-pointer payment-list" 
            :class="{'is-selected' : isSelected(item.code)}"
            v-for="(item, index) in ewalet" 
            :key="index" @click="selectPayment(item)">
               <div class="image">
                <img v-if="item.icon_url" :src="item.icon_url" />
                <img v-else src="/static/no-image.png" />
              </div>
              <div class="text-center name">{{ item.name }}</div>
            </div>
         </div>
      </fieldset>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    payments: Object,
    paymentSelected: Object,
    isCod: Boolean
  },
  data() {
    return {
      selected: null
    }
  },
  computed: {
    virtualAccounts() {
      if(this.payments && this.payments.paymentGateway.length) {
        return this.payments.paymentGateway.filter(function(el) {
          return el.group == 'Virtual Account' && el.active == true
        })
      }
      return []
    },
    convenienceStore() {
      if(this.payments && this.payments.paymentGateway.length) {
        return this.payments.paymentGateway.filter(function(el) {
          return el.group == 'Convenience Store' && el.active == true
        })
      }
      return []
    },
    ewalet() {
      if(this.payments && this.payments.paymentGateway.length) {
        return this.payments.paymentGateway.filter(function(el) {
          return el.group == 'E-Wallet' && el.active == true
        })
      }
      return []
    },
    isSelectedCod() {
      if(this.paymentSelected) {
        if(this.paymentSelected.payment_type == 'COD') {
          return true
        } else {
          return false
        }
      }else {
        return false
      }
    },
  },
  methods: {
    isSelected(code) {
      if(this.paymentSelected) {
        if(this.paymentSelected.code == code) {
          return true
        } else {
          return false
        }
      }else {
        return false
      }
    },
    isSelectedBank(id) {
      if(this.paymentSelected) {
        if(this.paymentSelected.id == id) {
          return true
        } else {
          return false
        }
      }else {
        return false
      }
    },
    selectCodPayment() {
      let cod = {
        payment_type: 'COD',
        payment_method: 'COD',
        payment_name: 'Bayar Ditempat' 
      }
      this.$emit('onSelect', cod )
    },
    selectPayment(item) {
      this.$emit('onSelect', {...item, payment_type: 'GATEWAY'})
    },
    selectPaymentBank(item){
      this.$emit('onSelect', {...item, payment_type: 'DIRECT'})
    },
  }
}
</script>

<style>

</style>