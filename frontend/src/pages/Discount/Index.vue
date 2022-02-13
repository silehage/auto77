<template>
  <q-page padding>
    <q-header>
      <q-toolbar>
        <q-btn :to="{name: 'Settings'}"
          flat round dense
          icon="arrow_back" />
        <q-toolbar-title>
         Diskon / Kupon
        </q-toolbar-title>
      <q-btn class="gt-xs" flat label="Tambah Kategori" no-caps icon="add_circle" :to="{name: 'CategoryForm'}"/>
      </q-toolbar>
    </q-header>
    <q-tabs 
    v-model="tab"
    >
      <q-tab name="diskon" label="Diskon"></q-tab>
      <q-tab name="kupon" label="Kupon"></q-tab>
      <q-tab name="promo" label="Promo"></q-tab>
    </q-tabs>
    <q-separator />

    <q-tab-panels v-model="tab" animated>
      <q-tab-panel name="diskon">
        <div class="text-h6">Diskon</div>
        <discount />
      </q-tab-panel>

      <q-tab-panel name="kupon">
        <div class="text-h6">Alarms</div>
        Lorem ipsum dolor sit amet consectetur adipisicing elit.
      </q-tab-panel>

      <q-tab-panel name="promo">
        <div class="text-h6">Movies</div>
        Lorem ipsum dolor sit amet consectetur adipisicing elit.
      </q-tab-panel>
    </q-tab-panels>
  </q-page>
</template>

<script>
import Discount from './Discount.vue'
export default {
  name: 'DiscountIndex',
  components: {
    Discount
  },
  data() {
    return {
      tab: 'diskon'
    }
  }
}
</script>
