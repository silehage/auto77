<template>
  <q-page>
     <q-header>
      <q-toolbar>
        <q-btn v-go-back.single
          flat round dense
          icon="eva-arrow-back" />
        <q-toolbar-title>
         List Customer Service
        </q-toolbar-title>
        <q-btn label="Tambah CS" icon="add" outline @click="handleAddCs"></q-btn>
      </q-toolbar>
      </q-header>
      <div>
        <q-list separator>
          <q-item>
            <q-item-section side>#</q-item-section>
            <q-item-section>Nama Cs</q-item-section>
            <q-item-section>Ponsel</q-item-section>
            <q-item-section side>Aksi</q-item-section>
          </q-item>
          <q-item v-for="(user, i) in customer_services" :key="i">
            <q-item-section side top>{{ i+1 }}</q-item-section>
            <q-item-section top>
              <div>{{ user.name }}</div>
            </q-item-section>
            <q-item-section top>
              <div>{{ user.phone }}</div>
            </q-item-section>
            <q-item-section side >
              <div class="row q-gutter-sm">
                <q-btn size="sm" color="red-6" round unelevated icon="eva-trash-2" @click="handleDeleteUser(user.id)">
                  <q-tooltip>
                    Hapus User
                  </q-tooltip>
                </q-btn>
                <q-btn size="sm" color="blue" round unelevated icon="eva-edit" @click="handleEditUser(user)">
                  <q-tooltip>
                    Edit User
                  </q-tooltip>
                </q-btn>
              </div>
            </q-item-section>
          </q-item>
          <q-item v-if="userNotAvailable">
            <q-item-section class="text-center q-py-md">
              Tidak ada data
            </q-item-section> 
          </q-item>
        </q-list>
        <div class="q-py-md flex justify-center">
          <q-btn :loading="load" v-if="canLoad" label="Loadmore" @click="loadUser" color="primary" outline></q-btn>
        </div>
      </div>
      <q-dialog v-model="userModal">
        <q-card class="card-lg">
          <div class="card-heading">
            <div>{{ formMode }} CS</div>
            <q-btn icon="eva-close" round padding="xs" v-close-popup flat></q-btn>
          </div>
          <q-card-section>
             <q-form @submit.prevent="submit" class="q-gutter-y-sm">
                <q-input
                  v-model="form.name"
                  color="grey-6"
                  label="Nama *"
                  dense
                  lazy-rules
                  :rules="[ val => val && val.length > 0 || 'Wajib diisi']"
                  >
                  <template v-slot:prepend>
                    <q-icon name="eva-person-outline" />
                  </template>
                </q-input>
                
                <q-input
                  v-model="form.phone"
                  color="grey-6"
                  label="No Whatsapp *"
                  dense
                  lazy-rules
                  :rules="[ val => val && val.length > 0 || 'Wajib diisi']"
                  >
                  <template v-slot:prepend>
                    <q-icon name="eva-phone-outline" />
                  </template>
                </q-input>
            <div class="column q-pt-md">
              <q-btn :loading="loading" 
              type="submit" color="primary" padding="sm lg" 
              >Submit</q-btn>

            </div>
              </q-form>
          </q-card-section>
        </q-card>
      </q-dialog>
  </q-page>
</template>

<script>
import { Api } from 'boot/axios'
export default {
  name: 'UserList',
  data() {
    return {
      take: 10,
      canLoad: false,
      search: '',
      userNotAvailable: false,
      deleteId: null,
      load: false,
      isPwd: true,
      form: {
        _method: 'POST',
        id: '',
        name: '',
        phone: '',
      },
      formMode: 'Tambah',
      userModal: false
    }
  },
  mounted() {
    this.getCs()
  },
  computed: {
    isDesktop() {
      return window.innerWidth > 600 ? true : false
    },
    loading() {
      return this.$store.state.loading
    },
    customer_services() {
      return this.$store.state.customer_services
    }
  },
  methods: {
    submit() {
      if(this.checkInputPhone()) {
        this.$store.commit('SET_LOADING', true)
        if(this.formMode == 'Tambah') {
          this.form._method = 'POST'
        Api().post('customer_service', this.form).then(() => {
            this.userModal = false
            this.getCs()
          }).finally(() => this.$store.commit('SET_LOADING', false))
        }
        if(this.formMode == 'Edit') {
           this.form._method = 'PUT'
          Api().put('customer_service/' + this.form.id, this.form).then(() => {
            this.userModal = false
            this.getCs()
          }).finally(() => this.$store.commit('SET_LOADING', false))
        }
      }
    },
    handleEditUser(cs) {
      this.clearForm()
      this.formMode = 'Edit',
      this.form.id = cs.id
      this.form.name = cs.name
      this.form.phone = cs.phone
      this.userModal = true
    },
    checkInputPhone() {
      this.form.phone = this.form.phone.replace(/\D/g,'')
      if(!this.checkPhoneNumber(this.form.phone)) {
        this.$q.notify({
          message: 'Silahkan masukkan nomor whatsapp yang benar.',
          type: 'negative'
        })
        this.form.phone = ''

        return false
      }
      return true
    },
    checkPhoneNumber(formatted) {

      if(formatted.length > 9 ) {
        
        if(formatted.startsWith('08') || formatted.startsWith('628')) {
          return true
        } 
      }
    },
    getCs() {
      this.$q.loading.show()
      Api().get('customer_service').then(response => {
        if(response.status == 200) {
          this.$store.commit('SET_CS', response.data.results)
          this.userNotAvailable = response.data.results.length ? false : true
        }
      })
      .finally(() => {
        this.$q.loading.hide()
      })
    },
    handleAddCs() {
      this.formMode = 'Tambah'
      this.clearForm()
      this.userModal = true
    },
    clearForm() {
      this.form.name = ''
      this.form.phone = ''
    },
    handleDeleteUser(id) {
      if(id == 1) {
        this.$q.notify({
          type: 'negative',
          message: 'User admin tidak bisa di hapus'
        })
        return
      }
      this.$q.dialog({
        title: 'Yakin akan menghapus user?',
        message: 'Data yang di hapus tidak dapat dikembalikan',
        cancel: true
      }).onOk(() => {
        this.deleteCs(id)
      })
    },
    deleteCs(id) {
      Api.delete('customer_service/'+ id).then(() => {
        this.getCs()
      })
    }

  }
}
</script>