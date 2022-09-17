<template>
  <q-page class="q-pb-xl">
    <q-header>
      <q-toolbar>
        <q-btn :to="{name: 'Settings'}"
          flat round dense
          icon="eva-arrow-back" />
        <q-toolbar-title>
         Pengaturan Toko
        </q-toolbar-title>
      
      </q-toolbar>
    </q-header>
    <div class="q-pa-md q-gutter-y-md">
      <div class="q-gutter-y-md">
        <q-input label="Nama Toko" v-model="form.name"></q-input>
        <q-input label="Nomor Whatsapp" v-model="form.phone" placeholder="0812*******"></q-input>
        <q-input label="Slogan" v-model="form.slogan"></q-input>
        <div class="q-my-xs text-red text-sm" v-if="errors.phone">Nomor Whatsapp harus berupa angka</div>
        <q-input autogrow label="Deskripsi Toko" v-model="form.description"></q-input>
        <q-input label="Google Play Url" v-model="form.google_play_url"></q-input>
        <q-input label="Facebook App ID" v-model="form.facebook_app_id"></q-input>
        <div class="q-my-md">
          <div for="" class="text-grey-8 q-mb-sm">Alamat Toko</div>
          <q-editor 
            v-model="form.address" 
            :toolbar="[
              ['bold', 'italic', 'underline']
            ]"
          ></q-editor>
        </div>
      </div>
     
      <div>
        <input type="file" class="hidden" ref="favicon" @change="updateFaviconPreview">
        <input type="file" class="hidden" ref="image" @change="updateImagePreview">
        <q-btn label="Upload Logo" size="sm" color="blue" icon="eva-upload" class="mt-2" type="button" @click.prevent="handleBtnUpload"></q-btn>
        <q-btn label="Upload Favicon" size="sm" color="pink" icon="eva-upload" class="mt-2 q-ml-md" type="button" @click.prevent="handleUploadFavicon"></q-btn>
       <div class="text-xs text-red q-my-md" v-if="errors.logo"> {{ errors.logo[0]}}</div>
      </div>
      <q-list v-if="faviconPreview">
       <q-item>
        <q-item-section top>
          <img :src="faviconPreview" class="shadow-4 q-pa-xs bg-grey" style="width:90px;height:90px;object-fit:contain;"/>
        </q-item-section>
        <q-space />
        <q-item-section side>
          <div class="text-grey-8 q-gutter-xs">
            <q-btn @click="removeFavicon" size="sm" round icon="eva-trash-2" glossy color="red"/>
          </div>
          </q-item-section>
        </q-item>
      </q-list>       
      
      <q-list v-if="imagePreview">
       <q-item>
        <q-item-section top>
          <img :src="imagePreview" class="shadow-4 q-pa-xs bg-grey" style="width:160px;;height:auto;"/>
        </q-item-section>
        <q-space />
        <q-item-section side>
          <div class="text-grey-8 q-gutter-xs">
            <q-btn @click="removeLogo" size="sm" round icon="eva-trash-2" glossy color="red"/>
          </div>
          </q-item-section>
        </q-item>
      </q-list>       
      
    </div>
    <q-footer class="q-pa-sm bg-transparent">
        <q-btn :loading="isLoading" class="full-width" @click="save" label="Simpan Data" color="primary">
           <q-tooltip class="bg-accent">Simpan Data</q-tooltip>
        </q-btn>
    </q-footer>
  </q-page>
</template>

<script>
import { Api } from 'boot/axios'
export default {
  data () {
    return {
      provinceOptions: [],
      subdistrictOptions: [],
      cityOptions: [],
      toko: null,
      isLoading: false,
      form: {
        name: '',
        phone: '',
        address: '',
        description: '',
        slogan: '',
        logo: '',
        favicon: '',
        is_remove_favicon: false,
        is_remove_logo: false,
        google_play_url: '',
        facebook_app_id: ''
      },
      imagePreview: '',
      faviconPreview: ''
    }
  },
  computed: {
    config() {
      return this.$store.state.config
    },
    dataToko() {
      return this.$store.state.shop
    },
    errors () {
      return this.$store.state.errors
    }
  },
  methods: {
    save() {
      this.isLoading = true;
      let self = this;
      let formData = new FormData();

      for( const i in this.form) {
        if(this.form[i] && this.form[i] != null && this.form[i] != 'null') {
          formData.append(i, this.form[i])
        }
      }
      
      Api().post('shop',formData,{headers: {'Content-Type': 'multipart/form-data' }}).then(response => {
        if(response.status == 200) {
          this.$store.commit('SET_SHOP', response.data.results)
          this.$q.notify({
            type: 'positive',
            message: 'Berhasil menyimpan data'
          })
          this.$router.push({ name: 'Settings'})
        }
      }).catch(err => {
        this.isLoading = false
         this.$q.notify({
           type: 'negative',
            message: 'Gagal menyimpan data, Coba refresh halaman'
          })
      })

    },
    handleBtnUpload() {
      this.$refs.image.click()
    },
    handleUploadFavicon() {
      this.$refs.favicon.click()
    },
    updateImagePreview() {
        this.form.logo = this.$refs.image.files[0]

        const reader = new FileReader();

        reader.onload = (e) => {
            this.imagePreview = e.target.result;
        };

        reader.readAsDataURL(this.$refs.image.files[0]);
    },
    updateFaviconPreview() {
        this.form.favicon = this.$refs.favicon.files[0]

        const reader = new FileReader();

        reader.onload = (e) => {
            this.faviconPreview = e.target.result;
        };

        reader.readAsDataURL(this.$refs.favicon.files[0]);
    },
    removeFavicon() {
      if(this.toko.favicon) {
        this.form.is_remove_favicon = true
      }
      this.form.favicon = ''
      this.faviconPreview = ''
    },
    removeLogo() {
      if(this.toko.logo_path) {
        this.form.is_remove_logo = true
      }
      this.form.logo = ''
      this.imagePreview = ''
    },
    setDataToko() {
      this.form.name = this.toko.name ? this.toko.name : ''
      this.form.phone = this.toko.phone ? this.toko.phone : ''
      this.form.slogan = this.toko.slogan ? this.toko.slogan : ''
      this.form.google_play_url = this.toko.google_play_url ? this.toko.google_play_url : ''
      this.form.facebook_app_id = this.toko.facebook_app_id ? this.toko.facebook_app_id : ''
      this.form.address = this.toko.address ? this.toko.address : ''
      this.form.description = this.toko.description ? this.toko.description : ''
      this.imagePreview = this.toko.logo_path ? this.toko.logo_url : ''
      this.faviconPreview = this.toko.favicon ? this.toko.favicon_url : ''
    },

    getShop() {
       Api().get('shop').then(response => {
        if(response.status == 200) {
          this.toko = response.data.results.shop
          this.setDataToko()
          this.$store.commit('SET_SHOP', response.data.results.shop)
          this.$store.commit('SET_CONFIG', response.data.results.config)
        }
      })
    }

  },
  mounted() {
    this.getShop()
  }
}
</script>

<style>

</style>