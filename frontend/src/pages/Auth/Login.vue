<template>
  <q-page class="flex flex-center relative" :class="{'bg-dark': $q.dark.isActive }">
    <q-header class="text-primary box-shadow" :class="{ 'bg-dark text-grey-1': $q.dark.isActive, 'bg-white': !$q.dark.isActive }">
       <q-toolbar class="header__padding">
         <q-btn :to="{name: 'Home'}"
            flat round dense
            icon="eva-arrow-back" />
          <q-toolbar-title class="text-weight-medium">Login</q-toolbar-title>
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
          <q-form @submit.prevent="submit" class="q-gutter-y-sm q-pa-md">
              <q-input
              v-model="form.email"
              color="grey-6"
              label="Email atau No Ponsel *"
              dense
              lazy-rules
              :rules="[ val => val && val.length > 0 || 'Wajib diisi']"
            >
            <template v-slot:prepend>
              <q-icon name="eva-person-outline" />
            </template>
              </q-input>
            
            <q-input 
            v-model="form.password" 
            label="Password Anda *"
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
          >Login</q-btn>

        </div>
          </q-form>
          <div class="text-center q-mt-sm">
          Belum punya akun <q-btn no-caps color="primary" padding="xs" flat :disabled="isLoading" label="Daftar Disini" :to="{ name: 'Register'}"></q-btn>
          </div>
          <div class="text-center q-mt-sm">
          <q-btn no-caps color="primary" padding="xs" flat :disabled="isLoading" label="Lupa password?" :to="{ name: 'ForgotPassword'}"></q-btn>
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
        password: '',
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
    }
  },
  methods: {
    ...mapActions('user', ['login']),
    submit() {
     this.login(this.form)
    }
  }
}
</script>
