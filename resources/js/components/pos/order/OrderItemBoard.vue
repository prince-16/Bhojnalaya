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
  menuItemsLoading: {
    type: Boolean,
    default: false,
  },
  menuItemsError: {
    type: String,
    default: '',
  },
})

defineEmits(['update:item-search', 'update:short-code', 'add-item', 'add-menu-item', 'edit-menu-item', 'delete-menu-item'])
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

      <div class="short-code-group">
        <input
          :value="props.shortCode"
          class="short-code-input"
          type="text"
          placeholder="Short Code"
          @input="$emit('update:short-code', $event.target.value)"
        />
        <AppButton class="menu-action-pill menu-action-pill--icon menu-action-pill--primary" aria-label="Add menu item" title="Add menu item" @click="$emit('add-menu-item')">
          <svg viewBox="0 0 20 20" aria-hidden="true">
            <path d="M10 3.75a.75.75 0 0 1 .75.75v4.75h4.75a.75.75 0 0 1 0 1.5h-4.75v4.75a.75.75 0 0 1-1.5 0v-4.75H4.5a.75.75 0 0 1 0-1.5h4.75V4.5a.75.75 0 0 1 .75-.75Z" />
          </svg>
        </AppButton>
      </div>
    </div>

    <p v-if="props.menuItemsLoading" class="item-grid__status">Loading menu items...</p>
    <p v-else-if="props.menuItemsError" class="item-grid__status item-grid__status--error">{{ props.menuItemsError }}</p>

    <div class="item-grid">
      <div
        v-for="item in props.filteredItems"
        :key="item.id"
        class="menu-item-card-wrap"
      >
        <AppButton
          class="menu-item-card"
          :class="`menu-item-card--${item.accent}`"
          @click="$emit('add-item', item)"
        >
          <span>{{ item.name }}</span>
          <strong v-if="item.price > 0">{{ props.formatCurrency(item.price) }}</strong>
        </AppButton>

        <div class="menu-item-actions">
          <button class="menu-item-icon-btn menu-item-icon-btn--edit" title="Edit item" @click.stop="$emit('edit-menu-item', item)">
            <svg viewBox="0 0 20 20" fill="currentColor" width="13" height="13" aria-hidden="true">
              <path d="M13.586 3.586a2 2 0 1 1 2.828 2.828l-.793.793-2.828-2.828.793-.793ZM11.379 5.793 3 14.172V17h2.828l8.38-8.379-2.83-2.828Z" />
            </svg>
          </button>
          <button class="menu-item-icon-btn menu-item-icon-btn--delete" title="Delete item" @click.stop="$emit('delete-menu-item', item)">
            <svg viewBox="0 0 20 20" fill="currentColor" width="13" height="13" aria-hidden="true">
              <path fill-rule="evenodd" d="M9 2a1 1 0 0 0-.894.553L7.382 4H4a1 1 0 0 0 0 2v10a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V6a1 1 0 1 0 0-2h-3.382l-.724-1.447A1 1 0 0 0 11 2H9ZM7 8a1 1 0 0 1 2 0v6a1 1 0 1 1-2 0V8Zm5-1a1 1 0 0 0-1 1v6a1 1 0 1 0 2 0V8a1 1 0 0 0-1-1Z" clip-rule="evenodd" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <div v-if="props.filteredItems.length === 0" class="item-grid__empty">
      No items available in this category.
    </div>
  </section>
</template>
