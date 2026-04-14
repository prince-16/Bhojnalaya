<script setup>
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
})

defineEmits(['open-table'])
</script>

<template>
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

        <button v-for="action in props.quickActions" :key="action.label" class="header-action" :class="{ 'header-action--solid': action.emphasized }">
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
        <button
          v-for="table in section.tables"
          :key="table.id"
          class="table-card"
          :class="`table-card--${table.status}`"
          @click="$emit('open-table', { table, sectionTitle: section.title })"
        >
          <span>{{ table.label }}</span>
        </button>
      </div>
    </section>

    <div class="enquiry-banner">For Inquiry Call or WhatsApp : 9034142334</div>
  </section>
</template>
