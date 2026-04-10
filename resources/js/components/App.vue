<script setup>
import { computed, reactive, ref } from 'vue'

const sections = [
  {
    title: 'Ground Floor',
    tables: Array.from({ length: 10 }, (_, index) => ({
      id: index + 1,
      label: `${index + 1}`,
      status: index === 2 ? 'running' : 'blank',
    })),
  },
  {
    title: 'Basement',
    tables: Array.from({ length: 10 }, (_, index) => ({
      id: index + 11,
      label: `${index + 11}`,
      status: index === 4 ? 'printed' : 'blank',
    })),
  },
  {
    title: 'Party Hall',
    tables: [
      { id: 21, label: 'Hall 1', status: 'blank' },
      { id: 22, label: 'Hall 2', status: 'paid' },
    ],
  },
]

const legendItems = [
  { label: 'Blank Table', color: '#eceff5' },
  { label: 'Running Table', color: '#47b7ff' },
  { label: 'Printed Table', color: '#76c35b' },
  { label: 'Paid Table', color: '#f1c67a' },
  { label: 'Running KOT Table', color: '#ffd24a' },
]

const quickActions = [
  { label: 'Delivery', emphasized: false },
  { label: 'Pick Up', emphasized: false },
  { label: '+ Add Table', emphasized: true },
]

const topMenu = [
  { label: 'Bills', icon: 'receipt' },
  { label: 'Orders', icon: 'bag' },
  { label: 'Tables', icon: 'table' },
  { label: 'Apps', icon: 'grid' },
  { label: 'Reports', icon: 'monitor' },
  { label: 'Customers', icon: 'users' },
  { label: 'Alerts', icon: 'bell' },
  { label: 'Profile', icon: 'user' },
]

const categories = [
  {
    id: 'fast-food',
    name: 'Fast Food',
    items: [
      { id: 1, name: 'Aloo Tikki Burger', price: 90, accent: 'green' },
      { id: 2, name: 'Cheese Garlic Bread', price: 140, accent: 'green' },
      { id: 3, name: 'Chicken Angara (Boneless)', price: 220, accent: 'red' },
      { id: 4, name: 'Chilli Mushroom', price: 180, accent: 'green' },
      { id: 5, name: 'Dahi Ke Shole', price: 160, accent: 'green' },
      { id: 6, name: 'Fry Masala Papad', price: 75, accent: 'green' },
      { id: 7, name: 'Green Salad', price: 60, accent: 'green' },
      { id: 8, name: 'Grilled Paneer Sandwich', price: 155, accent: 'green' },
      { id: 9, name: 'Hakka Noodles', price: 170, accent: 'green' },
      { id: 10, name: 'Masala Dosa', price: 130, accent: 'green' },
      { id: 11, name: 'Omlate (3 Eggs)', price: 110, accent: 'yellow' },
      { id: 12, name: 'Open Item', price: 0, accent: 'green' },
      { id: 13, name: 'Oreo Shake', price: 145, accent: 'green' },
      { id: 14, name: 'Paneer Wrap', price: 165, accent: 'green' },
      { id: 15, name: 'Raj Kachodi', price: 95, accent: 'green' },
      { id: 16, name: 'RasMalai', price: 85, accent: 'green' },
      { id: 17, name: 'Salted Lassi', price: 70, accent: 'green' },
      { id: 18, name: 'Spl. Shahi Paneer', price: 240, accent: 'green' },
      { id: 19, name: 'Spring Roll', price: 150, accent: 'green' },
      { id: 20, name: 'Strawberry Mojito', price: 120, accent: 'green' },
      { id: 21, name: 'Sweet Corn Soup', price: 115, accent: 'green' },
      { id: 22, name: 'Tandoori Momos (8 Pcs)', price: 180, accent: 'green' },
      { id: 23, name: 'Tandoori Pasta', price: 190, accent: 'green' },
      { id: 24, name: 'Veg Burger', price: 95, accent: 'green' },
      { id: 25, name: 'Water Bottle', price: 20, accent: 'green' },
    ],
  },
  { id: 'favorites', name: 'Favorite Items', items: [] },
  { id: 'beverages', name: 'Beverages', items: [] },
  { id: 'burgers', name: 'Burgers', items: [] },
  { id: 'egg', name: 'EGG', items: [] },
  { id: 'chicken', name: 'Chicken', items: [] },
  { id: 'chakna', name: 'Chakhna', items: [] },
  { id: 'chinese-snacks', name: 'Chinese Snacks', items: [] },
  { id: 'soup', name: 'Chinse Soups', items: [] },
  { id: 'garlic-bread', name: 'Garlic Bread', items: [] },
  { id: 'gravy', name: 'Gravy Items', items: [] },
  { id: 'wraps', name: 'Hawaiian Wraps', items: [] },
  { id: 'maggie', name: 'Maggie Lover', items: [] },
]

const orderTypes = ['Dine In', 'Delivery', 'Pick Up']
const actionTabs = ['Table', 'Guest', 'Group', 'Notes', 'Order']
const paymentModes = ['Cash', 'Card', 'Due', 'Other', 'Part']

const view = ref('tables')
const selectedTable = ref(null)
const selectedCategoryId = ref(categories[0].id)
const itemSearch = ref('')
const shortCode = ref('')
const selectedOrderType = ref('Dine In')
const selectedPaymentMode = ref('Cash')
const cart = reactive([])
const flags = reactive({
  complimentary: false,
  paid: false,
  loyalty: true,
  feedbackSms: true,
})

const activeCategory = computed(() => categories.find((category) => category.id === selectedCategoryId.value) ?? categories[0])

const filteredItems = computed(() => {
  const query = itemSearch.value.trim().toLowerCase()
  return activeCategory.value.items.filter((item) => {
    const searchTarget = `${item.name} ${item.id}`.toLowerCase()
    return searchTarget.includes(query)
  })
})

const totalAmount = computed(() => cart.reduce((sum, item) => sum + item.price * item.quantity, 0))
const totalQuantity = computed(() => cart.reduce((sum, item) => sum + item.quantity, 0))

function iconPath(icon) {
  switch (icon) {
    case 'receipt':
      return 'M8 3.75A2.25 2.25 0 0 0 5.75 6v12.69c0 .7.81 1.09 1.36.67l1.34-1 1.34 1a.75.75 0 0 0 .9 0l1.34-1 1.34 1a.75.75 0 0 0 .9 0l1.34-1 1.34 1c.55.42 1.36.03 1.36-.67V6A2.25 2.25 0 0 0 16 3.75H8ZM8 5.25h8A.75.75 0 0 1 16.75 6v11.18l-.59-.44a.75.75 0 0 0-.9 0l-1.34 1-1.34-1a.75.75 0 0 0-.9 0l-1.34 1-1.34-1a.75.75 0 0 0-.9 0l-.59.44V6A.75.75 0 0 1 8 5.25Zm1.5 3a.75.75 0 0 1 .75-.75h3.5a.75.75 0 0 1 0 1.5h-3.5a.75.75 0 0 1-.75-.75Zm0 3a.75.75 0 0 1 .75-.75h3.5a.75.75 0 0 1 0 1.5h-3.5a.75.75 0 0 1-.75-.75Z'
    case 'bag':
      return 'M8.75 7.5V6a3.25 3.25 0 1 1 6.5 0v1.5h1.25A1.75 1.75 0 0 1 18.25 9.25v7.5A1.75 1.75 0 0 1 16.5 18.5h-9A1.75 1.75 0 0 1 5.75 16.75v-7.5A1.75 1.75 0 0 1 7.5 7.5h1.25Zm1.5 0h3.5V6a1.75 1.75 0 1 0-3.5 0v1.5Zm-2.75 1.5a.25.25 0 0 0-.25.25v7.5c0 .14.11.25.25.25h9a.25.25 0 0 0 .25-.25v-7.5a.25.25 0 0 0-.25-.25H7.5Z'
    case 'table':
      return 'M6.5 5.5A1.5 1.5 0 0 1 8 4h8a1.5 1.5 0 0 1 1.5 1.5V8A1.5 1.5 0 0 1 16 9.5h-.75v4.75a.75.75 0 0 1-1.5 0V13h-3.5v1.25a.75.75 0 0 1-1.5 0V9.5H8A1.5 1.5 0 0 1 6.5 8V5.5Zm1.5 0V8h8V5.5H8Zm-1.25 6.75a.75.75 0 0 1 .75-.75h9a.75.75 0 0 1 0 1.5h-9a.75.75 0 0 1-.75-.75Z'
    case 'grid':
      return 'M6.5 5.5A1.5 1.5 0 0 1 8 4h2a1.5 1.5 0 0 1 1.5 1.5v2A1.5 1.5 0 0 1 10 9H8a1.5 1.5 0 0 1-1.5-1.5v-2Zm6 0A1.5 1.5 0 0 1 14 4h2a1.5 1.5 0 0 1 1.5 1.5v2A1.5 1.5 0 0 1 16 9h-2a1.5 1.5 0 0 1-1.5-1.5v-2Zm-6 6A1.5 1.5 0 0 1 8 10h2a1.5 1.5 0 0 1 1.5 1.5v2A1.5 1.5 0 0 1 10 15H8a1.5 1.5 0 0 1-1.5-1.5v-2Zm6 0A1.5 1.5 0 0 1 14 10h2a1.5 1.5 0 0 1 1.5 1.5v2A1.5 1.5 0 0 1 16 15h-2a1.5 1.5 0 0 1-1.5-1.5v-2Z'
    case 'monitor':
      return 'M7.5 5A2.5 2.5 0 0 0 5 7.5v5A2.5 2.5 0 0 0 7.5 15h2.75v1.5H8.5a.75.75 0 0 0 0 1.5h7a.75.75 0 0 0 0-1.5h-1.75V15h2.75A2.5 2.5 0 0 0 19 12.5v-5A2.5 2.5 0 0 0 16.5 5h-9Zm0 1.5h9A1 1 0 0 1 17.5 7.5v5a1 1 0 0 1-1 1h-9a1 1 0 0 1-1-1v-5a1 1 0 0 1 1-1Z'
    case 'users':
      return 'M12 10a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Zm-4.75 7a4.75 4.75 0 0 1 9.5 0 .75.75 0 0 1-1.5 0 3.25 3.25 0 0 0-6.5 0 .75.75 0 0 1-1.5 0ZM6.5 11.25a2 2 0 1 0-1.97-2.33.75.75 0 0 1-1.48-.23 3.5 3.5 0 1 1 6.41 2.35.75.75 0 1 1-1.28-.79A1.98 1.98 0 0 0 6.5 11.25Zm11 0a2 2 0 1 0-1.68-.92.75.75 0 0 1-1.27.8 3.5 3.5 0 1 1 6.2-2.14.75.75 0 1 1-1.48.22 1.98 1.98 0 0 0-1.77 2.04Z'
    case 'bell':
      return 'M12 4.5a3.5 3.5 0 0 0-3.5 3.5v1.34c0 .4-.12.8-.34 1.13L7 12.1V13h10v-.9l-1.16-1.63a2 2 0 0 1-.34-1.13V8A3.5 3.5 0 0 0 12 4.5Zm-5 8.75v-1.15l.94-1.32c.4-.57.61-1.25.61-1.94V8a4.5 4.5 0 1 1 9 0v.84c0 .69.21 1.37.61 1.94l.94 1.32v1.15a.75.75 0 0 1-.75.75H15.8a3.05 3.05 0 0 1-5.6 0H7.75a.75.75 0 0 1-.75-.75Zm4.78 1.25a1.55 1.55 0 0 0 2.44 0h-2.44Z'
    case 'user':
      return 'M12 10a2.75 2.75 0 1 0 0-5.5A2.75 2.75 0 0 0 12 10Zm-4.5 7a4.5 4.5 0 0 1 9 0 .75.75 0 0 1-1.5 0 3 3 0 0 0-6 0 .75.75 0 0 1-1.5 0Z'
    case 'fork':
      return 'M9 4.5a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 1 1-3 0V6A1.5 1.5 0 0 1 9 4.5Zm6 0a1.5 1.5 0 0 1 1.5 1.5V9a1.5 1.5 0 1 1-3 0V6A1.5 1.5 0 0 1 15 4.5ZM8.25 11.25a.75.75 0 0 1 .75.75v6a.75.75 0 0 1-1.5 0v-6a.75.75 0 0 1 .75-.75Zm7.5 0a.75.75 0 0 1 .75.75v6a.75.75 0 0 1-1.5 0v-6a.75.75 0 0 1 .75-.75Z'
    case 'guest':
      return 'M12 4.75a2.75 2.75 0 1 1 0 5.5 2.75 2.75 0 0 1 0-5.5Zm-4.75 10a4.75 4.75 0 0 1 9.5 0 .75.75 0 0 1-1.5 0 3.25 3.25 0 0 0-6.5 0 .75.75 0 0 1-1.5 0Z'
    case 'group':
      return 'M7.75 6.5a2.25 2.25 0 1 1 0 4.5 2.25 2.25 0 0 1 0-4.5Zm8.5 0a2.25 2.25 0 1 1 0 4.5 2.25 2.25 0 0 1 0-4.5Zm-4.25 5.25a2.75 2.75 0 1 1 0-5.5 2.75 2.75 0 0 1 0 5.5Zm-6 5.5a.75.75 0 0 1-.75-.75 3.75 3.75 0 0 1 4.88-3.58 5.62 5.62 0 0 0-1.31 2.7.75.75 0 1 1-1.48-.24A2.25 2.25 0 0 0 6 16.5c0 .41-.34.75-.75.75Zm12 0a.75.75 0 0 1-.75-.75c0-.78-.18-1.51-.5-2.17a.75.75 0 1 1 1.35-.66c.43.87.65 1.83.65 2.83a.75.75 0 0 1-.75.75Zm-8.25-.75a3.25 3.25 0 0 1 6.5 0 .75.75 0 0 1-1.5 0 1.75 1.75 0 0 0-3.5 0 .75.75 0 0 1-1.5 0Z'
    case 'note':
      return 'M7.5 4.5A1.5 1.5 0 0 0 6 6v12a1.5 1.5 0 0 0 1.5 1.5h9A1.5 1.5 0 0 0 18 18V9.31a1.5 1.5 0 0 0-.44-1.06l-3.81-3.81A1.5 1.5 0 0 0 12.69 4.5H7.5Zm0 1.5h5V9a1 1 0 0 0 1 1h3v8a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V6a.5.5 0 0 1 .5-.5Zm6.5.56L16.94 9H13.5a.5.5 0 0 1-.5-.5V6.06Z'
    case 'order':
      return 'M7.5 5.25A2.25 2.25 0 0 1 9.75 3h4.5A2.25 2.25 0 0 1 16.5 5.25v13.5A2.25 2.25 0 0 1 14.25 21h-4.5A2.25 2.25 0 0 1 7.5 18.75V5.25Zm1.5 0v13.5c0 .41.34.75.75.75h4.5a.75.75 0 0 0 .75-.75V5.25a.75.75 0 0 0-.75-.75h-4.5a.75.75 0 0 0-.75.75Zm1.75 2.5a.75.75 0 0 1 .75-.75h1a.75.75 0 0 1 0 1.5h-1a.75.75 0 0 1-.75-.75Z'
    default:
      return ''
  }
}

function openTable(table, sectionTitle) {
  selectedTable.value = { ...table, sectionTitle }
  view.value = 'order'
}

function goBackToTables() {
  view.value = 'tables'
}

function selectCategory(categoryId) {
  selectedCategoryId.value = categoryId
}

function addItem(item) {
  const existingItem = cart.find((cartItem) => cartItem.id === item.id)

  if (existingItem) {
    existingItem.quantity += 1
    return
  }

  cart.push({
    id: item.id,
    name: item.name,
    price: item.price,
    quantity: 1,
  })
}

function updateQuantity(itemId, delta) {
  const cartItem = cart.find((item) => item.id === itemId)

  if (!cartItem) {
    return
  }

  cartItem.quantity += delta

  if (cartItem.quantity <= 0) {
    const index = cart.findIndex((item) => item.id === itemId)
    cart.splice(index, 1)
  }
}

function formatCurrency(value) {
  return new Intl.NumberFormat('en-IN').format(value)
}
</script>

<template>
  <main class="petpooja-screen">
    <header class="topbar">
      <div class="topbar__brand">
        <button class="icon-button icon-button--menu" aria-label="Open menu">
          <span></span>
          <span></span>
          <span></span>
        </button>

        <div class="brand-mark">Bp</div>

        <button class="primary-button">New Order</button>

        <label class="search-box">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M10.5 5a5.5 5.5 0 1 0 3.47 9.77l3.63 3.63a.75.75 0 1 0 1.06-1.06l-3.63-3.63A5.5 5.5 0 0 0 10.5 5Zm-4 5.5a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" />
          </svg>
          <input type="text" placeholder="Bill No" />
        </label>
      </div>

      <div class="topbar__actions">
        <div class="support-card">
          <div class="support-card__icon">
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <path d="M6.62 10.79a15.05 15.05 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1-.24c1.12.37 2.31.56 3.54.56a1 1 0 0 1 1 1V20a1 1 0 0 1-1 1C10.4 21 3 13.6 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.23.19 2.42.56 3.54a1 1 0 0 1-.24 1l-2.2 2.25Z" />
            </svg>
          </div>
          <div>
            <p>Call For Support</p>
            <strong>9099912483</strong>
          </div>
        </div>

        <nav class="menu-icons" aria-label="Main navigation">
          <button v-for="item in topMenu" :key="item.label" class="menu-icon-button" :aria-label="item.label">
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <path :d="iconPath(item.icon)" />
            </svg>
          </button>
        </nav>

        <button class="menu-icon-button power-button" aria-label="Logout">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M12 3.75a.75.75 0 0 1 .75.75v6.69a.75.75 0 0 1-1.5 0V4.5a.75.75 0 0 1 .75-.75Zm4.62 2.52a.75.75 0 0 1 1.06.04 8 8 0 1 1-11.36 0 .75.75 0 1 1 1.1 1.02 6.5 6.5 0 1 0 9.22 0 .75.75 0 0 1-.02-1.06Z" />
          </svg>
        </button>
      </div>
    </header>

    <section v-if="view === 'tables'" class="content-shell">
      <div class="content-header">
        <div>
          <p class="eyebrow">Table View</p>
        </div>

        <div class="content-header__right">
          <button class="refresh-button" aria-label="Refresh tables">
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <path d="M17.65 6.35A7.95 7.95 0 0 0 12 4V1.75a.75.75 0 0 0-1.28-.53L7.97 3.97a.75.75 0 0 0 0 1.06l2.75 2.75A.75.75 0 0 0 12 7.25V5.5a6.5 6.5 0 1 1-6.37 7.8.75.75 0 0 0-1.46.34A8 8 0 1 0 17.65 6.35Z" />
            </svg>
          </button>

          <button v-for="action in quickActions" :key="action.label" class="header-action" :class="{ 'header-action--solid': action.emphasized }">
            {{ action.label }}
          </button>
        </div>
      </div>

      <div class="toolbar">
        <div class="toolbar__actions">
          <button class="secondary-button">+ Table Reservation</button>
          <button class="secondary-button">+ Contactless</button>
        </div>

        <div class="toolbar__toggles">
          <label class="toggle-card">
            <input type="radio" checked />
            <span>Move KOT / Items</span>
          </label>

          <label class="toggle-card toggle-card--muted">
            <input type="radio" />
            <span>Blank Table</span>
          </label>

          <div class="legend">
            <span v-for="item in legendItems" :key="item.label" class="legend__item">
              <i :style="{ backgroundColor: item.color }"></i>
              {{ item.label }}
            </span>
          </div>
        </div>
      </div>

      <section v-for="section in sections" :key="section.title" class="floor-block">
        <h2>{{ section.title }}</h2>

        <div class="table-grid" :class="{ 'table-grid--hall': section.title === 'Party Hall' }">
          <button
            v-for="table in section.tables"
            :key="table.id"
            class="table-card"
            :class="`table-card--${table.status}`"
            @click="openTable(table, section.title)"
          >
            <span>{{ table.label }}</span>
          </button>
        </div>
      </section>

      <div class="enquiry-banner">For Inquiry Call or WhatsApp : 9034142334</div>
    </section>

    <section v-else class="order-layout">
      <aside class="category-sidebar">
        <button class="category-sidebar__active">
          <span>{{ activeCategory.name }}</span>
          <span class="category-sidebar__chevron">▾</span>
        </button>

        <button
          v-for="category in categories"
          :key="category.id"
          class="category-link"
          :class="{ 'category-link--selected': selectedCategoryId === category.id }"
          @click="selectCategory(category.id)"
        >
          {{ category.name }}
        </button>
      </aside>

      <section class="order-board">
        <div class="order-filters">
          <label class="search-box search-box--wide">
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <path d="M10.5 5a5.5 5.5 0 1 0 3.47 9.77l3.63 3.63a.75.75 0 1 0 1.06-1.06l-3.63-3.63A5.5 5.5 0 0 0 10.5 5Zm-4 5.5a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" />
            </svg>
            <input v-model="itemSearch" type="text" placeholder="Search item" />
          </label>

          <input v-model="shortCode" class="short-code-input" type="text" placeholder="Short Code" />
        </div>

        <div class="item-grid">
          <button
            v-for="item in filteredItems"
            :key="item.id"
            class="menu-item-card"
            :class="`menu-item-card--${item.accent}`"
            @click="addItem(item)"
          >
            <span>{{ item.name }}</span>
            <strong v-if="item.price > 0">{{ formatCurrency(item.price) }}</strong>
          </button>
        </div>

        <div v-if="filteredItems.length === 0" class="item-grid__empty">
          No items available in this category.
        </div>
      </section>

      <aside class="cart-panel">
        <div class="order-type-tabs">
          <button
            v-for="type in orderTypes"
            :key="type"
            class="order-type-tab"
            :class="{ 'order-type-tab--active': selectedOrderType === type }"
            @click="selectedOrderType = type"
          >
            {{ type }}
          </button>
        </div>

        <div class="cart-top-meta">
          <div class="action-strip">
            <button v-for="tab in actionTabs" :key="tab" class="action-strip__button">
              <svg viewBox="0 0 24 24" aria-hidden="true">
                <path :d="iconPath(tab === 'Table' ? 'fork' : tab === 'Guest' ? 'guest' : tab === 'Group' ? 'group' : tab === 'Notes' ? 'note' : 'order')" />
              </svg>
              <span>{{ tab }}</span>
            </button>
          </div>

          <div class="selected-table-card">
            <div>
              <strong>{{ selectedTable?.sectionTitle }}</strong>
              <p>Table {{ selectedTable?.label }}</p>
            </div>
            <button class="back-link" @click="goBackToTables">Change</button>
          </div>
        </div>

        <div class="cart-table-header">
          <span>ITEMS</span>
          <span>CHECK ITEMS</span>
          <span>QTY.</span>
          <span>PRICE</span>
        </div>

        <div v-if="cart.length === 0" class="empty-cart">
          <div class="empty-cart__icon">🍽</div>
          <strong>No Item Selected</strong>
          <p>Please Select Item from Left Menu Item</p>
        </div>

        <div v-else class="cart-items">
          <article v-for="item in cart" :key="item.id" class="cart-item">
            <div>
              <strong>{{ item.name }}</strong>
              <p>Selected from {{ activeCategory.name }}</p>
            </div>
            <label class="cart-check">
              <input type="checkbox" />
            </label>
            <div class="quantity-stepper">
              <button @click="updateQuantity(item.id, -1)">-</button>
              <span>{{ item.quantity }}</span>
              <button @click="updateQuantity(item.id, 1)">+</button>
            </div>
            <strong>{{ formatCurrency(item.price * item.quantity) }}</strong>
          </article>
        </div>

        <div class="cart-controls">
          <div class="cart-controls__row">
            <button class="cart-cta cart-cta--red">Bogo Offer</button>
            <button class="cart-cta cart-cta--outline">Split</button>
            <label class="check-flag">
              <input v-model="flags.complimentary" type="checkbox" />
              <span>Complimentary</span>
            </label>
            <div class="cart-total">Total <strong>{{ formatCurrency(totalAmount) }}</strong></div>
          </div>

          <div class="cart-controls__row cart-controls__row--payments">
            <label v-for="mode in paymentModes" :key="mode" class="payment-radio">
              <input v-model="selectedPaymentMode" type="radio" :value="mode" />
              <span>{{ mode }}</span>
            </label>
          </div>

          <div class="cart-controls__row cart-controls__row--flags">
            <label class="check-flag">
              <input v-model="flags.paid" type="checkbox" />
              <span>It's Paid</span>
            </label>
            <label class="check-flag">
              <input v-model="flags.loyalty" type="checkbox" />
              <span>Loyalty</span>
            </label>
            <label class="check-flag">
              <input v-model="flags.feedbackSms" type="checkbox" />
              <span>Send Feedback SMS</span>
            </label>
          </div>

          <div class="bottom-actions">
            <button class="bottom-actions__primary">Save</button>
            <button class="bottom-actions__primary">Save & Print</button>
            <button class="bottom-actions__primary">Save & Bill</button>
            <button class="bottom-actions__dark">KOT</button>
            <button class="bottom-actions__dark">KOT & Print</button>
            <button class="bottom-actions__ghost">Hold</button>
          </div>
        </div>
      </aside>
    </section>
  </main>
</template>
