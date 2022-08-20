<template>
  <div class="q-gutter-y-lg">
    <div v-if="payments.localbanks.length || isCod">
      <fieldset>
        <legend class="q-px-sm">{{ isCod && config.is_cod_payment ? 'Bank Transfer / Bayar Ditempat' : 'Bank Transfer' }}</legend>
        <div class="row q-gutter-sm payment-container">
          <div v-if="isCod && config.is_cod_payment"
            class="box-shadow1 cursor-pointer payment-list column justify-center" 
            :class="{'is-selected text-primary' : isSelectedCod}" @click="selectCodPayment">
              <div class="text-md text-weight-bold" >COD</div>
              <div class="text-center name">Bayar Ditempat</div>
          </div>
          <div 
            class="box-shadow1 cursor-pointer payment-list column justify-center" 
            :class="{'is-selected text-primary' : isSelectedBank(item.id)}"
            v-for="(item, index) in payments.localbanks" 
            :key="index" @click="selectPaymentBank(item)">
            <div>
              <div class="text-center text-weight-bold text-md">{{ item.bank_name }}</div>
              <div class="text-center text-weight-medium" style="font-size:12px;">Kcp {{ item.bank_office }}</div>
              <div class="text-center text-weight-medium" style="font-size:12px;">Bank Transfer</div>
            </div>
          </div>
        </div>
        <div class="q-px-sm text-caption">*Verifikasi manual</div>
      </fieldset>
    </div>
    <div v-if="virtualAccounts.length">
       <fieldset>
        <legend class="q-px-sm">Virtual Account</legend>
        <div class="row q-gutter-sm payment-container">
          <div v-for="(item, index) in virtualAccounts" :key="index">
            <div 
              class="box-shadow1 cursor-pointer payment-list" 
              :class="{'is-selected text-primary' : isSelected(item.code)}"
               @click="selectPayment(item)">
                <div class="image">
                  <img v-if="item.icon_url" :src="item.icon_url" />
                  <img v-else src="/static/no-image.png" />
                </div>
                <div class="text-center name">{{ item.name }}</div>
              </div>
            <div v-if="isFeeCustomer(item.fee_customer)" class="text-grey-7 text-xs q-pa-xs text-center">Fee {{ moneyIDR(calculateFee(item.fee_customer)) }}</div>
          </div>
        </div>
      </fieldset>
    </div>
    <div v-if="convenienceStore.length">
      <fieldset>
        <legend class="q-px-sm">Convenience Store</legend>
         <div class="row q-gutter-sm payment-container">
           <div v-for="(item, index) in convenienceStore" 
            :key="index">
            <div 
            class="box-shadow1 cursor-pointer payment-list" 
            :class="{'is-selected text-primary' : isSelected(item.code)}"
             @click="selectPayment(item)">
              <div class="image">
                  <img v-if="item.icon_url" :src="item.icon_url" />
                  <img v-else src="/static/no-image.png" />
                </div>
              <div class="text-center name">{{ item.name }}</div>
            </div>
            <div v-if="isFeeCustomer(item.fee_customer)" class="text-grey-7 text-xs q-pa-xs text-center">Fee {{ moneyIDR(calculateFee(item.fee_customer)) }}</div>
           </div>
        </div>
      </fieldset>
    </div>
    <div v-if="ewalet.length">
      <fieldset>
        <legend class="q-px-sm">E-Walet</legend>
         <div class="row q-gutter-sm payment-container">
           <div v-for="(item, index) in ewalet" :key="index">
            <div 
              class="box-shadow1 cursor-pointer payment-list" 
              :class="{'is-selected text-primary' : isSelected(item.code)}"
               @click="selectPayment(item)">
                <div class="image">
                  <img v-if="item.icon_url" :src="item.icon_url" />
                  <img v-else src="/static/no-image.png" />
                </div>
                <div class="text-center name">{{ item.name }}</div>
              </div>
              <div v-if="isFeeCustomer(item.fee_customer)" class="text-grey-7 text-xs q-pa-xs text-center">Fee {{ moneyIDR(calculateFee(item.fee_customer)) }}</div>
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
    config() {
      return this.$store.state.config
    },
    formOrder() {
      return this.$store.getters['order/getFormOrder']
    },
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
      if(this.formOrder.payment_type == 'COD') {
        return true
      } else {
        return false
      }
    }
  },
  methods: {
    isFeeCustomer(fee) {
      if(fee && fee.flat > 0 || fee.percent > 0) {
        return true
      }
      return false
    },

    calculateFee(fee) {
      let totalFee = parseInt(fee.flat);

      if(fee.percent > 0) {
        let feePercent = (parseInt(this.formOrder.total) * parseFloat(fee.percent))/100

        if(fee.flat > 0) {
          totalFee += feePercent
        } else {
          totalFee = feePercent
        }
         
      }

      return parseInt(Math.ceil(totalFee));
    },
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
    commitFormOrder(key, val) {
      this.$store.commit('order/SET_FORM_ORDER', { key: key, value: val})
    },
    selectCodPayment() {

      this.commitFormOrder('payment_name',  'Cash On Delivery')
      this.commitFormOrder('payment_code', '')
      this.commitFormOrder('payment_fee', 0)
      this.commitFormOrder('payment_method',  'COD')
      this.commitFormOrder('payment_type', 'COD')

      this.$emit('onSelectPayment', {
        payment_name: 'COD',
        payment_type: 'COD',
        payment_code: ''
      })
    },
    selectPayment(item) {
      
      this.commitFormOrder('payment_name',  item.name)
      this.commitFormOrder('payment_code', '')
      this.commitFormOrder('payment_method',  item.code)
      this.commitFormOrder('payment_type', 'PAYMENT_GATEWAY')
      this.commitFormOrder('payment_fee', this.calculateFee(item.fee_customer))

      this.$emit('onSelectPayment', item)
    },
    selectPaymentBank(item){

      let name =  item.bank_name + ' - ' + item.bank_office + ' a/n ' + item.account_name
      this.commitFormOrder('payment_name', name)
      this.commitFormOrder('payment_code', item.account_number)
      this.commitFormOrder('payment_method', 'BANK_TRANSFER')
      this.commitFormOrder('payment_type', 'BANK_TRANSFER')
      this.commitFormOrder('payment_fee', 0)

      this.$emit('onSelectPayment', item)
    },
  }
}
</script>
