<template>
  <q-card class="max-width">
    <q-card-section class="q-px-none">
      <q-list separator>
        <q-item>
          <q-item-section side>
            <q-icon name="eva-keypad"></q-icon>
          </q-item-section>
          <q-item-section>
            <q-item-label class="text-md">Kategori</q-item-label>
          </q-item-section>
          <q-item-section side>
            <q-btn flat icon="eva-close" dense @click="closeCategory"></q-btn>
          </q-item-section>
        </q-item>
        <q-item v-for="category in categories.data" :key="category.id" clickable @click="handleShowCategory(category.id)">
          <q-item-section avatar>
            <q-avatar>
              <q-img :src="category.src"></q-img>
            </q-avatar>
          </q-item-section>
          <q-item-section>
            <q-item-label>{{ category.title }}</q-item-label>
          </q-item-section>
        </q-item>
      </q-list>

      </q-card-section>
    </q-card>
</template>

<script>
export default {
  computed: {
    categories() {
      return this.$store.state.category.categories
    }
  },
  methods: {
    handleShowCategory(id) {
      this.closeCategory()
      if(id != this.$route.params.id) {
        this.$store.dispatch('product/getProductsByCategory', id)
        this.$router.push({ name: 'ProductCategory', params: { id: id }})
      }
    },
    closeCategory() {
      this.$store.commit('SET_MENU_CATEGORY', false)
    }
  },
  mounted() {
    if(!this.categories.data.length) {
      this.$store.dispatch('category/getCategories')
    }
  }
}
</script>
