<template>
  <div>
    <q-input :value="pricing" filled :label="label" @input="updateData" :prefix="prefix" :suffix="suffix"></q-input>
  </div>
</template>

<script>
export default {
  props: {
    label: {
      type: String,
      default: 'Price'
    },
    value: [String, Number],
    prefix: String,
    suffix: String,
  },
  computed: {
    pricing() {
      return this.money(this.value)

    }
  },
  mounted() {
    if(!this.value) {
       this.$emit('input', 0)
    }
  },
  methods: {
    updateData(val) {
      if(val){
        this.$emit('input', this.money(this.cleaning(val)))
      }else {
        this.$emit('input', 0)
      }
    },
    money(numb) {
      return numb.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    },
    cleaning(val) {
      return val.replace(/\D|^0+/g, '')
    }
  }
}
</script>