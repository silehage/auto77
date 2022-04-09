<template>
  <q-page style="background:white;" class="q-pb-xl relative" :class="{'flex flex-center' : !ready }">
    <template v-if="ready && product">
    <div class="q-pa-md header-top">
      <div class="flex justify-between">
        <q-btn 
        @click="backButton"
        flat dense icon="keyboard_backspace" icon-size="27px" style="cursor:pointer;opacity:.9;">
        </q-btn>
        <shopping-cart  />
      </div>
    </div>
    <div class="col relative overflow-x-hidden">
      <q-carousel
        ref="carousel"
        :style="cStyle"
        class="img-carousel"
        animated
        swipeable
        v-model="slide"
        :fullscreen.sync="fullscreen"
        navigation
        infinite
        :height="height"
        style="max-height:574px;"
      >
        <q-carousel-slide v-for="(img, index) in product.assets" :key="index" :name="index+1" :img-src="img.src" ratio="1" />

        <template v-slot:control>
          <q-carousel-control
            position="bottom-right"
            :offset="[18, 40]"
          >
            <q-btn
              push round dense color="white" text-color="primary"
              :icon="fullscreen ? 'fullscreen_exit' : 'fullscreen'"
              @click="fullscreen = !fullscreen"
            />
          </q-carousel-control>
        </template>
      </q-carousel>
      <q-card flat class="product-detail relative">
        <q-card-section class="q-pt-xs">
          
          <h1 class="text-h6 text-weight-semibold q-mb-md" v-if="product">{{ product.title }}</h1>
          <div class="row items-center justify-between">
            <div class="row items-center">
              <div class="text-h6 text-weight-medium">{{ moneyIDR(parseInt(totalPrice)) }}</div>
              <div v-if="product.pricing.is_discount" class="row items-start">
                <div class="text-md text-weight-bsemibold text-red text-strike q-ml-xs">{{ moneyIDR(parseInt(product.pricing.default_price)) }}</div>
                <!-- <span v-if="product.discount" class="bg-red-6 text-white rounded-borders" style="padding:2px 3px;font-size:12px;">{{ product.pricing.discount_percent }}%</span> -->
              </div>
            </div>
            <div class="row q-gutter-md text-h6 items-center">
              <q-btn flat round icon="remove_circle_outline" size="24" @click="decrementQty" style="cursor:pointer;"></q-btn>
              <div>{{ quantity }}</div>
              <q-btn flat round icon="add_circle_outline" size="24" @click="incrementQty" style="cursor:pointer;"></q-btn>
            </div>
          </div>

          <div class="row items-center q-gutter-x-md">
            <q-rating 
              v-model="productRating"
              readonly
              color="green"
              icon="star_border"
              icon-selected="star"
              icon-half="star_half"
              size="sm" 
            />
            <!-- <div v-if="parseFloat(product.rating) > 0" class="text-weight-bold text-green text-subtitle1 text-md"> {{ product.rating }}</div> -->
          </div>
      
           <div id="variations" v-if="product.variants.length" class="q-mt-md">
            <div v-for="(variant, varIndex) in product.variants" :key="varIndex">
              <div class="q-py-sm text-weight-medium">Pilih {{ variant.variant_name }}</div>
              <div class="q-gutter-sm">
                <q-btn unelevated :outline="varItemGetColor(varItem.id)" color="green-6" v-for="(varItem, varItemIndex) in variant.variant_items" :key="varItemIndex" :label="varItem.variant_item_label" @click="handleVariantItemSelectted(varItem)"></q-btn>
              </div>
              <div v-if="variantItemSelected" class="q-pt-sm q-gutter-sm">
                <div class="q-pt-sm text-weight-medium">Pilih {{ variant.variant_item_name }}</div>
                <q-btn unelevated :disabled="itemVal.item_stock < 1" :color="itemVal.item_stock < 1? 'grey-8' : 'green-6'" :outline="varValueGetColor(itemVal.id)" v-for="(itemVal, itemValIndex) in variantItemSelected.variant_item_values" :key="itemValIndex" :label="itemVal.item_label" @click="handleSelectedItemValue(itemVal)"></q-btn>
              </div>
            </div>
            </div>
            <div v-if="isHasVariant">
             <div v-if="varianValueSelected" class="text-subtitle1 text-weight-medium q-mt-sm" :style="stockStyle()">Stok: {{ currentStock == 0 ? 'Habis' : currentStock }}
              </div>
            </div>
            <div v-if="!isHasVariant">
              <div class="text-subtitle1 text-weight-medium q-mt-sm" :style="stockStyle()">Stok: {{ currentStock == 0 ? 'Habis' : currentStock }}
              </div>
            </div>
          <div class="q-py-md">
            <h3 class="text-md q-mb-sm">Deskripsi Produk</h3>
            <div class="" v-html="product.description"></div>
          </div>
        </q-card-section>
        <q-card-section>
          <div class="flex justify-between items-center">
            <q-btn unelevated color="primary" @click="handleReviewModal" label="Berikan ulasan" class="q-my-xs"></q-btn>
            <div class="text-weight-medium text-primary text-subtitle2 q-my-xs">
             {{ product.reviews_count > 0 ? 'Total ' + product.reviews_count +' ulasan' : 'Belum ada ulasan'}}
            </div>
          </div>
        </q-card-section>
      </q-card>
      <div id="ulasan">
        <q-card flat>
          <q-card-section>
            <div class="q-gutter-y-md">
              <div v-for="(review, index) in product.reviews" :key="index">
                <div class="row justify-between items-center">
                  <div class="text-subtitle2 q-pa-sm">{{ review.name }}</div>
                  <q-item-section side>
                      <q-rating 
                      readonly
                      v-model="review.rating"
                      color="green-7"
                      icon="star_border"
                      icon-selected="star"
                      icon-half="star_half"
                    />
                  </q-item-section>
                </div>
                <div class="q-pa-md bg-grey-2 text-grey-7 text-sm"> {{ review.comment }} </div>
              </div>
            </div>
          </q-card-section>
        </q-card>
        <div class="q-my-md row justify-center">
          <q-btn flat color="primary" :loading="loadMoreLoading" v-if="product.reviews.length < product.reviews_count" label="loadmore.." @click="loadReview">
            <template v-slot:loading>
              <q-spinner-facebook />
            </template>
          </q-btn>
        </div>
      </div>
    </div>
    <q-footer class="q-gutter-x-sm flex q-pa-md bg-white">
        <!-- <q-btn @click="btnFavorite" icon="favorite" outline round :color="isLike? 'pink' : 'dark'"></q-btn> -->
        <q-btn unelevated rounded outline @click="chat" icon="chat" label="Chat" color="primary" class="col"></q-btn>
        <q-btn unelevated rounded :disabled="outOfStock" @click="addNewItem" icon="shopping_basket" :label="cartTextButton" :color="cartTextColor" class="col"></q-btn>
    </q-footer>
    </template>
      <q-inner-loading :showing="!ready">
        <q-spinner-facebook size="50px" color="primary"/>
      </q-inner-loading>
      <q-dialog v-model="reviewModal">
        <div class="q-card" style="width:100%;max-width:360px;">
          <q-card-section>
          <q-form @submit.prevent="submitReview">
            <div>
            <div class="text-subtitle2 q-mb-sm">Berikan Ulasan Anda</div>
              <q-rating 
                v-model="form.rating"
                color="green"
                icon="star_border"
                icon-selected="star"
                icon-half="star_half"
                size="sm" 
              />
            <div class="q-my-md q-gutter-y-xs">
              <q-input 
              dense 
              label="Nama Anda" 
              v-model="form.name"
              :rules="[val => val && val != '' || 'Wajib disisi']"
              ></q-input>
              <q-input 
              dense 
              label="Ulasan Anda" 
              type="textarea" 
              v-model="form.comment" 
              rows="3"
              :rules="[val => val && val != '' || 'Wajib disisi']"
              ></q-input>
            </div>
            <div class="q-gutter-y-sm q-my-md items-center text-grey">
              <div class="text-grey text-xs">Jawab tantangan berikut, hanya untuk memastikan anda bukan robot</div>
              <div class="row q-gutter-x-sm items-center">
                <div class="text-weight-bold bg-dark text-white q-px-sm q-py-xs rounded">{{ number2 }} + {{ number1 }} </div> 
                <div class="text-weight-bold"> = </div> 
               <input class="rounded text-grey-9" type="text" v-model="jawaban" style="width:60px;padding:3px 6px;border:1px solid grey">
              </div>
            </div>
            <div class="row justify-end q-gutter-x-sm">
              <q-btn type="button" @click.prevent="reviewModal = false" label="Batal" color="secondary"></q-btn>
              <q-btn :disabled="chalengeTesting" type="submit" :loading="loading" label="Kirim Ulasan" color="primary"></q-btn>
            </div>
          </div>
          </q-form>
          </q-card-section>
        </div>
      </q-dialog>
      <q-dialog v-model="chatModal">
      <q-card style="max-width:450px;width:100%;" class="text-grey-9">
        <div class="text-weight-bold q-py-sm q-px-md bg-primary text-white">Kirim pesan / tanya ke penjual</div>
        <q-card-section class="transition-height">
          <div class="q-mb-sm text-subtitle2" v-if="this.product"># {{ product.title }}</div>
          <q-input outlined autogrow autofocus v-model="chatText" placeholder="contoh: Halo Admin, Apakah ini masih ada?"></q-input>
          <div class="q-pt-sm">
            <div class="q-pa-xs text-xs cursor-pointer" v-for="chat in defaultChat" :key="chat" @click="changeChatText(chat)">
              <span>{{ chat }}</span>
            </div>
          </div>
        </q-card-section>
        <q-card-actions class="justify-end">
          <q-btn @click="closeChatModal" flat label="Batal"></q-btn>
          <q-btn @click="submitChat" :disabled="!chatText" flat color="primary" label="Kirim"></q-btn>
        </q-card-actions>
      </q-card>
    </q-dialog>
    <q-dialog 
    v-model="cartModal"
    position="bottom"
    transition-show="slide-up"
    transition-hide="slide-down"
    >
      <q-card flat class="max-width bg-white" v-if="product">
        <q-linear-progress size="10px" :value="100" />
          <q-card-section>
            <!-- <q-item-label class="text-weight-medium">{{ product.title }}</q-item-label> -->
          <q-list>
            <q-item>
              <q-item-section avatar>
                <q-img :src="product.assets[0].src" width="60px" class="rounded-borders"></q-img>
              </q-item-section>
              <q-item-section top>
                <div class="text-md text-weight-meduim q-mb-sm">Produk berhasil ditambahkan.</div>
                <q-item-label caption>Anda bisa lanjut kehalaman checkout atau berbelanja kembali</q-item-label>
              </q-item-section>
            </q-item>
          </q-list>
          <div class="flex justify-end q-gutter-x-sm q-pt-sm">
            <q-btn flat no-caps @click="cartModal = false" label="Berbelanja Lagi" color="primary"></q-btn>
            <q-btn unelevated no-caps :to="{ name: 'Cart' }" label="Lanjut Checkout" color="primary"></q-btn>
          </div>
        </q-card-section>
      </q-card>
    </q-dialog>
    <q-dialog v-model="alreadyItemModal">
      <q-card style="width:100%;max-width:350px;">
        <q-card-section>
          <div class="q-mb-sm text-weight-medium text-md">Produk yang sama ada di keranjang</div>
          <div>Tetap ingin menambahkan? jika "YA" item akan di perbarui kuantitasnya.</div>
        </q-card-section>
        <q-card-actions class="justify-end q-gutter-x-sm">
          <q-btn flat no-caps @click="alreadyItemModal = false" label="Batalkan" color="primary"></q-btn>
          <q-btn unelevated no-caps @click="updateNewItem" label="YA Tambahkan" color="primary"></q-btn>
        </q-card-actions>
      </q-card>
    </q-dialog>
    <q-dialog 
      v-model="formVariantModal"
      position="bottom"
      transition-show="slide-up"
      transition-hide="slide-down"
      >
      <q-card class="max-width" flat v-if="product">
        <q-card-section>
          <div class="text-weight-medium text-md2 q-mb-sm text-green">{{ moneyIDR(parseInt(totalPrice)) }}</div>
          <div id="variations" v-if="product.variants.length" class="">
            <div v-for="(variant, varIndex) in product.variants" :key="varIndex">
              <div class="q-py-sm text-weight-medium">Pilih {{ variant.variant_name }}</div>
              <div class="q-gutter-sm">
                <q-btn unelevated :outline="varItemGetColor(varItem.id)" color="green" v-for="(varItem, varItemIndex) in variant.variant_items" :key="varItemIndex" :label="varItem.variant_item_label" @click="handleVariantItemSelectted(varItem)"></q-btn>
              </div>
              <div v-if="variantItemSelected" class="q-pt-md q-gutter-sm">
                <div class="q-pt-sm text-weight-medium">Pilih {{ variant.variant_item_name }}</div>
                <q-btn unelevated :disabled="itemVal.item_stock < 1" :color="itemVal.item_stock < 1? 'grey' : 'green'" :outline="varValueGetColor(itemVal.id)" v-for="(itemVal, itemValIndex) in variantItemSelected.variant_item_values" :key="itemValIndex" :label="itemVal.item_label" @click="handleSelectedItemValue(itemVal)"></q-btn>
              </div>
            </div>
            </div>
        </q-card-section>
        <q-card-section>
        <q-btn unelevated :disabled="outOfStock" @click="addNewItem" icon="shopping_basket" :label="cartTextButton" :color="cartTextColor" class="full-width"></q-btn>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { mapMutations, mapActions } from 'vuex'
import ShoppingCart from 'components/ShoppingCart.vue'
export default {
  name: 'ProductShow',
  components: { ShoppingCart },
  data () {
    return {
      defaultChat: ['Apakah ini masih ada?', 'Apakah bisa grosir?'],
      chatText: '',
      chatModal: false,
      reviewModal: false,
      number1: 0,
      number2: 0,
      jawaban: '',
      loading: false,
      slide: 1,
      quantity: 1,
      discount: 0,
      fullscreen: false,
      shop: this.$store.state.shop,
      ready: false,
      product: null,
      loadMoreLoading: false,
      form: {
        product_id: null,
        name: '',
        comment: '',
        rating: 0
      },
      cartModal: false,
      alreadyItemModal: false,
      variantItemSelected: null,
      varianValueSelected: null,
      formVariantModal: false
    }
  },
  computed: {
    session_id() {
      return this.$store.state.session_id
    },
    chalengeTesting() {
      return this.number1+this.number2 != this.jawaban
    },
    productRating() {
      return parseFloat(this.product.rating)
    },
    carts() {
      return this.$store.getters['cart/getCarts']
    },
    favorites: function() {
      return this.$store.state.product.favorites
    },
    isLike() {
      if(this.favorites.length > 0) {
         return this.favorites.find(el => el == this.$route.params.id) ? true : false
      }
      return false;
    },
    cStyle() {
      if(!this.fullscreen && this.$q.screen.width < 560 && this.$q.screen.width > 200) {
        return 'height:'+ this.$q.screen.width +'px'
      }
        return ''
    },
    height() {
      return this.$q.screen.width+'px'
    },
    productStock() {
      return this.product.variant_items_sum_item_stock? parseInt(this.product.variant_items_sum_item_stock) : this.product.stock
    },
    outOfStock() {
      return (this.currentStock-this.quantity) < 0
    },
    currentStock() {

      let hasCart = this.carts.items.find(el => el.sku == this.currentProductSku)

      if(hasCart != undefined) {

        if(this.varianValueSelected) {

          return parseInt(this.varianValueSelected.item_stock)-hasCart.quantity
        }
       return this.productStock-hasCart.quantity

      } else {
        if(this.varianValueSelected) {

          return parseInt(this.varianValueSelected.item_stock)
        }
        return this.productStock
      }
    },
    isHasVariant() {
      return this.product.variants.length > 0
    },
    currentProductSku() {
      if(this.varianValueSelected) {
        return this.varianValueSelected.item_sku
      }
      return this.product.sku ? this.product.sku : this.product.id
    },
    realPrice() {
      if(this.varianValueSelected) {

        return (parseInt(this.product.pricing.current_price)+parseInt(this.varianValueSelected.additional_price))
      }
      return parseInt(this.product.pricing.current_price)
    },
    totalPrice() {
      if(this.varianValueSelected) {

        return (parseInt(this.product.pricing.current_price)+parseInt(this.varianValueSelected.additional_price)) * this.quantity
      }
      return parseInt(this.product.pricing.current_price) * this.quantity
    },
    cartTextButton() {
       if(this.currentStock >= 1) {
            return 'Beli'
          }
        return 'Habis'
    },
    cartTextColor() {
      if(this.currentStock >=1){
        return 'primary'
      }
      return 'grey-7'
    },

  },
  methods: {
    ...mapMutations('product',['ADD_REMOVE_TO_FAVORITE']),
    ...mapActions('product', ['getProductBySlug', 'loadProductReview', 'addProductReview']),
    backButton() {
      if(window.history.length > 2) {
        window.history.back()
      }else {
        this.$router.push({name: 'ProductIndex'})
      }
    },
    
    varItemGetColor(id) {
      if(this.variantItemSelected) {
        return this.variantItemSelected.id == id ?  false : true
      }
      return true
    },
    varValueGetColor(id) {
      if(this.varianValueSelected) {
        return this.varianValueSelected.id == id ? false : true
      }
      return true
    },
    handleVariantItemSelectted(item) {
      this.variantItemSelected = item
      this.varianValueSelected = null
      this.quantity = 1
    },
    handleSelectedItemValue(value) {
      this.varianValueSelected = value
      this.quantity = 1
    },
    discountPriceFormat() {
      return (this.subtotal()*this.discount)/100
    },
    subQty() {
      if(this.carts.items.length > 1) {
        return this.carts.items.reduce((a,b) => a.quantity + b.quantity) 
      }
      return this.carts.items[0].quantity
    },
    subtotal() {
      if(this.carts.items.length > 1) {
        let j = [];
        this.carts.items.forEach(element => {
          j.push(element.quantity*element.price)
        });
        return j.reduce((a,b) => a + b)
      }
      return this.carts.items[0].quantity * this.carts.items[0].price
    },
    total () {
      if(this.discount || this.discount !== 0) {
        return (this.subtotal() - this.discountPriceFormat())
      }
      return this.subtotal()
    },
    stockStyle() {
      if(this.currentStock >= 6 ) return 'color:green'
      if(this.currentStock > 0 && this.currentStock <= 5) return 'color:#ffa800fc'
      if(this.currentStock <= 0 ) return 'color:red'
    },
    addToCart() {
      this.formVariantModal = false
     if(! this.session_id) this.makeSessionId()
      this.$store.dispatch('cart/addToCart' , {
        session_id: this.session_id, 
        product_id: this.product.id, 
        product_stock: this.currentStock, 
        sku: this.currentProductSku, 
        name: this.product.title, 
        price: this.realPrice, 
        quantity: this.quantity, 
        note: this.getCartNote(),
        product_url: this.getRoutePath(), 
        image_url: this.product.assets[0].src, 
        weight: this.product.weight})

        this.quantity = 1
    },
    addNewItem() {
      if(this.isHasVariant) {
        if(!this.varianValueSelected || !this.variantItemSelected) {
          if(this.formVariantModal) {
            this.$q.notify({
              type: 'info',
              message: 'Silahkan pilih produk varian terlebih dahulu',
            })
          } else {
            this.formVariantModal = true
          }
          return
        }
      }
      this.checkCart().then(res => {
        this.addToCart()
        this.cartModal = true
      }).catch(err => {
        this.alreadyItemModal = true
      })
    },
    updateNewItem() {
      this.alreadyItemModal = false
      this.addToCart()
      this.cartModal = true
    },
    checkCart() {
      return new Promise((resolve, reject) => {
        let cartAlready;

          cartAlready = this.carts.items.find(el => el.sku == this.currentProductSku)

          if(cartAlready != undefined) {

            reject()

          } else {

            resolve()
          }

      })
    },
    getCartNote() {
      let str = 'Varian: '
      if(this.isHasVariant) {
        str += this.product.variants[0].variant_name + ' ' + this.variantItemSelected.variant_item_label + ', ' + this.product.variants[0].variant_item_name + ' ' + this.varianValueSelected.item_label
      }
      return str
    },
    getRoutePath() {
      let props = this.$router.resolve({ 
        name: 'ProductShow',
        params: { slug: this.product.slug },
      });

      return location.origin + props.href;
    },
    btnFavorite() {
      this.ADD_REMOVE_TO_FAVORITE(this.product.id)
    },
    setPrice() {
      this.priceTotal = this.price*this.quantity
    },
    incrementQty() {
      if(this.quantity < this.currentStock) {
        this.quantity += 1
      } else {
        this.$q.dialog({
          title: 'Warning!',
          message: 'Stok tidak cukup, stok tersisa ' + this.currentStock + ' item.'
        })
      }
    },
    decrementQty() {
      if(this.quantity > 1) {
        this.quantity -= 1
      }
    },
    getTeaser(html) {
      if(html) {
        let strippedString = html.replace(/(<([^>]+)>)/gi, "");
        return strippedString.substr(0, 80)
      } else {
        return ''
      }
    },
    handleReviewModal() {
      this.getRandomNumber()
      this.reviewModal = true
    },
    submitReview() {
      if(this.number1+this.number2 != this.jawaban) {
        this.$q.notify({
          type: 'negative',
          message: 'Jawaban Salah, silahkan jawab dengan benar.'
        })
        this.getRandomNumber()
        return
      }
      this.jawaban = ''
      this.getRandomNumber()
      this.form.product_id = this.product.id
      if(this.form.name && this.form.comment && this.form.rating) {
        this.loading = true
        this.reviewModal = false
        this.addProductReview(this.form)
        this.getProduct()
        this.resetForm()
        this.loading = false
      } else {
        this.$q.notify({
          type: 'warning',
          message: 'Semua field harus di isi'
        })
      }
    },
    resetForm() {
      this.form.name = ''
      this.form.comment = ''
    },
    loadReview() {
      this.loadMoreLoading = true
      this.loadProductReview({ product_id: this.product.id, skip: this.product.reviews.length }).then(response => {
        if(response.status == 200) {
          this.loadMoreLoading = false
          this.product.reviews = [... this.product.reviews, ...response.data.results]
        }
      }).catch(err => {
         this.loadMoreLoading = false
      })
    },
    getProduct() {
      this.getProductBySlug(this.$route.params.slug).then(response => {
        if(response.status == 200) {
          this.product = response.data.results
          this.ready = true
          if(this.isHasVariant) {
            this.variantItemSelected = this.product.variants[0].variant_items[0];
          }
        } else {
          this.$router.push({name: 'ProductIndex'})
        }
      }).catch(() => {
        this.$router.push({name: 'ProductIndex'})
      })
    },
    getRandomNumber() {
      let number = [1,2,3,4,5,6,7,8,9]
      this.number1 = Math.floor((Math.random() * number.length));
      this.number2= Math.floor((Math.random() * number.length));
    },
    formatPhoneNumber(number) {
      let formatted = number.replace(/\D/g,'')

      if(formatted.startsWith('0')) {
        formatted = '62' + formatted.substr(1)
      }

      return formatted;
    },
    chat() {
      this.chatModal = true
    },
    closeChatModal() {
      this.chatText = ''
      this.chatModal = false
    },
    changeChatText(chat) {
      this.chatText = chat
    },
    submitChat() {
      let shopPhone = this.shop.phone
      if(!shopPhone) {
        this.$q.dialog({
            title: 'Maaf, Sedang masalah!',
            message: 'Silahkan coba kembali beberapa saat lagi, jika masih berlanjut silahkan hubungi kami.',
          })
          return
      }

      let link = 'https://api.whatsapp.com/send?phone=' + this.formatPhoneNumber(shopPhone) + '&text=' + encodeURI(this.chatText + '\nProduk: '+ this.product.title +'\n') + location.href;
      window.open(link, '_blank');

      setTimeout(() => {
        this.closeChatModal()
      }, 2000)

    },
    makeSessionId() {
      var randomChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

      var result = '';
        for ( var i = 0; i < 60; i++ ) {
            result += randomChars.charAt(Math.floor(Math.random() * randomChars.length));
        }

       this.$store.commit('SET_SESSION_ID', result);
    }

  },
  mounted() {
    this.getProduct()
    this.getRandomNumber()
      
  },
  meta() {
    return {
      title: this.product?.title,
      meta: {
        description: { name: 'description', content: this.getTeaser(this.product?.description) },
        ogDescription: { property: 'og:description', content: this.getTeaser(this.product?.description)  },
        ogTitle:  { property: 'og:title', content: this.product?.title },
        ogImage:  { property: 'og:image', content: this.product?.assets[0].src },
      }
    }
  }
}
</script>

<style lang="scss">
.header-top {
  z-index:50;
  position:fixed;
  top:0;
  left:0;
  right:0;
  width:100%;
  background: transparent;
  background: linear-gradient(0deg, rgba(2, 0, 36, 0) 0%, rgba(0, 0, 5, 0.678) 100%); 
  color:#fff;
}
.q-body--fullscreen-mixin .img-carousel::after {
  height: 0;
}
.product-detail::before {
position: absolute;
  width: 100%;
  background-color:white;
  content: "";
  height: 20px;
  border-radius: 80px 80px 0 0;
  top: -20px;
  left: 0;
  .q-carousel__control.absolute.absolute-bottom-right {
    transform: translateY(-20px)
  }
  .q-carousel__navigation--bottom{
    transform: translateY(-20px)
  }
}
</style>