<template>
  <div v-editable="blok">
    <FeatureCard :title="blok.name" />
  </div>
</template>

<script setup>
defineProps({ blok: Object })
</script>
