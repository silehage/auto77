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
        <q-btn unelevated rounded @click="checkoutModal = true" no-caps icon="eva-message-circle-outline" label="Hubungi kami" color="primary" class="col"></q-btn>
    </q-footer>
    </template>
      <q-inner-loading :showing="!ready">
        <q-spinner-facebook size="50px" color="primary"/>
      </q-inner-loading>
      <q-dialog v-model="checkoutModal">
      <q-card class="" style="max-width:450px;width:100%;">
        <div class="q-px-lg q-pb-xl q-pt-md column q-gutter-y-md">
          <div class="text-weight-bold q-mb-sm text-lg">Hubungi Cs Kami</div>
          <q-list separator>
           <q-separator></q-separator>
            <q-item avatar v-for="(cs,i) in customer_services" :key="i" clickable @click="checkoutByCs(cs)">
              <q-item-section avatar>
                <q-icon color="green" text-color="green" padding="sm">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 50 50" width="20px" height="20px">
                    <g id="surface1441897">
                    <path style=" stroke:none;fill-rule:nonzero;fill:currentColor;fill-opacity:1;" d="M 25 2 C 12.316406 2 2 12.316406 2 25 C 2 28.960938 3.023438 32.855469 4.964844 36.289062 L 2.035156 46.730469 C 1.941406 47.074219 2.035156 47.441406 2.28125 47.695312 C 2.472656 47.894531 2.734375 48 3 48 C 3.078125 48 3.160156 47.988281 3.238281 47.972656 L 14.136719 45.273438 C 17.464844 47.058594 21.210938 48 25 48 C 37.683594 48 48 37.683594 48 25 C 48 12.316406 37.683594 2 25 2 Z M 36.570312 33.117188 C 36.078125 34.476562 33.71875 35.722656 32.585938 35.886719 C 31.566406 36.035156 30.277344 36.101562 28.863281 35.65625 C 28.007812 35.386719 26.90625 35.027344 25.496094 34.429688 C 19.574219 31.902344 15.707031 26.011719 15.410156 25.625 C 15.117188 25.234375 13 22.464844 13 19.59375 C 13 16.726562 14.523438 15.3125 15.066406 14.730469 C 15.609375 14.144531 16.246094 14 16.640625 14 C 17.035156 14 17.429688 14.003906 17.773438 14.019531 C 18.136719 14.039062 18.625 13.882812 19.101562 15.023438 C 19.59375 16.191406 20.777344 19.058594 20.921875 19.351562 C 21.070312 19.644531 21.167969 19.984375 20.972656 20.375 C 20.777344 20.761719 20.679688 21.007812 20.382812 21.347656 C 20.085938 21.6875 19.761719 22.105469 19.496094 22.367188 C 19.199219 22.660156 18.894531 22.976562 19.238281 23.558594 C 19.582031 24.144531 20.765625 26.050781 22.523438 27.597656 C 24.777344 29.585938 26.679688 30.199219 27.269531 30.492188 C 27.859375 30.785156 28.203125 30.734375 28.550781 30.347656 C 28.894531 29.957031 30.023438 28.644531 30.417969 28.058594 C 30.8125 27.476562 31.203125 27.574219 31.746094 27.769531 C 32.289062 27.960938 35.191406 29.371094 35.78125 29.664062 C 36.371094 29.957031 36.765625 30.101562 36.914062 30.34375 C 37.0625 30.585938 37.0625 31.753906 36.570312 33.117188 Z M 36.570312 33.117188 "/>
                    </g>
                  </svg>
                </q-icon>
              </q-item-section>
              <q-item-section>{{ cs.name }}</q-item-section>
            </q-item>
             <q-separator></q-separator>
          </q-list>
          <!-- <q-btn no-caps v-for="(cs, index) in customer_services" :key="index" color="green" class="full-width"  @click="checkoutByCs(cs)">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 50 50" width="20px" height="20px">
              <g id="surface1441897">
              <path style=" stroke:none;fill-rule:nonzero;fill:currentColor;fill-opacity:1;" d="M 25 2 C 12.316406 2 2 12.316406 2 25 C 2 28.960938 3.023438 32.855469 4.964844 36.289062 L 2.035156 46.730469 C 1.941406 47.074219 2.035156 47.441406 2.28125 47.695312 C 2.472656 47.894531 2.734375 48 3 48 C 3.078125 48 3.160156 47.988281 3.238281 47.972656 L 14.136719 45.273438 C 17.464844 47.058594 21.210938 48 25 48 C 37.683594 48 48 37.683594 48 25 C 48 12.316406 37.683594 2 25 2 Z M 36.570312 33.117188 C 36.078125 34.476562 33.71875 35.722656 32.585938 35.886719 C 31.566406 36.035156 30.277344 36.101562 28.863281 35.65625 C 28.007812 35.386719 26.90625 35.027344 25.496094 34.429688 C 19.574219 31.902344 15.707031 26.011719 15.410156 25.625 C 15.117188 25.234375 13 22.464844 13 19.59375 C 13 16.726562 14.523438 15.3125 15.066406 14.730469 C 15.609375 14.144531 16.246094 14 16.640625 14 C 17.035156 14 17.429688 14.003906 17.773438 14.019531 C 18.136719 14.039062 18.625 13.882812 19.101562 15.023438 C 19.59375 16.191406 20.777344 19.058594 20.921875 19.351562 C 21.070312 19.644531 21.167969 19.984375 20.972656 20.375 C 20.777344 20.761719 20.679688 21.007812 20.382812 21.347656 C 20.085938 21.6875 19.761719 22.105469 19.496094 22.367188 C 19.199219 22.660156 18.894531 22.976562 19.238281 23.558594 C 19.582031 24.144531 20.765625 26.050781 22.523438 27.597656 C 24.777344 29.585938 26.679688 30.199219 27.269531 30.492188 C 27.859375 30.785156 28.203125 30.734375 28.550781 30.347656 C 28.894531 29.957031 30.023438 28.644531 30.417969 28.058594 C 30.8125 27.476562 31.203125 27.574219 31.746094 27.769531 C 32.289062 27.960938 35.191406 29.371094 35.78125 29.664062 C 36.371094 29.957031 36.765625 30.101562 36.914062 30.34375 C 37.0625 30.585938 37.0625 31.753906 36.570312 33.117188 Z M 36.570312 33.117188 "/>
              </g>
            </svg>
            <span class="q-ml-sm">{{ cs.name }}</span>
          </q-btn> -->
        </div>
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