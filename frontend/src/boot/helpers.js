import Vue from 'vue'

Vue.mixin({
  methods: {
    moneyIDR(numb) {
      return 'Rp '+ numb.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }    
  }
})