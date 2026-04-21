<script setup>
import AppButton from '../ui/AppButton.vue'

const props = defineProps({
  quickActions: {
    type: Array,
    required: true,
  },
  legendItems: {
    type: Array,
    required: true,
  },
  sections: {
    type: Array,
    required: true,
  },
  loading: {
    type: Boolean,
    default: false,
  },
  error: {
    type: String,
    default: '',
  },
})

defineEmits(['open-table', 'refresh', 'quick-action', 'edit-table', 'delete-table'])
</script>

<template>
  <section class="content-shell">
    <div class="content-header">
      <div>
        <p class="eyebrow">Table View</p>
      </div>

      <div class="content-header__right">
        <AppButton class="refresh-button" aria-label="Refresh tables" @click="$emit('refresh')">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M17.65 6.35A7.95 7.95 0 0 0 12 4V1.75a.75.75 0 0 0-1.28-.53L7.97 3.97a.75.75 0 0 0 0 1.06l2.75 2.75A.75.75 0 0 0 12 7.25V5.5a6.5 6.5 0 1 1-6.37 7.8.75.75 0 0 0-1.46.34A8 8 0 1 0 17.65 6.35Z" />
          </svg>
        </AppButton>

        <AppButton v-for="action in props.quickActions" :key="action.label" class="header-action" :class="{ 'header-action--solid': action.emphasized }" @click="$emit('quick-action', action)">
          {{ action.label }}
        </AppButton>
      </div>
    </div>

    <p v-if="props.loading" class="eyebrow">Loading tables...</p>
    <p v-else-if="props.error" class="eyebrow">{{ props.error }}</p>

    <div class="toolbar">
      <div class="toolbar__actions">
        <AppButton class="secondary-button">+ Table Reservation</AppButton>
        <AppButton class="secondary-button">+ Contactless</AppButton>
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
          <span v-for="item in props.legendItems" :key="item.label" class="legend__item">
            <i :style="{ backgroundColor: item.color }"></i>
            {{ item.label }}
          </span>
        </div>
      </div>
    </div>

    <section v-for="section in props.sections" :key="section.title" class="floor-block">
      <h2>{{ section.title }}</h2>

      <div class="table-grid" :class="{ 'table-grid--hall': section.title === 'Party Hall' }">
        <div
          v-for="table in section.tables"
          :key="table.id"
          class="table-card-wrapper"
        >
          <AppButton
            class="table-card"
            :class="`table-card--${table.status}`"
            @click="$emit('open-table', { table, sectionTitle: section.title })"
          >
            <span>{{ table.label }}</span>
          </AppButton>
          <div class="table-card-actions">
            <button class="table-action-btn table-action-btn--edit" title="Edit Table" @click.stop="$emit('edit-table', table)">
              <svg viewBox="0 0 20 20" fill="currentColor" width="13" height="13" aria-hidden="true">
                <path d="M13.586 3.586a2 2 0 1 1 2.828 2.828l-.793.793-2.828-2.828.793-.793ZM11.379 5.793 3 14.172V17h2.828l8.38-8.379-2.83-2.828Z" />
              </svg>
            </button>
            <button class="table-action-btn table-action-btn--delete" title="Delete Table" @click.stop="$emit('delete-table', table)">
              <svg viewBox="0 0 20 20" fill="currentColor" width="13" height="13" aria-hidden="true">
                <path fill-rule="evenodd" d="M9 2a1 1 0 0 0-.894.553L7.382 4H4a1 1 0 0 0 0 2v10a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V6a1 1 0 1 0 0-2h-3.382l-.724-1.447A1 1 0 0 0 11 2H9ZM7 8a1 1 0 0 1 2 0v6a1 1 0 1 1-2 0V8Zm5-1a1 1 0 0 0-1 1v6a1 1 0 1 0 2 0V8a1 1 0 0 0-1-1Z" clip-rule="evenodd" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </section>

    <p v-if="!props.loading && !props.sections.length" class="eyebrow">No dining tables found. Use + Add Table to create one.</p>

    <div class="enquiry-banner">For Inquiry Call or WhatsApp : 9034142334</div>
  </section>
</template>
