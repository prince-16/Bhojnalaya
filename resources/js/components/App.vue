<script setup>
const sections = [
  {
    title: 'Ground Floor',
    tables: Array.from({ length: 10 }, (_, index) => ({
      id: index + 1,
      label: `${index + 1}`,
      status: 'blank',
    })),
  },
  {
    title: 'Basement',
    tables: Array.from({ length: 10 }, (_, index) => ({
      id: index + 11,
      label: `${index + 11}`,
      status: 'blank',
    })),
  },
  {
    title: 'Party Hall',
    tables: [
      { id: 21, label: 'Hall 1', status: 'blank' },
      { id: 22, label: 'Hall 2', status: 'blank' },
    ],
  },
]

const legendItems = [
  { label: 'Blank Table', color: '#eceff5' },
  { label: 'Running Table', color: '#4bb6ff' },
  { label: 'Printed Table', color: '#70c25b' },
  { label: 'Paid Table', color: '#f5c77b' },
  { label: 'Running KOT Table', color: '#ffd34f' },
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
    default:
      return ''
  }
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

        <div class="brand-mark">Petpooja</div>

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

    <section class="content-shell">
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
          <button v-for="table in section.tables" :key="table.id" class="table-card" :class="`table-card--${table.status}`">
            <span>{{ table.label }}</span>
          </button>
        </div>
      </section>

      <div class="enquiry-banner">For Inquiry Call or WhatsApp : 9034142334</div>
    </section>
  </main>
</template>