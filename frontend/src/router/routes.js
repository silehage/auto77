
const routes = [
  {
    path: '/',
    component: () => import('layouts/FrontLayout.vue'),
    children: [
      { path: '', name: 'Home', component: () => import('pages/Index.vue')},
      { path: 'favorite', name: 'ProductFavorite', component: () => import('pages/Product/Favorite.vue')},
      { path: 'products', name: 'ProductIndex', component: () => import('pages/Product/Index.vue') },
      { path: 'gallery', name: 'GalleryShow', component: () => import('pages/Gallery/Show.vue') },
      { path: 'products/category/:id', name: 'ProductCategory', component: () => import('pages/Product/ByCategory.vue') },
      { path: 'search-product', name: 'ProductSearch', component: () => import('src/pages/Search/SearchProduct.vue') },
      { path: 'posts', name: 'FrontPostIndex', component: () => import('src/pages/Post/FrontPostIndex.vue') },
      { path: 'post/:slug', name: 'FrontPostShow', component: () => import('src/pages/Post/FrontPostShow.vue') },
    ]
  },
  {
    path: '/product',
    component: () => import('src/layouts/BlankLayout.vue'),
    children:[
      {
        path: ':slug',
        name: 'ProductShow',
        component: () => import('pages/Product/Show.vue')
      }
    ]
  },
  { 
    path: '/p',
    component: () => import('src/layouts/BlankLayout.vue'),
    children: [
      { path: 'login', name: 'Login', component: () => import('src/pages/Auth/Login.vue')},
      { path: 'register', name: 'Register', component: () => import('src/pages/Auth/Register.vue')},
      { path: 'forgot-password', name: 'ForgotPassword', component: () => import('src/pages/Auth/ForgotPassword.vue')},
      { path: 'reset-password', name: 'ResetPassword', component: () => import('src/pages/Auth/ResetPassword.vue')},
    ]
  },
  {
    path: '/form',
    component: () => import('layouts/FormLayout.vue'),
    meta: { requiresAdmin: true },
    children: [
      { path: 'product/create', name: 'ProductCreate', component: () => import('pages/Product/Create.vue') },
      { path: 'product/edit/:id', name: 'ProductEdit', component: () => import('pages/Product/Edit.vue')},
      { path: 'category/form', name: 'CategoryForm', component: () => import('pages/Category/Form.vue')},
      { path: 'category/edit/:id', name: 'CategoryEdit', component: () => import('pages/Category/Edit.vue')},
      { path: 'post/create', name: 'PostCreate', component: () => import('pages/Post/Create.vue')},
      { path: 'post/edit/:id', name: 'PostEdit', component: () => import('pages/Post/Edit.vue')},
      { path: 'config', name: 'Config', component: () => import('pages/Config/Index.vue')},
    ]
  },
  {
    path: '/me',
    component: () => import('layouts/CustomerLayout.vue'),
    meta: { requiresCustomer: true },
    children: [
      { path: '', name: 'CustomerAccount', component: () => import('src/pages/Customer/CustomerAccount.vue') },
      { path: 'edit', name: 'CustomerAccountEdit', component: () => import('src/pages/Customer/CustomerAccountEdit.vue') },
    ]
  },
  {
    path: '/admin',
    component: () => import('layouts/AdminLayout.vue'),
    meta: { requiresAdmin: true },
    children: [
      { path: 'account', name: 'Account', component: () => import('src/pages/Account/Index.vue') },
      { path: 'settings', name: 'Settings', component: () => import('src/pages/Dashboard/Settings.vue') },
      { path: 'slider', name: 'Slider', component: () => import('src/pages/Slider/Index.vue')},
      { path: 'gallery', name: 'Gallery', component: () => import('src/pages/Gallery/Index.vue')},
      { path: 'store', name: 'Shop', component: () => import('src/pages/Shop/Index.vue')},
      { path: 'categories', name: 'CategoryIndex', component: () => import('pages/Category/Index.vue')},
      { path: 'products', name: 'AdminProductIndex', component: () => import('pages/Product/AdminProductIndex.vue')},
      { path: 'posts', name: 'AdminPostIndex', component: () => import('src/pages/Post/AdminPostIndex.vue')},
      { path: 'blocks', name: 'AdminBlockIndex', component: () => import('pages/Block/Index.vue')},
      { path: 'cs', name: 'CustomerServices', component: () => import('pages/Account/CustomerServices.vue')},
      { path: 'users', name: 'UserList', component: () => import('pages/Account/UserList.vue')},
      { path: 'promo', name: 'PromoIndex', component: () => import('pages/Promo/Index.vue')},
      { path: 'promo/:id', name: 'PromoDetail', component: () => import('pages/Promo/Detail.vue')},
    ]
  },
  // Always leave this as last one,
  // but you can also remove it
  {
    path: '*',
    component: () => import('pages/Error404.vue')
  }
]

export default routes
