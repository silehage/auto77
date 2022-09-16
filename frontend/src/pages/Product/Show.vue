<template>
  <q-page  class="q-pb-xl relative" :class="{'flex flex-center' : !ready }">
    <template v-if="ready && product">
    <div class="q-pa-md header-top">
      <div class="flex justify-between">
        <q-btn 
        @click="backButton"
        flat dense icon="eva-arrow-back" icon-size="27px" style="cursor:pointer;opacity:.9;">
        </q-btn>
        <MenuRight />
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
              :icon="fullscreen ? 'eva-minimize-outline' : 'eva-maximize-outline'"
              @click="fullscreen = !fullscreen"
            />
          </q-carousel-control>
        </template>
      </q-carousel>
      <q-card class="product-detail relative box-shadow">
        <q-card-section class="q-py-xs">
          
          <h1 class="text-h6 text-weight-semibold q-mb-md" v-if="product">{{ product.title }}</h1>
          <div class="row items-center justify-between">
            <div class="flex items-center">
              <div class="flex items-center">
                  <span class="text-md q-mr-sm">Rp</span>
                  <span class="text-lg text-weight-medium">
                  {{ $money(parseInt(totalPrice)) }} 
                  </span>
                </div>
                <div class="flex items-center text-strike text-xs q-ml-xs text-red" v-if="product.pricing.is_discount">
                  <span class="text-sm q-mr-sm">Rp</span>
                  <span class="text-md">{{ $money(product.pricing.default_price * quantity) }} </span>
                </div>
              <div>
              </div>
            </div>
          </div>

        </q-card-section>
      </q-card>

     <div class="box-shadow q-pa-md q-mt-md" v-if="product.varians.length">
        <div>
          <div class="text-md">Pilih Varian <span class="text-sm text-weight-normal text-grey-7"></span></div>
          <div class="">
          <div class="q-mb-xs">{{ product.varians[0].label}}</div>
          <div class="q-gutter-sm">
            <q-btn class="product-varian--btn" outline v-for="item in product.varians" :key="item.id" :label="item.value" :color="varianSelected && varianSelected.id == item.id? 'accent' : 'grey-9'" @click="selectProductVarian(item)">
            <badge-tick v-if="varianSelected && varianSelected.id == item.id " />
            </q-btn>
          </div>
          </div>
            <div class="q-mt-md" v-if="varianSelected && varianSelected.has_subvarian">
              <div class="q-mb-xs">{{ varianSelected.subvarian[0].label }}</div>
              <div class="q-gutter-sm">
                <q-btn outline v-for="item in varianSelected.subvarian" 
                :key="item.id" 
                :label="item.value" 
                :disable="item.stock < 1 && !item.is_preorder"
                @click="selectProductSubvarian(item)" 
                :color="subvarianSelected && subvarianSelected.id == item.id ? 'accent' : 'grey-9'" 
                class="relative product-variation product-varian--btn"
                >
                  <badge-tick v-if="subvarianSelected && subvarianSelected.id == item.id " />
                </q-btn>
              </div>
          </div>
        </div>
      </div>
      <q-card class="box-shadow q-mt-md">
        <q-card-section class="" style="min-height:150px;">
          <div class="text-md q-mb-md">Deskripsi Produk</div>
          <div class="" v-html="product.description"></div>
        </q-card-section>
      </q-card>
    </div>
    <q-footer class="q-gutter-x-sm flex q-pa-md bg-transparent">
        <q-btn unelevated rounded @click="checkoutModal = true" icon="eva-message-circle-outline" label="Hubungi Kami" color="primary" class="col"></q-btn>
    </q-footer>
    </template>
      <q-inner-loading :showing="!ready">
        <q-spinner-facebook size="50px" color="primary"/>
      </q-inner-loading>
      <q-dialog v-model="checkoutModal">
      <q-card style="max-width:450px;width:100%;">
        <div class="text-weight-bold q-py-sm q-px-md text-md">Hubungi Cs Kami</div>
        <q-card-section class="q-px-md q-pb-lg column q-gutter-y-md">
          <q-btn v-for="(cs, index) in customer_services" :key="index" color="green" class="full-width" icon="eva-message-circle-outline" :label="cs.name"  @click="checkoutByCs(cs)">
          </q-btn>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { mapMutations, mapActions } from 'vuex'
import MenuRight from 'components/MenuRight.vue'
import BadgeTick from 'components/BadgeTick.vue'
import { Api } from 'boot/axios'
export default {
  name: 'ProductShow',
  components: { MenuRight, BadgeTick },
  data () {
    return {
      tab: 'Description',
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
      loadMoreLoading: false,
      checkoutModal: false,
      form: {
        product_id: null,
        name: '',
        comment: '',
        rating: 0
      },
      cartModal: false,
      alreadyItemModal: false,
      varianSelected: null,
      subvarianSelected: null,
      formVariantModal: false,
      product: null,
      productReviews: [],
    }
  },
  computed: {
    customer_services() {
      return this.$store.state.customer_services
    },
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
      // return this.$store.getters['cart/getCarts']
      return this.$store.state.cart.carts
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
      if(this.product.varians.length) {
        if(this.subvarianSelected) {
          return this.subvarianSelected.stock
        }
        return 0
      } 
      return this.product.stock
    },
    currentStock() {
      let hasCart = this.carts.find(el => el.sku == this.currentProductSku)

      if(this.isHasVarian) {

        if(this.varianSelected) {  
        
          if(!this.varianSelected.has_subvarian) {
            return hasCart != undefined ? this.varianSelected.stock-hasCart.quantity : this.varianSelected.stock
          }

          if(this.subvarianSelected) {
  
            return hasCart != undefined ? this.subvarianSelected.stock-hasCart.quantity : this.subvarianSelected.stock
          }

          return hasCart != undefined ? this.productStock-hasCart.quantity : this.productStock

        } else {

           if(this.subvarianSelected) {

            return hasCart != undefined ? this.subvarianSelected.stock-hasCart.quantity :  this.productStock
          }

          return hasCart != undefined ?  this.productStock-hasCart.quantity :  this.productStock
        }

      }else {

        return hasCart != undefined ?  this.productStock-hasCart.quantity :  this.productStock

      }

    },
    isHasVarian() {
      return this.product.varians.length > 0
    },
    currentProductSku() {
      if(this.varianSelected) {
        if(!this.varianSelected.has_subvarian) {
          return this.varianSelected.sku
        }
        if(this.subvarianSelected) {
          return this.subvarianSelected.sku
        }
      }
      return this.product.sku ? this.product.sku : this.product.id
    },
    realPrice() {
      if(this.varianSelected) {

        if(!this.varianSelected.has_subvarian) {
          return this.product.pricing.current_price + this.varianSelected.price
        }

        if(this.subvarianSelected) {
  
          return  this.product.pricing.current_price + this.subvarianSelected.price
        }
      }
      return this.product.pricing.current_price
    },
    totalPrice() {
      if(this.varianSelected) {

        if(!this.varianSelected.has_subvarian) {

          return (this.product.pricing.current_price + this.varianSelected.price) * this.quantity

        } else {

          if(this.subvarianSelected) {
    
            return (this.product.pricing.current_price + this.subvarianSelected.price) * this.quantity
          } 
  
            return (this.product.pricing.current_price + this.varianSelected.subvarian[0].price) * this.quantity
        }

      }

      return this.product.pricing.current_price * this.quantity
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
    selectProductVarian(item) {
      this.varianSelected = item
      this.subvarianSelected = null
      this.quantity = 1
    },
    checkoutByCs(cs) {
      this.$q.loading.show()
      let str = `Halo apakah ${this.product.title} masih ada?`
      str = encodeURI(str)
      let link = 'https://api.whatsapp.com/send?phone=' + this.formatPhoneNumber(cs.phone) + '&text=' + str

      window.open(link, '_blank');

      setTimeout(() => {
        this.checkoutModal = false
         this.$q.loading.hide()
      }, 2000)
    },
    selectProductSubvarian(item) {
      this.subvarianSelected = item
      this.quantity = 1
    },
    backButton() {
      if(window.history.length > 2) {
        window.history.back()
      }else {
        this.$router.push({name: 'ProductIndex'})
      }
    },
    getIcon(rating) {
      let rtg = parseInt(rating)

      if(rtg <= 2) {
        return 'sentiment_very_dissatisfied'
      } else if(rtg == 3) {
        return 'sentiment_neutral'
      } else if(rtg == 4) {
        return 'sentiment_satisfied_alt'
      }else {
         return 'sentiment_very_satisfied'
      }
    },
    varItemGetColor(id) {
      if(this.varianSelected) {
        return this.varianSelected.id == id ?  false : true
      }
      return true
    },
    varValueGetColor(id) {
      if(this.subvarianSelected) {
        return this.subvarianSelected.id == id ? false : true
      }
      return true
    },
    handleVariantItemSelectted(item) {
      this.varianSelected = item
      this.subvarianSelected = null
      this.quantity = 1
      this.setViewProductPattern()
    },
    handleSelectedItemValue(value) {
      this.subvarianSelected = value
      this.quantity = 1
      this.setViewProductPattern()
    },
    discountPriceFormat() {
      return (this.subtotal()*this.discount)/100
    },
    subQty() {
      if(this.carts.length > 1) {
        return this.carts.reduce((a,b) => a.quantity + b.quantity) 
      }
      return this.carts[0].quantity
    },
    subtotal() {
      if(this.carts.length > 1) {
        let j = [];
        this.carts.forEach(element => {
          j.push(element.quantity*element.price)
        });
        return j.reduce((a,b) => a + b)
      }
      return this.carts[0].quantity * this.carts[0].price
    },
    total () {
      if(this.discount || this.discount !== 0) {
        return (this.subtotal() - this.discountPriceFormat())
      }
      return this.subtotal()
    },
    addToCart() {
      
      this.formVariantModal = false

      let cartItem = {
        session_id: this.session_id, 
        product_id: this.product.id, 
        product_stock: this.currentStock, 
        sku: this.currentProductSku, 
        name: this.product.title, 
        price: this.realPrice, 
        quantity: this.quantity, 
        note: this.getVarianTextNote(),
        product_url: this.getRoutePath(), 
        image_url: this.product.assets[0].src, 
        weight: this.product.weight
      }


      this.$store.dispatch('cart/addToCart' , cartItem)

        this.quantity = 1
    },
    showNotifyHasSelectVarian() {
      if(this.formVariantModal) {
        this.$q.notify({
          type: 'info',
          message: 'Silahkan pilih produk varian terlebih dahulu',
        })
      } else {
        this.formVariantModal = true
      }
    },
    addNewItem() {
      if(this.isHasVarian) {
        if(this.varianSelected) {

          if(this.varianSelected.has_subvarian) {

            if(!this.subvarianSelected) {

              this.showNotifyHasSelectVarian()

              return 
            }
          }

        } else {
          this.showNotifyHasSelectVarian()
          return

        }
      }

      if(this.currentStock <= 0) {
        let item = this.isHasVarian ? 'varian' : 'produk'
        this.$q.dialog({
          title: 'Stok habis',
          message: `Maaf, stok untuk ${item} ini habis, silahkan pilih ${item} lainnya.`
        })

        return
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

          cartAlready = this.carts.find(el => el.sku == this.currentProductSku)

          if(cartAlready != undefined) {

            reject()

          } else {

            resolve()
          }

      })
    },
    getVarianTextNote() {
       let str = ''
      if(this.varianSelected) {
        if(this.varianSelected.has_subvarian && this.subvarianSelected) {
          str += `${this.varianSelected.label} ${this.varianSelected.value}, ${this.subvarianSelected.label} ${this.subvarianSelected.value}`
        } else {
          str += `${this.varianSelected.label} ${this.varianSelected.value}`
        }
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
    checkVarianIsReady() {
      if(this.isHasVarian) {
        if(!this.varianSelected) return false

        if(this.varianSelected.has_subvarian) {
          if(!this.subvarianSelected) return false
        }
      }
      return true
    },
    getTeaser(html) {
      if(html) {
        let strippedString = html.replace(/(<([^>]+)>)/gi, "");
        return strippedString.substr(0, 80)
      } else {
        return ''
      }
    },

    getProduct() {
      this.getProductBySlug(this.$route.params.slug).then(response => {
        if(response.status == 200) {
          this.product = response.data.data
          this.ready = true
          if(this.isHasVarian) {
            if(this.product.varians[0].has_subvarian) {
              this.varianSelected = this.product.varians[0];
            }
          }
        } else {
          // this.$router.push({name: 'ProductIndex'})
        }
      }).catch(() => {
        // this.$router.push({name: 'ProductIndex'})
      })
    },
    getCustomerService() {
      this.$store.dispatch('getCustomerService').then(response => {
        if(response.status == 200) {
          let cs = []
          cs = response.data.results

          if(!cs.length) {
            cs = [{
              name: 'Admin',
              phone: this.shop.phone
            }]
          }
          this.$store.commit('SET_CS', cs)
        }
      })
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

  },
  mounted() {
    if(!this.session_id) {
      this.makeSessionId()
    }
    if(!this.customer_services.length) {
      this.getCustomerService()
    }
  },
  created() {
    if(!this.product || this.product.slug != this.$route.params.slug) {
      this.getProduct()  

    } else {
      this.ready = true
    }
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