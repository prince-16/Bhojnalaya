<template>
    <div>
        <h1>Hello Bhoomika 👋</h1>

        <input v-model="name" placeholder="Enter name" />

        <button @click="sayHello">Click</button>

        <p>{{ message }}</p>
    </div>
</template>

<script>
export default {
    data() {
        return {
            name: '',
            message: ''
        }
    },
    methods: {
        sayHello() {
            this.message = "Hello " + this.name
        }
    }
}
</script>