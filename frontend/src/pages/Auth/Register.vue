<template>
  <q-page class="flex flex-center relative" :class="{'bg-dark': $q.dark.isActive }">
    <q-header class="text-primary box-shadow" :class="{ 'bg-dark text-grey-1': $q.dark.isActive, 'bg-white': !$q.dark.isActive }">
       <q-toolbar class="header__padding">
         <q-btn :to="{name: 'Home'}"
            flat round dense
            icon="eva-arrow-back" />
          <q-toolbar-title class="text-weight-medium">Register</q-toolbar-title>
       </q-toolbar>
    </q-header>
    <div style="max-width: 450px;width:100%;" class="q-pa-sm">
      <div class="text-red q-pb-sm text-center" v-if="errors.email">{{ errors.email[0] }}</div>
      <q-card flat class="q-pt-sm q-mt-sm" >
        <q-card-section>
          <q-form @submit.prevent="submit" class="q-gutter-y-sm q-pa-md">
            <q-input
              v-model="form.name"
              color="grey-6"
              label="Nama Anda *"
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
              color="grey-6"
              label="Email Anda *"
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
              label="No Ponsel / Whatsapp *"
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
            :type="isPwd ? 'password' : 'text'"
            dense
            :rules="[ val => val && val.length > 0 || 'Please type password']"
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
          :type="isPwd ? 'password' : 'text'"
          dense
          :rules="[ val => val && val.length > 0 || 'Wajib diisi']"
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
        <div class="column">
          <q-btn :loading="isLoading" 
          type="submit" color="primary" padding="sm lg" 
          >Daftar Sekarang</q-btn>

        </div>
          </q-form>
          <div class="column text-center q-mt-sm">
          <div>
          Sudah punya akun <q-btn no-caps color="primary" padding="xs" flat :disabled="isLoading" label="Login Disini" :to="{ name: 'Login'}"></q-btn>
          </div>
        </div>
        </q-card-section>
      </q-card>
    </div>
  </q-page>
</template>

<script>
import { mapActions } from 'vuex'
export default {
  name: 'PageLogin',
  data () {
    return {
      isPwd: true,
      form: {
        email: '',
        phone: '',
        password: '',
        password_confirmation: '',
        device_name: 'APP'
      }
    }
  },
  computed: {
    errors() {
      return this.$store.state.errors
    },
    isLoading() {
      return this.$store.state.loading
    },
    shop() {
      return this.$store.state.shop
    },
  },
  methods: {
    ...mapActions('user', ['register']),
    submit() {
      if(this.checkInputPhone()) {
        this.register(this.form)
      }
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
  }
}
</script>
