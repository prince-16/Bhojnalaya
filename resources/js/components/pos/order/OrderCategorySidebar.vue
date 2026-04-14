<script setup>
import AppButton from '../../ui/AppButton.vue'

const props = defineProps({
  categories: {
    type: Array,
    required: true,
  },
  selectedCategoryId: {
    type: String,
    required: true,
  },
  activeCategory: {
    type: Object,
    required: true,
  },
})

defineEmits(['select-category'])
</script>

<template>
  <aside class="category-sidebar">
    <AppButton class="category-sidebar__active">
      <span>{{ props.activeCategory.name }}</span>
      <span class="category-sidebar__chevron">▾</span>
    </AppButton>

    <AppButton
      v-for="category in props.categories"
      :key="category.id"
      class="category-link"
      :class="{ 'category-link--selected': props.selectedCategoryId === category.id }"
      @click="$emit('select-category', category.id)"
    >
      {{ category.name }}
    </AppButton>
  </aside>
</template>
