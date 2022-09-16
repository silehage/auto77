<template>
<q-card flat>
  <q-card-section>
    <div class="text-subtitle1 text-weight-bold">Pengaturan Basic</div>
    <div class="text-caption">Pengaturan tampilan produk dan social proof</div>
  </q-card-section>
  <q-list>
    <q-item>
      <q-item-section>
        <q-item-label>
          Tampilan produk beranda
        </q-item-label>
      </q-item-section>
      <q-item-section side>
        <div class="q-gutter-x-sm">
          <q-btn @click="changeHomeViewMode('grid')" label="Grid Mode" size="sm" unelevated :color="form.home_view_mode == 'grid' ? 'green-7' : 'grey-6'" dense icon="grid_view">
            <q-tooltip>
              Grid Mode
            </q-tooltip>
          </q-btn>
          <q-btn @click="changeHomeViewMode('list')" label="List Mode" size="sm" unelevated :color="form.home_view_mode == 'list' ? 'green-7' : 'grey-6'" dense icon="eva-list">
            <q-tooltip>
              List Mode
            </q-tooltip>
          </q-btn>
        </div>
      </q-item-section>
    </q-item>
    <q-item>
      <q-item-section>
        <q-item-label>
         Tampilan produk katalog
        </q-item-label>
      </q-item-section>
      <q-item-section side>
        <div class="q-gutter-x-sm">
          <q-btn @click="changeProductViewMode('grid')" label="Grid Mode" size="sm" unelevated :color="form.product_view_mode == 'grid' ? 'green-7' : 'grey-6'" dense icon="grid_view">
            <q-tooltip>
              Grid Mode
            </q-tooltip>
          </q-btn>
          <q-btn @click="changeProductViewMode('list')" label="List Mode" size="sm" unelevated :color="form.product_view_mode == 'list' ? 'green-7' : 'grey-6'" dense icon="eva-list">
            <q-tooltip>
              List Mode
            </q-tooltip>
          </q-btn>
        </div>
      </q-item-section>
    </q-item>
    <q-item>
    <q-item-section>
      <q-item-label>Base Color</q-item-label>
    </q-item-section>
    <q-item-section side>
        <input ref="color" type="color" v-model="form.theme_color" style="width:110px;height:20px;"/>
    </q-item-section>
  </q-item>
  </q-list>
  <q-card-section class="flex justify-end">
    <q-btn unelevated size="12px" label="Simpan Pengaturan" color="blue-7" @click="saveTampilan"></q-btn>
  </q-card-section>
</q-card>
</template>

<script>
import { Api } from 'boot/axios'
export default {
  data () {
    return {
      form: {
        home_view_mode:'',
        product_view_mode: '',
        theme_color: ''
      }
    }
  },
  computed: {
    config: function() {
      return this.$store.state.config
    }
  },
  mounted() {
    this.form.product_view_mode = this.config.product_view_mode
    this.form.home_view_mode = this.config.home_view_mode
    this.form.theme_color = this.config.theme_color
  },
  methods: {
    changeHomeViewMode(str) {
      this.form.home_view_mode = str
    },
    changeProductViewMode(str) {
      this.form.product_view_mode = str
    },
    saveTampilan() {
      Api().post('config', this.form).then(response => {
        if(response.status == 200) {
          this.$store.dispatch('getAdminConfig')
        }
        this.$q.notify({
          type: 'positive',
          message: 'Berhasil memperbarui data'
        })

      }).catch(() => {
        this.$q.notify({
          type: 'negative',
          message: 'Gagal memperbarui data'
        })
      })
    }
  }
}
</script>
