<template>
  <q-page>
    <q-header>
      <q-toolbar>
        <q-btn v-go-back.single
          flat round dense
          icon="eva-arrow-back" />
        <q-toolbar-title>
         Pengaturan Akun
        </q-toolbar-title>
      </q-toolbar>
      </q-header>
      <div class="q-pa-md q-gutter-y-md">
        <q-input  v-model="form.name" label="Nama">
          <template v-slot:prepend>
              <q-icon name="eva-person-outline" />
          </template>
        </q-input>
        <q-input  v-model="form.phone" label="No Ponsel / Whatasapp">
          <template v-slot:prepend>
              <q-icon name="eva-phone-outline" />
          </template>
        </q-input>
        <q-input  type="email" v-model="form.email" label="Email">
          <template v-slot:prepend>
              <q-icon name="eva-email-outline" />
          </template>
        </q-input>
        <template v-if="changePassword">
        <q-input  
        :type="isPwd ? 'password' : 'text'" 
        placeholder="Password Baru"
        v-model="form.password">
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
        :type="isPwd ? 'password' : 'text'" 
        placeholder="Konfirmasi Password"
        v-model="form.password_confirmation">
          <template v-slot:prepend>
              <q-icon name="eva-lock-outline" />
          </template>
        </q-input>
        </template>
        <q-btn @click="btnChangePassword" class="q-mt-md" dense color="primary" no-caps flat :label="changePassword? 'Batal Ganti Password' : 'Ganti Password'"></q-btn>
      </div>
      <q-footer class="q-pa-sm bg-transparent">
        <q-btn :loading="loading" class="full-width" @click="submit" label="Simpan Data" color="primary">
           <q-tooltip class="bg-accent">Simpan Data</q-tooltip>
        </q-btn>
      </q-footer>
  </q-page>
</template>

<script>
import { mapState, mapActions} from 'vuex'
import { Api } from 'boot/axios'
export default {
  data () {
    return {
      isPwd: true,
      isPwd1: true,
      changePassword: false,
      form: {
        user_id: '',
        name:'',
        email:'',
        phone: '',
        password: '',
        password_confirmation: '',
        role: 'admin'
      }
    }
  },
  computed: {
    ...mapState({
      user: state => state.user.user,
      loading: state => state.loading
    })
  },
  created() {
    if(!this.user) {
      Api().get('user').then(response => {
        if(response.status == 200) {
          this.form.name = response.data.results.name
          this.form.email = response.data.results.email
          this.form.phone = response.data.results.phone
          this.form.role = response.data.results.role
          this.$store.commit('user/SET_USER', response.data.results)
        }
      })
    } else {
      this.form.user_id = this.user.id
      this.form.name = this.user.name
      this.form.email = this.user.email
      this.form.phone = this.user.phone
      this.form.role = this.user.role
    }
  },
  methods: {
    ...mapActions('user', ['getUser', 'updateUser']),
    submit() {
      this.$store.commit('SET_LOADING', true)
      this.updateUser(this.form)
    },
    btnChangePassword() {
      this.changePassword = !this.changePassword
      this.form.password_confirmation = ''
      this.form.password = ''
    }
  },

}
</script>

<style>

</style>