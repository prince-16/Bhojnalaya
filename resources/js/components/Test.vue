<script setup>
import { ref } from 'vue'

const count = ref(0)

function increment() {
  count.value++
}

function reset() {
  count.value = 0
}
</script>

<template>
  <section class="counter-card">
    <h2>Count: {{ count }}</h2>

    <div class="actions">
      <button type="button" @click="increment">Increase</button>
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

h2 {
  margin: 0 0 16px;
  font-size: 2rem;
}

.actions {
  display: flex;
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