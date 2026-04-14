<script setup>
import { computed, useAttrs } from 'vue'

defineOptions({
  inheritAttrs: false,
})

const props = defineProps({
  type: {
    type: String,
    default: 'button',
  },
})

const attrs = useAttrs()

const forwardedAttrs = computed(() => {
  const { class: _className, ...rest } = attrs
  return rest
})
</script>

<template>
  <button :type="props.type" class="app-button" :class="attrs.class" v-bind="forwardedAttrs">
    <slot />
  </button>
</template>
