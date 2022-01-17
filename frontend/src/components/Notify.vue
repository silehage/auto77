<template>
  <div class="notif-content" :class="{'show' : isShowen}" v-if="orderItem">
    <q-list>
      <q-item>
        <q-item-section avatar>
           <q-avatar>
            <img :src="orderItem.images[0].src" />
          </q-avatar>
        </q-item-section>
        <q-item-section>
          <q-item-label caption>{{ orderItem.order.customer_name }} telah membeli</q-item-label>
          <q-item-label class="ellipsis-2-lines">{{ orderItem.name }} </q-item-label>
            <div class="text-green-7 q-pa-xs text-xs">{{ orderItem.created }}</div>
        </q-item-section>
        <q-item-section side top>
          <q-btn icon="close" size="10px" color="primary" flat round @click="closeNotify"></q-btn>
        </q-item-section>
      </q-item>
    </q-list>
  </div>
</template>

<script>
import { Api } from 'boot/axios'
export default {
  data() {
    return {
      orders: [],
      isShowen: false,
      orderItem: null,
      currentOrderIndex: 0
    }
  },
  watch: {
    isShowen: function(val) {
      if(val) {
         setTimeout(() => {
          this.isShowen = false
        }, this.notifyTimeOut)
      }
    }
  },
  computed: {
    config() {
      return this.$store.state.config
    },
    notifyInterval() {
      if(this.config){
        return this.config.notifypro_interval*1000
      } else {
        return 15*1000
      }
    },
    notifyTimeOut() {
      if(this.config){
        return this.config.notifypro_timeout*1000
      } else {
        return 4*1000
      }
    },
  },
  methods: {
    showNotif() {
      setInterval(() => {
        this.isShowen = true
         if(this.currentOrderIndex < (this.orders.length -1)) {
           this.currentOrderIndex++
          } else {
            this.currentOrderIndex = 0
          } 
          this.setCurrentOrder()
      }, this.notifyInterval)
    },
    setCurrentOrder() {
      this.orderItem = this.orders.find((e,i) => i == this.currentOrderIndex)
    },
    closeNotify() {
      this.isShowen = false
    }
  },
  
  mounted() {
      Api().get('getRandomOrder').then(res => {
      if(res.status == 200) {
        this.orders = res.data.results
        if(this.orders.length){
          this.setCurrentOrder()
          setTimeout(() => {
           this.showNotif()
        }, this.notifyInterval)
        }
      }
    })
   
  }
}
</script>

<style>

</style>