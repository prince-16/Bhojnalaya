<script setup>
import { computed, ref } from 'vue'

const count = ref(0)

const message = computed(() => {
  return count.value === 0 ? 'Start clicking the buttons.' : `Current count is ${count.value}.`
})

function increment() {
  count.value += 1
}

function decrement() {
  count.value -= 1
}

function reset() {
  count.value = 0
}
</script>

<template>
  <section class="counter-card">
    <p class="label">Counter value</p>
    <h2>{{ count }}</h2>
    <p class="message">{{ message }}</p>

    <div class="actions">
      <button type="button" @click="decrement">-1</button>
      <button type="button" @click="increment">+1</button>
      <button type="button" class="secondary" @click="reset">Reset</button>
    </div>
  </section>
</template>

<style scoped>
.counter-card {
  padding: 20px;
  border: 1px solid #cbd5e1;
  border-radius: 16px;
  background: #f8fafc;
}

.label {
  margin: 0;
  font-size: 0.9rem;
  color: #64748b;
}

h2 {
  margin: 8px 0;
  font-size: 3rem;
}

.message {
  margin: 0 0 16px;
  color: #334155;
}

.actions {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

button {
  padding: 10px 14px;
  border: 0;
  border-radius: 10px;
  background: #2563eb;
  color: #fff;
  cursor: pointer;
}

.secondary {
  background: #0f172a;
}
</style>