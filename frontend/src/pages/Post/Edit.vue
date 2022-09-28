<template>
  <q-page padding>
    <q-header>
      <q-toolbar>
        <q-btn v-go-back.single
          flat round dense
          icon="eva-arrow-back" />
        <q-toolbar-title>
         Edit Post
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
        <q-btn label="Update Data" type="submit" color="primary" class="full-width"></q-btn>
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
                <q-btn @click="removeGallery(img, idx)" size="sm" color="red" glossy round icon="eva-trash-2" />
            </q-item-section>
          </q-item>
        </q-list> 
      </div>
    </div>
    <input type="file" class="hidden" ref="image" @change="updateImagePreview" />
    <input multiple="multiple" type="file" class="hidden" ref="gallery" @change="updateGallery" />
    <q-inner-loading :showing="loading">
        <q-spinner-facebook size="50px" color="primary"/>
    </q-inner-loading>
  </q-page>
</template>

<script>
import { mapActions } from 'vuex'
export default {
  name: 'PostCreate',
  data() {
    return {
      post: null,
      loading: false,
      form: {
        id: '',
        title: '',
        tags: '',
        body: '',
        image: '',
        del_image: false,
        is_listing: false,
        is_promote: true,
        gallery: [],
        delete_gallery: []
      },
      imagePreview: '',
      galleries: []
    }
  },
  methods: {
    ...mapActions('post', ['updatePost', 'getSinglePost']),
    submitPost() {
      this.updatePost(this.form)
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
      this.form.del_image = true
    },
    removeGallery(src, index) {
      this.galleries.splice(index, 1)

      let asset = this.post.galleries.find(el => el.src == src) 

      if(asset != undefined) {
        this.form.delete_gallery.push(asset.id)
      }

    },
    getPostData() {
      this.loading = true
      this.getSinglePost(this.$route.params.id).then(response => {
        this.loading = false
        if(response.status == 200) {
          let data = response.data.results
          this.post = data

          this.form.id = data.id
          this.form.title = data.title
          this.form.tags = data.tags
          this.form.is_promote = data.is_promote
          this.form.is_listing = data.is_listing
          this.form.body = data.body
          this.imagePreview = data.image_url

          if(data.galleries.length) {
            this.galleries = data.galleries.map(el => el.src)
          }
        }
      }).catch(() => {
        this.loading = false
      })

    }
  },
  created() {
    this.getPostData()
  }
}
</script>
