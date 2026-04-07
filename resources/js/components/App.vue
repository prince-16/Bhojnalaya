<script setup>
import Test from './Test.vue'
</script>

<template>
  <main class="page">
    <h1>Simple Vue Counter</h1>
    <p>This is a small example to show why Vue is useful for interactive UI.</p>
    <Test />
  </main>
</template>

<style scoped>
.page {
  width: min(520px, calc(100vw - 32px));
  margin: 48px auto;
  padding: 24px;
  border-radius: 18px;
  background: #ffffff;
  box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
  color: #0f172a;
  font-family: Arial, sans-serif;
}

h1 {
  margin: 0 0 10px;
  font-size: 2rem;
}

p {
  margin: 0 0 20px;
  color: #475569;
}
</style>