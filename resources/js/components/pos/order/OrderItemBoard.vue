<script setup>
import AppButton from '../../ui/AppButton.vue'

const props = defineProps({
  itemSearch: {
    type: String,
    required: true,
  },
  shortCode: {
    type: String,
    required: true,
  },
  filteredItems: {
    type: Array,
    required: true,
  },
  formatCurrency: {
    type: Function,
    required: true,
  },
})

defineEmits(['update:item-search', 'update:short-code', 'add-item'])
</script>

<template>
  <section class="order-board">
    <div class="order-filters">
      <label class="search-box search-box--wide">
        <svg viewBox="0 0 24 24" aria-hidden="true">
          <path d="M10.5 5a5.5 5.5 0 1 0 3.47 9.77l3.63 3.63a.75.75 0 1 0 1.06-1.06l-3.63-3.63A5.5 5.5 0 0 0 10.5 5Zm-4 5.5a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" />
        </svg>
        <input :value="props.itemSearch" type="text" placeholder="Search item" @input="$emit('update:item-search', $event.target.value)" />
      </label>

      <input
        :value="props.shortCode"
        class="short-code-input"
        type="text"
        placeholder="Short Code"
        @input="$emit('update:short-code', $event.target.value)"
      />
    </div>

    <div class="item-grid">
      <AppButton
        v-for="item in props.filteredItems"
        :key="item.id"
        class="menu-item-card"
        :class="`menu-item-card--${item.accent}`"
        @click="$emit('add-item', item)"
      >
        <span>{{ item.name }}</span>
        <strong v-if="item.price > 0">{{ props.formatCurrency(item.price) }}</strong>
      </AppButton>
    </div>

    <div v-if="props.filteredItems.length === 0" class="item-grid__empty">
      No items available in this category.
    </div>
  </section>
</template>
