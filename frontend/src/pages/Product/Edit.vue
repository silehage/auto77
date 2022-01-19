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
      <!-- Start Product Variants -->
        <div id="variants" v-if="form.variants.length" class="q-gutter-y-md">
          <div class="text-lg text-weight-medium q-pt-lg">Produk Varian</div>
          <div v-for="(variation, varIndex) in form.variants" :key="varIndex" class="q-pa-md bg-grey-2">
            <div class="row items-center justify-between">
              <div class="text-lg text-weight-medium text-primary">{{ variation.variant_name }}</div>
              <div class="q-gutter-x-sm">
                <q-btn padding="3px 6px" dense icon="add" :label="'Varian ' +variation.variant_name" unelevated size="11px" color="primary" @click="handleAddVariantItem(varIndex)"></q-btn>
                <q-btn padding="3px 6px" dense icon="delete" unelevated size="11px" color="red-6" @click="handleRemoveVariation(varIndex)"></q-btn>
              </div>
            </div>
            <div v-for="(item, itemIndex) in variation.variant_items" :key="itemIndex" class="bg-white q-mt-md">
              <div class="row items-center justify-between q-pa-md">
                <div class="text-md text-weight-medium">{{ form.variants[varIndex].variant_items[itemIndex].variant_item_label }}</div>
                <div class="q-gutter-x-sm">
                <q-btn padding="3px 6px" icon="add" :label="'Varian ' +variation.variant_name + ' ' + form.variants[varIndex].variant_items[itemIndex].variant_item_label" unelevated size="11px" color="blue-6" @click="handleAddVariantValue(varIndex, itemIndex)"></q-btn>
                <q-btn padding="3px 6px" icon="delete" unelevated size="11px" color="red-6" @click="handleRemoveVariantItem(varIndex,itemIndex)"></q-btn>
                </div>
              </div>
              <q-list class="bg-grey-3 q-pa-sm">
                <!-- <div class="text-weight-medium q-pb-sm text-sm">Varian {{ item.variant_item_label }} Item</div> -->
                <q-item  v-for="(subItem, subItemIndex) in item.variant_item_values" :key="subItemIndex" class="q-pa-md bg-white q-mb-sm">
                  <q-item-section>
                    <q-input required v-model="form.variants[varIndex].variant_items[itemIndex].variant_item_values[subItemIndex].item_sku" label="Sku"></q-input>
                    <q-input required v-model="form.variants[varIndex].variant_items[itemIndex].variant_item_values[subItemIndex].additional_price" label="Additional Price" type="number" min="0"></q-input>
                  </q-item-section>
                  <q-item-section>
                    <q-input required v-model="form.variants[varIndex].variant_items[itemIndex].variant_item_values[subItemIndex].item_label" label="Label"></q-input>
                    <q-input required v-model="form.variants[varIndex].variant_items[itemIndex].variant_item_values[subItemIndex].item_stock" label="Stok" type="number" min="0"></q-input>
                  </q-item-section>
                  <q-item-section side>
                    <q-btn icon="delete" unelevated round color="red-6" padding="5px" size="11px" @click="handleRemoveVariantValue(varIndex, itemIndex, subItemIndex)"></q-btn>
                  </q-item-section>
                </q-item>
              </q-list>
            </div>
          </div>
        </div>
        <!-- End Product Variants -->
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
  name: 'ProductFormEdit',
  data () {
    return {
      requiredRules: [
        val => (val && val.length > 0) || 'Field harus diisi.'
      ],
      product: null,
      form: {
        id: '',
        title: '',
        price: '',
        weight: '',
        stock: '',
        category_id: '',
        description: '',
        variants: [],
        images: [],
        del_images: [],
        del_variant_ids: [],
        del_variant_item_ids: [],
        del_variant_value_ids: []
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
      this.form.variants = this.product.variants
      this.oldImages = this.product.assets
    },
    money(number) {
     return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR'}).format(number)
    },
  },
  mounted() {
    this.getProductById(this.$route.params.id).then((response) => {
      this.product = response.data.results
      this.setData() 
    })
    if(!this.categories.length) {
      this.getCategories()
    }

  },
}
</script>
