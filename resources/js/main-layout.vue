<template>
 <headerLayout v-if=(token)></headerLayout>
  <router-view />
</template>

<script setup lang="ts">
import headerLayout from './components/header-layout.vue';
import '@tabler/core/dist/css/tabler.min.css';
import '@tabler/core/dist/js/tabler.min.js';
const token = localStorage.getItem('token');
</script>

