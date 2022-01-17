<template>
  <q-page>
    <q-header>
      <q-toolbar>
        <q-btn v-go-back.single
          flat round dense
          icon="arrow_back" />
        <q-toolbar-title>
         Edit Produk
        </q-toolbar-title>
        
      </q-toolbar>
    </q-header>
    <q-form @submit.prevent="submit">
    <div class="q-pa-md">
      <div> 
         <q-input  
          type="text" 
          v-model="form.title" 
          label="Nama Produk"
          :rules="[val => val && val != '' || 'Nama produk harus diisi']"
        />
        <q-input  
          mask="#########" 
          v-model="form.price" 
          label="Harga"
          :rules="[val => val && val != '' || 'Harga Jual harus diisi']"
        />
        <q-input 
          mask="#####" 
          v-model="form.stock" 
          label="Stok"
          :rules="[val => val && val != '' || 'Stok harus diisi']"
        />
         <q-input 
           mask="#####" 
          v-model="form.weight" 
          label="Berat"
          suffix="gram"
          :rules="[val => val && val > 49 || 'Berat harus diisi min 50 gram']"
        />
        <div class="text-xs text-red" v-if="errors.stock"> {{ errors.weight[0]}}</div>

          <q-select
            v-model="form.category_id"
            :options="categories"
            label="Kategori"
            emit-value
            map-options
           class="q-pb-md"
            
          />
        <div class="q-mt-md">
          <label for="description" class="text-grey-7">Deskripsi</label>
          <q-editor v-model="form.description"
          />
          <div class="text-xs text-red" v-if="errors.description"> {{ errors.description[0]}}</div>
        </div>
        <section id="image-section" class="q-my-md q-gutter-y-sm">
          <div>
            <q-btn label="Upload Gambar Produk" size="sm" color="primary" icon="upload" class="mt-2 mr-2" type="button" @click.prevent="selectNewImage">
            </q-btn>
          <input type="file" class="hidden"
                              ref="image"
                              @change="updateImagePreview" multiple>

          </div>

          <q-list separator>
            <q-item  v-for="(image, index) in imagePreview" :key="index">

              <q-item-section>
                <img :src="image" class="shadow-4 q-pa-xs bg-white" style="width:100px;height:70px;object-fit:cover;"/>
              </q-item-section>

              <q-item-section side>
                  <q-btn @click.prevent="removeLocalImage(index)" size="sm" round icon="delete" glossy color="red"/>
              </q-item-section>
            </q-item>
            <q-item v-for="image in oldImages" :key="image.id">

              <q-item-section>
                <img :src="image.src" class="shadow-4 q-pa-xs bg-white" style="width:100px;height:70px;object-fit:cover;"/>
              </q-item-section>

              <q-item-section side>
                  <q-btn @click.prevent="removeImage(image)" size="sm" round icon="delete" glossy color="red"/>
              </q-item-section>
            </q-item>
          </q-list>
          <!-- <jet-input-error :message="form.errors.images" class="mt-2" /> -->
          </section>
      </div>
    </div>
    <q-footer>
       <q-btn type="submit" :loading="loading" class="full-width" label="Simpan Data">
           <q-tooltip class="bg-accent">Simpan Data</q-tooltip>
        </q-btn>
    </q-footer>
    </q-form>
  </q-page>
</template>

<script>
import { mapActions } from 'vuex'
export default {
  data () {
    return {
      requiredRules: [
        val => (val && val.length > 0) || 'Field harus diisi.'
      ],
      product: this.$store.getters['product/getProductById'](parseInt(this.$route.params.id)),
      form: {
        id: '',
        title: '',
        price: '',
        weight: '',
        stock: '',
        category_id: '',
        description: '',
        images: [],
        del_images: [],
      },
      imagePreview: [],
      oldImages: []
    }
  },
  computed: {
    errors: function() {
      return this.$store.state.errors
    },
    loading: function() {
      return this.$store.state.loading
    },
    categories() {
      return this.$store.getters['category/getValueOptions']
    },
  },
  methods: {
    ...mapActions('product', ['productUpdate', 'getProductById']),
    ...mapActions('category', ['getCategories']),
    submit() {
      let formData = new FormData();

      for(const x in this.form) {
        if(x != 'images' && x != 'del_images') {
          if(this.form[x]) {
            formData.append(x, this.form[x])
          }
        }
      }

      if(this.form.images.length > 0) {

        for( var i = 0; i < this.form.images.length; i++ ){
          let file = this.form.images[i];

          formData.append('images[' + i + ']', file);
        }
      }
      if(this.form.del_images.length > 0) {

        for( var i = 0; i < this.form.del_images.length; i++ ){
          let file = this.form.del_images[i];

          formData.append('del_images[' + i + ']', file);
        }
      }

      this.productUpdate(formData)
    },
    selectNewImage() {
        this.$refs.image.click();
    },

    updateImagePreview() {

      let img = this.$refs.image.files

      for(let i=0;i<img.length;i++){

        this.form.images.push(img[i])

        const reader = new FileReader();

        reader.onload = (e) => {
            this.imagePreview.push(e.target.result);
        };

        reader.readAsDataURL(img[i]);
        }
    },
    removeLocalImage(index) {

      this.imagePreview = this.imagePreview.filter(function(el,i) {
        return i !== index;
      })
      this.form.images = this.form.images.filter(function(el,i) {
        return i !== index;
      })
    },
    removeImage(img) {

      this.oldImages = this.oldImages.filter(function(el) {
        return el.id !== img.id;
      })
      this.form.del_images.push(img.filename)
    },
    setData() {
      this.form.id = this.product.id
      this.form.title = this.product.title
      this.form.price = this.product.price
      this.form.weight = this.product.weight
      this.form.stock = this.product.stock
      this.form.category_id = this.product.category_id
      this.form.description = this.product.description
      this.oldImages = this.product.assets
    },
    money(number) {
     return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR'}).format(number)
    },
  },
  mounted() {
    if(!this.product) {
      this.getProductById(this.$route.params.id).then((response) => {
        this.product = response.data.results
        this.setData() 
      })
    }else {
      this.setData() 
    }
    if(!this.categories.length) {
      this.getCategories()
    }

  },
}
</script>

<style>

</style>