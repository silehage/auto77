<template>
  <q-page>
     <q-header>
      <q-toolbar>
        <q-btn v-go-back.single
          flat round dense
          icon="eva-arrow-back" />
        <q-toolbar-title>
         List User
        </q-toolbar-title>
        <q-btn label="Tambah User" icon="add" outline @click="handleAddCs"></q-btn>
      </q-toolbar>
      </q-header>
      <div class="q-pa-md row justify-between items-center q-gutter-sm">
        <div class="col">
          <q-input square v-model="search" outlined dense @input="findUser" debounce="1000" placeholder="Ketik nama, email atau nomor telpon user">
          </q-input>
        </div>
        <div class="col-auto" style="width:60px">
          <q-input square v-model="take" outlined dense  label="Show" type="number" min="4"></q-input>
        </div>
        <div class="col-auto">
          <q-btn label="reset" unelevated color="primary" @click="reset"></q-btn>
        </div>
      </div>
      <div>
        <q-list separator>
          <q-item>
            <q-item-section side>#</q-item-section>
            <q-item-section>Info User</q-item-section>
            <q-item-section>Role</q-item-section>
            <q-item-section side>Aksi</q-item-section>
          </q-item>
          <q-item v-for="(user, i) in users" :key="i">
            <q-item-section side top>{{ i+1 }}</q-item-section>
            <q-item-section top>
              <div>{{ user.name }}</div>
              <div>{{ user.email }}</div>
              <div>{{ user.phone }}</div>
            </q-item-section>
            <q-item-section>{{ user.role }}</q-item-section>
            <q-item-section side class="q-gutter-sm">
              <q-btn size="sm" color="blue" round unelevated icon="eva-edit" @click="handleEditUser(user)">
                <q-tooltip>
                  Edit User
                </q-tooltip>
              </q-btn>
              <q-btn size="sm" color="red-6" round unelevated icon="eva-trash-2" @click="handleDeleteUser(user.id)">
                <q-tooltip>
                  Hapus User
                </q-tooltip>
              </q-btn>
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
            <div>{{ formMode }} User</div>
            <q-btn icon="eva-close" round padding="xs" v-close-popup flat></q-btn>
          </div>
          <q-card-section>
             <q-form @submit.prevent="submit" class="q-gutter-y-sm">
              <q-select :options="roles" v-model="form.role" label="Role" class="q-mb-md">
                 <template v-slot:prepend>
                    <q-icon name="eva-award-outline" />
                  </template>
              </q-select>
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
                  v-model="form.email"
                  type="email"
                  color="grey-6"
                  label="Email *"
                  dense
                  lazy-rules
                  :rules="[ val => val && val.length > 0 || 'Please type email']"
                >
                  <template v-slot:prepend>
                    <q-icon name="eva-email-outline" />
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
                
                <q-input 
                v-model="form.password" 
                label="Password *"
                color="grey-6"
                class="q-mb-md"
                :type="isPwd ? 'password' : 'text'"
                dense
                >
                <template v-slot:prepend>
                  <q-icon name="eva-lock-outline" />
                </template>
              <template v-slot:append>
                <q-icon
                  :name="isPwd ? 'eva-eye' : 'eva-eye-off-2'"
                  class="cursor-pointer"
                  @click="isPwd = !isPwd"
                />
              </template>
            </q-input>
            <q-input 
              v-model="form.password_confirmation" 
              label="Konfirmasi Password *"
              color="grey-6"
              class="q-mb-md"
              :type="isPwd ? 'password' : 'text'"
              dense
              >
              <template v-slot:prepend>
                <q-icon name="eva-lock-outline" />
              </template>
              <template v-slot:append>
                <q-icon
                  :name="isPwd ? 'eva-eye' : 'eva-eye-off-2'"
                  class="cursor-pointer"
                  @click="isPwd = !isPwd"
                />
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
      users: [],
      take: 10,
      canLoad: false,
      search: '',
      userNotAvailable: false,
      deleteId: null,
      load: false,
      isPwd: true,
      roles: ['admin', 'cs', 'customer'],
      form: {
        user_id: '',
        name: '',
        phone: '',
        email: '',
        password: '',
        password_confirmation: '',
        device_name: 'APP',
        role: 'cs'
      },
      formMode: 'Tambah',
      userModal: false
    }
  },
  mounted() {
    if(this.users.length < this.take) {
      this.getUsers()
    }
  },
  computed: {
    isDesktop() {
      return window.innerWidth > 600 ? true : false
    },
    loading() {
      return this.$store.state.loading
    }
  },
  methods: {
    submit() {
      if(this.checkInputPhone()) {
        this.$store.commit('SET_LOADING', true)
        if(this.formMode == 'Tambah') {
          if(!this.form.password || !this.form.password_confirmation) {
            this.$q.notify({
              type: 'negative',
              message: 'Password atau password konfirmasi wajib diisi'
            })
            return
          }
          this.$store.dispatch('user/addNewUser', this.form).then(() => {
            this.userModal = false
            this.getUsers()
          }).finally(() => this.$store.commit('SET_LOADING', false))
        }
        if(this.formMode == 'Edit') {
          this.$store.dispatch('user/update', this.form).then(() => {
            this.userModal = false
            this.getUsers()
          }).finally(() => this.$store.commit('SET_LOADING', false))
        }
      }
    },
    handleEditUser(user) {
      this.clearForm()
      this.formMode = 'Edit',
      this.form.user_id = user.id
      this.form.name = user.name
      this.form.phone = user.phone
      this.form.role = user.role
      this.form.email = user.email
      this.form.role = user.role
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
    getUsers() {
      this.$q.loading.show()
      Api().get(`userList?take=${this.take}`).then(response => {
        if(response.status == 200) {
          this.users = response.data.results
          this.canLoad = response.data.results.length == this.take ? true : false
          this.userNotAvailable = this.users.length ? false : true
        }
      })
      .finally(() => {
        this.$q.loading.hide()
      })
    },
    loadUser() {
      this.load = true
      Api().get(`userList?take=${this.take}&skip=${this.users.length}`).then(response => {
        if(response.status == 200) {
          this.users = [...this.users, ...response.data.results]
          this.canLoad = response.data.results.length == this.take ? true : false
          this.userNotAvailable = this.users.length ? false : true
        }
      })
      .finally(() => {
        this.load = false
      })
    },
    findUser() {
      if(this.search) {
        Api().get('findUser/' + this.search).then(response => {
          if(response.status == 200) {
            this.users = response.data.results
            this.userNotAvailable = this.users.length ? false : true
          }
        })
      }
    },
    reset() {
      this.users = []
      this.search = ''
      this.getUsers()
    },
    handleAddCs() {
      this.formMode = 'Tambah'
      this.clearForm()
      this.userModal = true
    },
    clearForm() {
      this.form.name = ''
      this.form.phone = ''
      this.form.email = ''
      this.form.password = ''
      this.form.password_confirmation = ''
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
        this.deleteUser(id)
      })
    },
    handleDisableVendor(id) {
      this.$q.dialog({
        title: 'Yakin akan NON aktifkan toko?',
        cancel: true
      }).onOk(() => {
        this.disableVendor(id)
      })
    },
    handleEnableVendor(id) {
      this.$q.dialog({
        title: 'Yakin akan mengaktifkan toko?',
        cancel: true
      }).onOk(() => {
        this.enableVendor(id)
      })
    },
    disableVendor(id) {
      Api().post('setVendorStatus', { id: id, status: 'disable'}).then(response => {
        if(response.status == 200) {
          this.reset()
        }
      })
    },
    enableVendor(id) {
      Api().post('setVendorStatus', { id: id, status: 'enable'}).then(response => {
        if(response.status == 200) {
          this.reset()
        }
      })
    },
    deleteUser(id) {
      this.$q.loading.show()
      Api().delete('user/'+ id).then(response => {
        if(response.status == 200) {
          this.reset()
        }
      })
      .finally(() => {
        this.$q.loading.hide()
      })
    },
  }
}
</script>