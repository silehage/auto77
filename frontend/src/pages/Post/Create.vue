<template>
  <q-page padding>
    <q-header>
      <q-toolbar>
        <q-btn v-go-back.single
          flat round dense
          icon="eva-arrow-back" />
        <q-toolbar-title>
         Tambah Post
        </q-toolbar-title>
        
      </q-toolbar>
    </q-header>
    <q-form @submit.prevent="submitPost">
      <q-input v-model="form.title" label="Title" :rules="[ val => val && val.length > 0 || 'Required']"></q-input>
      <q-input v-model="form.tags" label="Kategori"></q-input>
      <div class="row justify-between items-center q-py-sm q-my-xs border-b">
        <div>Tampil di Beranda</div>
        <div>
           <q-toggle v-model="form.is_promote" :label="form.is_promote? 'YES' : 'NO'" left-label></q-toggle>
        </div>
      </div>
      <div class="row justify-between items-center q-py-sm q-my-xs border-b">
        <div>Tampil di Halaman</div>
        <div>
           <q-toggle v-model="form.is_listing" :label="form.is_listing? 'YES' : 'NO'" left-label></q-toggle>
        </div>
      </div>
      <div class="q-mt-md">
        <div class="q-my-sm label-text">
          Konten
        </div>
        <q-editor v-model="form.body"></q-editor>
      </div>
     <q-footer class="bg-transparent q-pa-md">
        <q-btn :loading="loading" label="Simpan Data" type="submit" color="primary" class="full-width"></q-btn>
      </q-footer>
    </q-form>
    <div class="q-my-md flex justify-between">
      <q-btn label="Upload Image" size="sm" color="pink" icon="eva-upload" class="mt-2 mr-2" type="button" @click.prevent="selectNewImage" />
      <q-btn label="Upload Gallery" size="sm" color="blue" icon="eva-upload" class="mt-2 mr-2" type="button" @click.prevent="selectNewGallery" />
    </div>
    <div style="min-height:150px;">
      <div v-if="imagePreview">
        <div class="text-md">Image</div>
        <q-list>
          <q-item>
            <q-item-section>
              <img :src="imagePreview" class="shadow-4 q-pa-xs bg-white" style="width:100px;height:70px;object-fit:cover;"/>
            </q-item-section>
            <q-space />
            <q-item-section side>
                <q-btn @click="removeImage" size="sm" color="red" glossy round icon="eva-trash-2" />
            </q-item-section>
          </q-item>
      </q-list> 
      </div>
      <div v-if="galleries.length" class="q-mt-lg">
        <div class="text-md q-mb-sm">Gallery</div>
        <q-list>
          <q-item v-for="(img, idx) in galleries" :key="idx">
            <q-item-section>
              <img :src="img" class="shadow-4 q-pa-xs bg-white" style="width:100px;height:70px;object-fit:cover;"/>
            </q-item-section>
            <q-space />
            <q-item-section side>
                <q-btn @click="removeGallery(img)" size="sm" color="red" glossy round icon="eva-trash-2" />
            </q-item-section>
          </q-item>
        </q-list> 
      </div>
    </div>
    <input type="file" class="hidden" ref="image" @change="updateImagePreview" />
    <input multiple="multiple" type="file" class="hidden" ref="gallery" @change="updateGallery" />
  </q-page>
</template>

<script>
import { mapActions } from 'vuex'
export default {
  name: 'PostCreate',
  data() {
    return {
      form: {
        title: '',
        tags: '',
        body: '',
        image: '',
        is_listing: true,
        is_promote: true,
        gallery: []
      },
      imagePreview: '',
      galleries: []
    }
  },
  computed: {
    loading() {
      return this.$store.state.loading
    }
  },
  methods: {
    ...mapActions('post', ['addPost']),
    submitPost() {
      this.$store.commit('SET_LOADING', true)
      this.addPost(this.form)
    },
    updateImagePreview() {
     this.form.image = this.$refs.image.files[0]
     if(!this.form.image) return

        const reader = new FileReader();

        reader.onload = (e) => {
            this.imagePreview = e.target.result;
        };

        reader.readAsDataURL(this.$refs.image.files[0]);
    },
    updateGallery() {
      let files = this.$refs.gallery.files

      files.forEach(img => {

        const reader = new FileReader();
  
        reader.onload = (e) => {
            this.form.gallery.push(img);
            this.galleries.push(e.target.result);
        };
  
        reader.readAsDataURL(img);
      })

    },
    selectNewImage () {
      this.$refs.image.value = ''
      this.$refs.image.click()
    },
    selectNewGallery () {
      this.$refs.gallery.value = ''
      this.$refs.gallery.click()
    },
    removeImage() {
      this.imagePreview = ''
      this.form.image = ''
    },
    removeGallery(index) {
      this.galleries.splice(index, 1)
      this.form.gallery.splice(index, 1)
    }
  }
}
</script>
