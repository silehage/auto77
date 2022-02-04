<template>
  <q-page>
    <q-header>
      <q-toolbar>
        <q-btn v-go-back.single
          flat round dense
          icon="arrow_back" />
        <q-toolbar-title>
         Tambah Produk
        </q-toolbar-title>
        <q-btn flat icon="add" label="Produk Variasi" @click="handleAddVariant" v-if="!form.variants.length"></q-btn>
      </q-toolbar>
      </q-header>
      <q-form @submit.prevent="submit"> 
        <div class="q-pa-md q-gutter-y-sm">
            <q-input  
            filled 
            type="text" 
            v-model="form.title" 
            label="Nama Produk"
            :rules="requiredRules"
            ></q-input>
            <div class="text-xs text-red" v-if="errors.title"> {{ errors.title[0]}}</div>

            <div class="row items-center q-gutter-x-sm">
              <div class="col">
                <q-input  
                filled 
                mask="#########" 
                v-model="form.price" 
                label="Harga"
                :rules="requiredRules"
                ></q-input>
              </div>
              <div class="col">

                <q-input 
                  filled 
                  mask="#####" 
                  v-model="form.stock" 
                  label="Stok"
                  :rules="requiredRules"
                />
              </div>

              <div class="col">

              <q-input 
                filled 
                mask="#####" 
                v-model="form.weight" 
                label="Berat"
                suffix="gram"
                :rules="[val => val && val > 49 || 'Berat harus diisi min 50 gram']"
              />
              </div>


            </div>
              <q-select
              filled 
                v-model="form.category_id"
                :options="categories"
                label="Kategori"
                emit-value
                map-options
                class="q-pb-md"
              />
            <div class="q-mt-md q-mb-sm">
              <label for="description" class="text-grey-7 q-pb-sm block">Deskripsi</label>
              <q-editor 
              v-model="form.description"
              />
              <div class="text-xs text-red" v-if="errors.description"> {{ errors.description[0]}}</div>
            </div>
            <section id="image-section" class="q-my-lg q-gutter-y-sm">
              <div>
                <q-btn label="Upload Gambar Produk" size="sm" color="primary" icon="upload" class="mt-2 mr-2" type="button" @click.prevent="selectNewImage">
                </q-btn>
              </div>
                  
              <input type="file" class="hidden"
                                  ref="image"
                                  @change="updateImagePreview" multiple>
              <!-- <jet-input-error :message="form.errors.images" class="mt-2" /> -->

                  <q-list separator>
                  <q-item  v-for="(image, index) in imagePreview" :key="index">

                    <q-item-section>
                      <img :src="image" class="shadow-4 q-pa-xs bg-white" style="width:100px;height:70px;object-fit:cover;"/>
                    </q-item-section>

                    <q-item-section side>
                        <q-btn @click.prevent="removeImage(index)" size="sm" round icon="delete" glossy color="red"/>
                    </q-item-section>
                  </q-item>
                </q-list>
            </section>
          </div>
           <!-- Start Product Variants -->
      <div id="variants" v-if="form.variants.length">
        <div class="row items-center q-mt-xl q-pa-md q-gutter-x-md">
          <div class="text-md2 text-weight-medium">Produk Variasi</div>
          <q-btn padding="4px 8px" size="13px" unelevated @click="handleAddVariant" color="blue-6">
            <q-icon name="edit"></q-icon>
            <span>Edit</span>
          </q-btn>
        </div>
          <div v-for="(variant, varIndex) in form.variants" :key="varIndex" class="q-pa-md bg-grey-2" style="min-height:100px;">
            <div v-for="(item, itemIndex) in variant.variant_items" :key="itemIndex" class="q-mb-md">
              <div class="text-md text-weight-medium q-pb-xs">{{ variant.variant_name }} {{ item.variant_item_label }}</div>
              <q-list class="bg-grey-3 q-pa-sm">
                <q-item  v-for="(subItem, subItemIndex) in item.variant_item_values" :key="subItemIndex" class="q-pa-md bg-white q-mb-sm">
                  <q-item-section>
                    <q-input stack-label filled square required v-model="form.variants[varIndex].variant_items[itemIndex].variant_item_values[subItemIndex].item_label" dense :label="variant.variant_item_name"></q-input>
                  </q-item-section>
                  <q-item-section>
                    <q-input stack-label filled square required v-model="form.variants[varIndex].variant_items[itemIndex].variant_item_values[subItemIndex].additional_price" dense label="Additional Price" type="number" min="0"></q-input>

                  </q-item-section>
                  <q-item-section>
                    <q-input stack-label filled square required v-model="form.variants[varIndex].variant_items[itemIndex].variant_item_values[subItemIndex].item_stock" dense label="Stok" type="number" min="0"></q-input>

                  </q-item-section>
                </q-item>
              </q-list>
            </div>
          </div>
      </div>
      <!-- End Product Variants -->
        <!-- <textarea v-model="text" style="white-space:pre-wrap"/> -->
        <q-footer>
          <q-btn type="submit" :loading="loading" class="full-width" label="Simpan Data">
              <q-tooltip class="bg-accent">Simpan Data</q-tooltip>
            </q-btn>
        </q-footer>
      </q-form>
      <q-dialog 
      v-model="variantModalForm"
      persistent
      maximized
      seamless
      transition-show="slide-up"
      transition-hide="slide-down"
      >
        <product-variant-form @onSubmitForm="onSubmitForm" @onClose="variantModalForm = false" :props="form.variants"/>
      </q-dialog>
  </q-page>
</template>

<script>
import { mapActions } from 'vuex'
import ProductVariantForm from './ProductVariantForm.vue'
export default {
  name: 'ProductFormCreate',
  components: { ProductVariantForm },
  data () {
    return {
      requiredRules: [
        val => (val && val.length > 0) || 'Field harus diisi.'
      ],
      form: {
        title: '',
        price: '',
        weight: '',
        stock: '',
        description: '',
        category_id:'',
        variants: [],
        images: []
      },
      imagePreview: [],
      variantModalForm: false

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
    ...mapActions('product', ['productStore']),
    ...mapActions('category', ['getCategories']),
    onSubmitForm(data) {
      this.form.variants = data
      this.variantModalForm = false
    },
    handleAddVariant() {
      this.variantModalForm = true
    },

    // Submit Product
    submit() {

      let formData = new FormData();

      formData.append('title', this.form.title)
      formData.append('price', this.form.price)
      formData.append('weight', this.form.weight)
      formData.append('stock', this.form.stock)
      formData.append('description', this.form.description)

      if(this.form.category_id) {
        formData.append('category_id', this.form.category_id)
      }
      if(this.form.variants.length) {
        formData.append('variants', JSON.stringify(this.form.variants))
      }


      for( var i = 0; i < this.form.images.length; i++ ){
        let file = this.form.images[i];

        formData.append('images[' + i + ']', file);
      }

      this.productStore(formData)
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
    removeImage(index) {

      this.imagePreview = this.imagePreview.filter(function(el,i) {
        return i !== index;
      })
      this.form.images = this.form.images.filter(function(el,i) {
        return i !== index;
      })
    },
    money(number) {
     return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR'}).format(number)
    },
 
  },
  mounted() {
    if(!this.categories.length) {
      this.getCategories()
    }
  }
}
</script>

<style>

</style>