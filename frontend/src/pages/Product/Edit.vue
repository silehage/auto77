<template>
  <q-page>
    <q-header>
      <q-toolbar>
        <q-btn v-go-back.single flat round dense icon="eva-arrow-back" />
        <q-toolbar-title>
          Edit Produk
        </q-toolbar-title>
      </q-toolbar>
    </q-header>
    <q-form @submit.prevent="submit">
      <div class="q-pa-md q-gutter-y-sm">
        <q-input filled type="text" v-model="form.title" label="Nama Produk"
          :rules="[val => val && val != '' || 'Nama produk harus diisi']" />
        <!-- <money-formatter v-model="form.price" prefix="Rp"/> -->
        <q-input filled required label="Harga" v-model="form.price_custom_label" prefix="Rp"></q-input>
        <q-checkbox v-model="form.is_available" label="Produk Tersedia?"></q-checkbox>
        <q-select filled v-model="form.category_id" :options="categories" label="Kategori" emit-value map-options
          class="q-pb-md" />
        <div class="q-mt-md q-mb-sm">
          <label for="description" class="text-grey-7 q-pb-sm block">Deskripsi</label>
          <q-editor v-model="form.description" />
          <div class="text-xs text-red" v-if="errors.description"> {{ errors.description[0] }}</div>
        </div>
        <section id="image-section" class="q-my-md q-gutter-y-sm">
          <div>
            <q-btn label="Upload Gambar Produk" size="sm" color="primary" icon="eva-upload" class="mt-2 mr-2"
              type="button" @click.prevent="selectNewImage">
            </q-btn>
            <input type="file" class="hidden" ref="image" @change="updateImagePreview" multiple>

          </div>

          <div separator v-if="form.images.length" class="q-mt-md">
            <draggable v-model="form.images">
              <transition-group>
                <div v-for="(img, i) in form.images" :key="i" class="cursor-pointer q-pa-xs">
                  <div class="row justify-between items-center box-img-bordered q-px-sm q-py-xs relative"
                    :class="{ 'img-featured': i == 0 }">
                    <div class="featured-badge" v-if="i == 0">Featured</div>
                    <div class="row items-center q-gutter-x-sm ">
                      <div>{{ i + 1 }}</div>
                      <img :src="img.src" class="bg-white" style="width:100px;height:70px;object-fit:contain;" />
                    </div>
                    <q-btn icon="delete" round unelevated color="red" @click="handleRemoveImage(img)" dense></q-btn>
                  </div>
                </div>
              </transition-group>
            </draggable>
          </div>
        </section>
      </div>
      <!-- Start Product Variants -->
      <div id="variants">
        <div v-if="form.varians.length">
          <div v-if="form.varians[0].has_subvarian">

            <div v-for="(varian, varIndex) in form.varians" :key="varIndex">
              <div class="row items-start justify-between q-pa-md q-pt-lg">
                <div class="text-weight-bold text-md">{{ form.varians[varIndex].label }} {{ form.varians[varIndex].value
                }}
                </div>
                <div class="q-gutter-x-sm">
                  <q-btn unelevated size="10px" color="red" @click="deleteVarian(varIndex, varian)">Hapus {{
                    form.varians[varIndex].value }}</q-btn>
                  <q-btn unelevated size="10px" color="teal" @click="pushSubVarian(varIndex)">Tambah Item</q-btn>
                </div>
              </div>
              <div class="">
                <q-list class="bg-white q-pa-sm q-mt-xs" v-if="form.varians[varIndex].subvarian.length">
                  <q-item class="q-px-sm" v-for="(subvarian, subIndex) in form.varians[varIndex].subvarian"
                    :key="subIndex">
                    <q-item-section side>
                      <q-btn round unelevated padding="2px" icon="remove" size="9px" color="red"
                        @click="deleteSubvarian(varIndex, subIndex, subvarian)"></q-btn>
                    </q-item-section>
                    <q-item-section>
                      <q-input stack-label filled square required
                        v-model="form.varians[varIndex].subvarian[subIndex].value" dense
                        :label="form.varians[varIndex].subvarian[subIndex].label"></q-input>

                    </q-item-section>
                    <q-item-section>
                      <q-input stack-label filled square required
                        v-model="form.varians[varIndex].subvarian[subIndex].price" dense label="Tambahan Harga"
                        mask="###########"></q-input>
                    </q-item-section>
                    <q-item-section>
                      <q-input stack-label filled square required
                        v-model="form.varians[varIndex].subvarian[subIndex].stock" dense label="Stok"
                        mask="######"></q-input>
                    </q-item-section>

                  </q-item>
                </q-list>
              </div>
            </div>

          </div>
          <div v-else>
            <div class="row items-start justify-between q-pa-md q-pt-lg">
              <div class="text-weight-bold text-md">{{ form.varians[0].label }} </div>
              <div class="q-gutter-x-sm">
                <q-btn unelevated size="10px" color="teal" @click="pushVarian">Tambah Item</q-btn>
              </div>
            </div>
            <q-list>
              <q-list class="q-pa-sm q-mt-xs">
                <q-item v-for="(varian, vIndex) in form.varians" :key="vIndex">
                  <q-item-section side>
                    <q-btn round unelevated padding="2px" icon="remove" size="9px" color="red"
                      @click="deleteVarian(vIndex, varian)"></q-btn>
                  </q-item-section>
                  <q-item-section>
                    <q-input stack-label filled square required v-model="form.varians[vIndex].value" dense
                      :label="form.varians[vIndex].label"></q-input>
                  </q-item-section>
                  <q-item-section>
                    <q-input stack-label filled square required v-model="form.varians[vIndex].price" dense
                      label="Tambahan Harga" mask="###########"></q-input>
                  </q-item-section>
                  <q-item-section>
                    <q-input stack-label filled square required v-model="form.varians[vIndex].stock" dense label="Stok"
                      mask="######"></q-input>
                  </q-item-section>

                </q-item>
              </q-list>
            </q-list>
          </div>
        </div>
      </div>
      <!-- End Product Variants -->
      <q-footer class="bg-transparent q-pa-md">
        <q-btn color="primary" type="submit" :loading="loading" class="full-width" label="Simpan Data">
          <q-tooltip class="bg-accent">Simpan Data</q-tooltip>
        </q-btn>
      </q-footer>
    </q-form>
    <q-dialog v-model="varianModal">
      <q-card class="card-medium">
        <div class="card-heading">Tambah varian</div>
        <q-form @submit.prevent="addVarianProduk">
          <q-card-section>

            <div>
              <div class="text-md">Varian</div>
              <q-input label="Label" v-model="tempVarian.label" placeholder="contoh: Ukuran"></q-input>
              <q-input label="Item" v-model="tempVarian.value" placeholder="contoh: Small, Medium, Large"></q-input>
              <div class="text-grey-7 text-xs q-py-xs">Untuk multiple item, gunakan sparator koma</div>

            </div>
            <div v-if="canToggleSubvarian">
              <q-checkbox v-model="form.has_subvarian" label="Subvarian?"></q-checkbox>
            </div>
            <div class="q-mt-md" v-if="mustHaveSubvarian">
              <div class="text-md">Subvarian</div>
              <q-input label="Label" v-model="tempSubvarian.label" placeholder="contoh: Warna"></q-input>
              <q-input label="Item" v-model="tempSubvarian.value" placeholder="contoh: Merah, Biru, Ungu"> </q-input>
              <div class="text-grey-7 text-xs q-py-xs">Untuk multiple item, gunakan sparator koma</div>

            </div>
            <div class="flex justify-end q-mt-md q-gutter-sm">
              <q-btn label="Tutup" v-close-popup flat color="primary"></q-btn>
              <q-btn unelevated label="Tambah" type="submit" color="primary"></q-btn>
            </div>
          </q-card-section>
        </q-form>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { mapActions } from 'vuex'
import { Api } from 'boot/apiv2'
import draggable from 'vuedraggable'
export default {
  name: 'ProductFormEdit',
  components: {
    draggable,
  },
  data() {
    return {
      varianModal: false,
      tempVarian: {
        label: '',
        value: '',
      },
      tempSubvarian: {
        label: '',
        value: '',
        stock: '',
        is_preorder: false,
        price: '',
        sku: '',
        items: ''
      },
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
        varians: [],
        images: [],
        del_images: [],
        has_subvarian: false,
        remove_varian: [],
        remove_subvarian: [],
        is_available: false,
        price_custom_label: ''

      },
      imagePreview: [],
      oldImages: [],
      variantModalForm: false,
      productOldImages: [],
      chatOptions: []
    }
  },
  computed: {
    errors: function () {
      return this.$store.state.errors
    },
    loading: function () {
      return this.$store.state.loading
    },
    categories() {
      return this.$store.getters['category/getValueOptions']
    },
    canToggleSubvarian() {
      if (this.form.varians.length) {
        if (this.form.varians[0].has_subvarian) {
          return false
        }
      }
      return true
    },
    mustHaveSubvarian() {
      if (this.form.varians.length) {
        if (this.form.varians[0].has_varian) {
          return true
        }
      }
      if (this.form.has_subvarian) {
        return true
      }
      return false
    },
    canAddVarian() {
      if (this.form.varians.length) {
        if (!this.form.varians[0].has_subvarian) {
          return false
        }
      }
      return true
    }
  },
  methods: {
    ...mapActions('product', ['productUpdate', 'getProductById']),
    ...mapActions('category', ['getCategories']),
    onUpdateImage(data) {
      this.form.product_images.push(data)
    },
    onDeleteProductOldImage(filename) {
      this.productOldImages = this.productOldImages.filter(k => k.filename != filename)
      this.form.del_product_images.push(filename)
    },
    onDeleteImage(idx) {
      this.form.product_images.splice(idx, 1)
    },
    deleteVarian(varIndex, varian) {
      this.$q.dialog({
        title: 'Yakin akan menghapus data?',
        cancel: true
      }).onOk(() => {
        this.form.varians.splice(varIndex, 1)
        this.form.remove_varian.push(varian.id)
      })
    },
    deleteSubvarian(varIndex, subIndex, subvarian) {

      this.form.remove_subvarian.push(subvarian.id)
      this.form.varians[varIndex].subvarian.splice(subIndex, 1)

      if (!this.form.varians[varIndex].subvarian.length) {
        this.form.varians.splice(varIndex, 1)
      }
    },
    pushSubVarian(varIndex) {
      let varian = this.form.varians[varIndex]

      let tpl = { label: varian.subvarian[0].label, value: '', stock: 0, price: 0 }

      this.form.varians[varIndex].subvarian.push(tpl)
    },
    pushVarian() {
      this.form.varians.push({ has_subvarian: false, label: this.form.varians[0].label, value: '', stock: 0, price: 0 })

    },
    addVarianProduk() {

      let varianArr = this.tempVarian.value.split(',')

      if (this.form.has_subvarian) {

        varianArr.forEach(v => {
          let varian = null
          varian = { has_subvarian: true, label: this.tempVarian.label, value: v, subvarian: [] }

          let subArr = this.tempSubvarian.value.split(',')

          subArr.forEach(el => {
            let sub = { label: this.tempSubvarian.label, value: el, stock: 0, price: 0 }
            varian.subvarian.push(sub)
          })


          this.form.varians.push(varian)

        })
      } else {

        varianArr.forEach(v => {

          let varian = null
          varian = { has_subvarian: false, label: this.tempVarian.label, value: v, stock: 0, price: 0 }

          this.form.varians.push(varian)

        })
      }


      this.varianModal = false
    },

    handleAddVarian() {
      this.varianModal = true
    },
    // submit
    submit() {
      this.productUpdate(this.form)
    },
    selectNewImage() {
      this.$refs.image.click();
    },

    updateImagePreview() {

      let imgs = this.$refs.image.files

      const formData = new FormData();

      for (let i = 0; i < imgs.length; i++) {

        formData.append('images[' + i + ']', imgs[i]);
      }

      this.$q.loading.show({
        message: 'Uploading, Please Wait...',
        backgroundColor: 'white',
        messageColor: 'primary',
        spinnerColor: 'primary',
      });

      Api().post('assets', formData, { headers: { 'content-Type': 'multipart/formData' } })
        .then(res => {
          if (res.status == 200) {
            res.data.results.forEach(el => {
              this.form.images.push(el)
            })
          }
        })
        .finally(() => {
          this.$q.loading.hide();
        })
    },
    removeLocalImage(index) {

      this.imagePreview = this.imagePreview.filter(function (el, i) {
        return i !== index;
      })
      this.form.images = this.form.images.filter(function (el, i) {
        return i !== index;
      })
    },
    handleRemoveImage(img) {
      this.$q.loading.show({
        message: 'Please Wait...',
        backgroundColor: 'white',
        messageColor: 'primary',
        spinnerColor: 'primary',
      });
      Api().delete('assets/' + img.id)
        .then(res => {
          if (res.status == 200) {
            this.form.images = this.form.images.filter(el => el.filename != img.filename)
          }
        })
        .finally(() => {
          this.$q.loading.hide();
        })
    },
    setData() {
      this.form.id = this.product.id
      this.form.title = this.product.title
      this.form.price = this.product.custom
      this.form.price_custom_label = this.product.price_custom_label
      this.form.category_id = this.product.category_id
      this.form.description = this.product.description
      this.form.is_available = this.product.is_available ? true : false
      // this.form.varians = this.product.varians
      // this.form.has_subvarian = this.product.varians.length ? this.product.varians[0].has_subvarian : false

      this.form.images = this.product.assets

    },
  },
  mounted() {
    this.getProductById(this.$route.params.id).then((response) => {
      this.product = response.data.results
      this.setData()
    })
    this.getCategories()
  },
}
</script>
