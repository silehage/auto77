<template>
  <q-page class="q-pb-xl" :class="{ 'flex flex-center': !galleries.available }">
    <q-header>
      <q-toolbar>
        <q-btn :to="{ name: 'Settings' }" flat round dense icon="eva-arrow-back" />
        <q-toolbar-title>
          Photo Gallery
        </q-toolbar-title>
        <q-btn class="gt-xs" oucolor="grey-1" text-color="grey-9" tline icon="eva-plus-circle" label="Upload Gallery"
          no-caps @click="handleBtnUpload" />
      </q-toolbar>
    </q-header>
    <div class="q-pa-sm text-xs text-grey-8 bg-yellow-2 z-50"
      :class="galleries.data.length ? 'relative' : 'absolute-top'">
      <div>Note</div>
      <div>Untuk mempercepat loading aplikasi, kecilkan / kompress image sebelum upload</div>
    </div>
    <input type="file" class="hidden" ref="image" @change="handleFileUpload">
    <!-- <progress max="100" :value="uploadPercentage" class="full-width" style="height:20px;" v-show="uploadPercentage > 0"></progress> -->
    <template v-if="galleries.available">
      <div class="q-py-md">
        <q-list separator>
          <q-item v-for="gallery in galleries.data" :key="gallery.id">
            <q-item-section top avatar>
              <img :src="gallery.src" class="img-thumb" />
            </q-item-section>
            <q-item-section top>
              <div class="q-mb-xs">Urutan Tamplian</div>
              <div class="row q-gutter-sm items-center">
                <!-- <q-input style="max-width:4rem;" dense outlined type="number" :value="gallery.weight" @input="changeWeight($event,gallery.id)"></q-input> -->
                <q-btn icon="remove" size="xs" unelevated round @click="decre(gallery)" />
                <span>{{ gallery.weight }}</span>
                <q-btn icon="add" size="xs" round unelevated @click="incre(gallery)" />
              </div>
            </q-item-section>
            <q-item-section center side>
              <q-btn @click="remove(gallery.id)" size="sm" round icon="eva-trash-2" glossy color="red" />
            </q-item-section>
          </q-item>
        </q-list>
      </div>
    </template>
    <template v-else>
      <div>Tidak ada data</div>
    </template>
    <q-inner-loading :showing="!galleries.ready">
      <q-spinner-facebook size="50px" color="primary" />
    </q-inner-loading>
    <q-page-sticky class="lt-sm" position="bottom-right" :offset="[12, 12]">
      <q-btn fab icon="add" color="primary" glossy @click="handleBtnUpload" />
    </q-page-sticky>
  </q-page>
</template>

<script>
import { Api } from 'boot/axios'
import { mapState, mapActions } from 'vuex'
export default {
  data() {
    return {
      timeout: null,
      uploadPercentage: 0
    }
  },
  computed: {
    ...mapState({
      galleries: state => state.gallery.galleries,
      loading: state => state.loading
    })
  },
  mounted() {
    if (!this.galleries.data.length) this.getAll()
  },
  methods: {
    ...mapActions('gallery', ['removeItem', 'getAll', 'updateWeight']),

    changeWeight(weight, id) {

      this.updateWeight({ value: weight, id: id })

    },

    incre(gallery) {
      this.changeWeight(gallery.weight + 1, gallery.id)
    },
    decre(gallery) {
      if (gallery.weight <= 1) return
      this.changeWeight(gallery.weight - 1, gallery.id)
    },

    remove(id) {
      this.$q.dialog({
        title: 'Konfirmasi Penghapusan Item',
        message: 'Yakin akan menghapus data ini?',
        ok: { label: 'Hapus', flat: true, 'no-caps': true },
        cancel: { label: 'Batal', flat: true, 'no-caps': true },
      }).onOk(() => {
        this.removeItem(id)
      }).onCancel(() => {
        console.log('Cancel')
      }).onDismiss(() => {
      })
    },
    handleBtnUpload() {
      this.$refs.image.click()
    },
    handleFileUpload() {
      this.$store.commit('slider/SET_DATA_STATUS', false)
      let formData = new FormData();
      formData.append('image', this.$refs.image.files[0]);

      Api().post('gallery',
        formData,
        {
          headers: { 'Content-Type': 'multipart/form-data' },
          onUploadProgress: (progressEvent) => {
            this.uploadPercentage = parseInt(Math.round((progressEvent.loaded / progressEvent.total) * 100))
          }
        }).then(res => {
          this.getAll()
        })
        .catch(err => {
          console.log('FAILURE!!');
          this.$store.commit('slider/SET_DATA_STATUS', true)
        });
    },
  }
}
</script>

<style lang="css" scoped>
.absolute-top-right {
  top: 4px;
  right: 4px;
}
</style>>