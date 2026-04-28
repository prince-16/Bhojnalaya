<script setup>
import AppButton from '../ui/AppButton.vue'

const props = defineProps({
  topMenu: {
    type: Array,
    required: true,
  },
  iconPath: {
    type: Function,
    required: true,
  },
})

defineEmits(['new-order', 'menu-select'])
</script>

<template>
  <header class="topbar">
    <div class="topbar__brand">
      <AppButton class="icon-button icon-button--menu" aria-label="Open menu">
        <span></span>
        <span></span>
        <span></span>
      </AppButton>

      <div class="brand-mark">Bp</div>

      <AppButton class="primary-button" @click="$emit('new-order')">New Order</AppButton>

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
        <AppButton v-for="item in props.topMenu" :key="item.label" class="menu-icon-button" :aria-label="item.label" :data-tooltip="item.label" @click="$emit('menu-select', item)">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path :d="props.iconPath(item.icon)" />
          </svg>
        </AppButton>
      </nav>

      <AppButton class="menu-icon-button power-button" aria-label="Logout" data-tooltip="Logout">
        <svg viewBox="0 0 24 24" aria-hidden="true">
          <path d="M12 3.75a.75.75 0 0 1 .75.75v6.69a.75.75 0 0 1-1.5 0V4.5a.75.75 0 0 1 .75-.75Zm4.62 2.52a.75.75 0 0 1 1.06.04 8 8 0 1 1-11.36 0 .75.75 0 1 1 1.1 1.02 6.5 6.5 0 1 0 9.22 0 .75.75 0 0 1-.02-1.06Z" />
        </svg>
      </AppButton>
    </div>
  </header>
</template>
