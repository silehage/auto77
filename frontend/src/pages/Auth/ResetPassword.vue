<template>
   <q-page class="flex flex-center relative" :class="{'bg-dark': $q.dark.isActive }">
    <q-header class="text-primary box-shadow" :class="{ 'bg-dark text-grey-1': $q.dark.isActive, 'bg-white': !$q.dark.isActive }">
       <q-toolbar class="header__padding">
        <q-btn :to="{ name: 'ForgotPassword'}"
          flat round dense
          icon="eva-arrow-back" />
        <q-toolbar-title class="text-weight-medium">
          Reset Password
        </q-toolbar-title>
      </q-toolbar>
    </q-header>
    <div style="max-width: 420px;width:100%;" class="q-pa-sm">
      <div class="flex justify-center">
        <img v-if="shop && shop.logo_path" :src="shop.logo_url" style="width:auto;height:95px;object-fit:contain;max-width:250px;" />
        <img v-else src="/icon/icon-192x192.png" style="width:auto;height:75px;object-fit:contain;max-width:150px;" />
      </div>
      <div class="text-red q-pb-sm text-center" v-if="errors.email">{{ errors.email[0] }}</div>
      <q-card flat class="q-pt-sm q-mt-sm" >
        <q-card-section>
          <div class="text-grey-7 q-pa-sm" v-if="isValidToken">Silahkan ganti kata sandi anda, gunakan kata sandi yang tidak mudah di tebak, tapi mudah di ingat.</div>
          <div class="text-grey-7 q-pa-sm" v-else>Kode token sudah kami kirim ke alamat email 
            <span class="text-weight-bold">{{ hideEmail }}</span>, 
            Silahkan buka email anda dan masukan token kedalam input yang tersedia.</div>
          <q-form @submit.prevent="submit" class="q-gutter-y-sm q-pa-sm">
             
            <template v-if="isValidToken">
              <q-input 
                  v-model="form.password" 
                  label="Kata Sandi *"
                  color="grey-6"
                  :type="isPwd ? 'password' : 'text'"
                  dense
                  >
                  <template v-slot:prepend>
                    <q-icon name="eva-lock-outline" />
                  </template>
                  <template v-slot:append>
                    <q-icon
                      :name="isPwd ? 'visibility_off' : 'visibility'"
                      class="cursor-pointer"
                      @click="isPwd = !isPwd"
                    />
                  </template>
              </q-input>
              <q-input 
                v-model="form.password_confirmation" 
                label="Konfirmasi Kata Sandi *"
                color="grey-6"
                :type="isPwd ? 'password' : 'text'"
                dense
                >
                <template v-slot:prepend>
                  <q-icon name="eva-lock-outline" />
                </template>
                <template v-slot:append>
                  <q-icon
                    :name="isPwd ? 'visibility_off' : 'visibility'"
                    class="cursor-pointer"
                    @click="isPwd = !isPwd"
                  />
                </template>
              </q-input>
            </template>
            <template v-else>
              <q-input 
                v-model="form.token" 
                label="Token *"
                color="grey-6"
                dense
                :rules="[ val => val && val.length > 0 || 'Silahkan masukan token']"
                >
                </q-input>
            </template>
        <div class="column q-pt-sm">
          <q-btn :loading="isLoading" 
          type="submit" color="primary" padding="sm lg"
          >{{ isValidToken? 'Update Password' : 'Kirim Token'}}</q-btn>

        </div>
        <div class="q-py-md text-center">
          <q-btn :disabled="isLoading" no-caps flat color="primary" :to="{name: 'Login'}">Kembali ke halaman login</q-btn>
        </div>
          </q-form>
        </q-card-section>
      </q-card>
    </div>
  </q-page>
</template>

<script>
import { Notify } from 'quasar'
import { mapActions } from 'vuex'
import { Api } from 'boot/axios'
export default {
  name: 'pageResetPassword',
  data () {
    return {
      isPwd: true,
      isValidToken: false,
      theEmail: 'anda',
      form: {
        email: '',
        token: '',
        password: '',
        password_confirmation: ''
      }
    }
  },
  computed: {
    errors() {
      return this.$store.state.errors
    },
    hideEmail() {
      return this.$store.state.forgot_password.hide_email
    },
    isLoading() {
      return this.$store.state.loading
    },
    shop() {
      return this.$store.state.shop
    }
  },
  mounted() {
    if(!localStorage.getItem('is_reqpwd')) {
      this.$router.push({name: 'ForgotPassword'})
    }
  },
  methods: {
    ...mapActions('user', ['resetPassword']),
    submit() {
      if(this.isValidToken) {
        if(!this.form.password || !this.form.password_confirmation) return
        this.resetPassword(this.form)
      } else {
        this.$store.commit('SET_LOADING', true)
        Api().get('validateToken/' +this.form.token)
        .then(response => {
          if(response.status == 200) {
            if(response.data.success) {
              this.isValidToken = true,
              this.form.token = response.data.data.token
              this.form.email = response.data.data.email
            }  else {
              Notify.create({
                type: 'negative',
                message: response.data.message
              })
            }
          }
        }).finally(() => {
          this.$store.commit('SET_LOADING', false)
        })
      }
    }
  }
}
</script>
