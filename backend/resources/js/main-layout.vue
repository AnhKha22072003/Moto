<template>
 <headerLayout v-if=(token)></headerLayout>
  <router-view />
</template>

<script setup lang="ts">
import headerLayout from './components/header-layout.vue';
const token = localStorage.getItem('token');
</script>
