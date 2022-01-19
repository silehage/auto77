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
        <q-btn flat icon="add" label="Produk Varian" @click="handleAddVariant"></q-btn>
      </q-toolbar>
      </q-header>
      <q-form @submit.prevent="submit"> 
        <div class="q-pa-md">
            <q-input  
            type="text" 
            v-model="form.title" 
            label="Nama Produk"
            :rules="requiredRules"
            ></q-input>
            <div class="text-xs text-red" v-if="errors.title"> {{ errors.title[0]}}</div>
            <q-input  
            mask="#########" 
            v-model="form.price" 
            label="Harga"
            :rules="requiredRules"
            ></q-input>
            <div 
            class="text-xs text-red"
            v-if="errors.price"> {{ errors.price[0]}}</div>

            <q-input 
              mask="#####" 
              v-model="form.stock" 
              label="Stok"
              :rules="requiredRules"
            />
            <div class="text-xs text-red" v-if="errors.stock"> {{ errors.stock[0]}}</div>
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
        <!-- <textarea v-model="text" style="white-space:pre-wrap"/> -->
        <q-footer>
          <q-btn type="submit" :loading="loading" class="full-width" label="Simpan Data">
              <q-tooltip class="bg-accent">Simpan Data</q-tooltip>
            </q-btn>
        </q-footer>
      </q-form>
      <q-dialog v-model="variationModal">
        <q-card class="card-medium">
          <q-form @submit.prevent="addVariation">
            <div class="card-heading">Tambah Produk Varian</div>
            <q-card-section>
              <q-input required label="Nama Produk Varian" v-model="tempVariant" stack-label placeholder="contoh: Warna, Ukuran dll"></q-input>
              <div class="row justify-end q-mt-md q-gutter-x-sm">
                <q-btn type="button" label="Batal" unelevated color="primary" outline v-close-popup></q-btn>
                <q-btn type="submit" label="Tambah" unelevated color="primary"></q-btn>
              </div>
            </q-card-section>
          </q-form>
        </q-card>
      </q-dialog>
      <q-dialog v-model="variantItemModal">
        <q-card class="card-medium">
          <q-form @submit.prevent="addVariantItem">
            <div class="card-heading">Tambah varian {{ variantItemSelectedLabel }}</div>
            <q-card-section>
              <q-input label="Nama Varian" required v-model="tempVarianItem"></q-input>
              <div class="row justify-end q-mt-md q-gutter-x-sm">
                <q-btn type="button" label="Batal" unelevated color="primary" outline v-close-popup></q-btn>
                <q-btn type="submit" label="Tambah" unelevated color="primary"></q-btn>
              </div>
            </q-card-section>
          </q-form>
        </q-card>
      </q-dialog>
  </q-page>
</template>

<script>
import { mapActions } from 'vuex'
export default {
  name: 'ProductFormCreate',
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
        variants: [
          {
            variant_name: 'Warna',
            variant_items: [
              { 
                variant_item_label: 'Biru', 
                variant_item_values: [
                  {item_sku: '123', item_label: 'XL', additional_price: 3000, item_stock: 0},
                  {item_sku: '456', item_label: 'SM', additional_price: 5000, item_stock: 0},
                ]
              },
              { 
                variant_item_label: 'Merah', 
                variant_item_values: [
                  {item_sku: 'DEF', item_label: 'XL', additional_price: 0, item_stock: 0},
                  {item_sku: 'THG', item_label: 'SM', additional_price: 7000, item_stock: 0},
                ]
              }
            ]
          },
        ],
        images: []
      },
      imagePreview: [],
      variationModal: false,
      tempVariant: '',
      variantItemModal: false,
      tempVarianItem: '',
      variantSelectedIndex: null

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
    variantItemSelected() {
      if(this.variantSelectedIndex != '' || this.variantSelectedIndex != null) {
        return this.form.variants.find((el, index) => index == this.variantSelectedIndex)
      }
      return null
    },
    variantItemSelectedLabel() {
      if(this.variantItemSelected) {
        return this.variantItemSelected.variant_name
      }
      return ''
    }
  },
  methods: {
    ...mapActions('product', ['productStore']),
    ...mapActions('category', ['getCategories']),
    handleRemoveVariation(varIndex) {
      this.form.variants.splice(varIndex, 1)
    },
    handleRemoveVariantItem(varIndex, itemIndex) {
      this.form.variants[varIndex].variant_items.splice(itemIndex, 1)
    },
    handleRemoveVariantValue(varIndex, itemIndex, subItemIndex) {
      this.form.variants[varIndex].variant_items[itemIndex].variant_item_values.splice(subItemIndex, 1)
    },
    handleAddVariantValue(varIndex, itemIndex) {
      this.form.variants[varIndex].variant_items[itemIndex].variant_item_values.push(
        {item_sku: '', item_label: '', additional_price: 0},
      )
    },
    handleAddVariant() {
        this.tempVariant = ''
        this.variationModal = true
    },
    handleAddVariantItem(varIndex) {
        this.tempVarianItem = ''
        this.variantItemModal = true
        this.variantSelectedIndex = varIndex
    },
    addVariation() {
      let tpl = { variant_name: this.tempVariant, variant_items: [] }
      this.form.variants.push(tpl)
      this.tempVariant = ''
      this.variationModal = false
    },
    addVariantItem() {
      this.form.variants[this.variantSelectedIndex].variant_items.push(
        { 
            variant_item_label: this.tempVarianItem, 
            variant_item_values: [
              {item_sku: '', item_label: '', additional_price: 0, item_stock: 0},
            ]
          },
      )
      this.variantSelectedIndex = null
      this.tempVarianItem = ''
      this.variantItemModal = false
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