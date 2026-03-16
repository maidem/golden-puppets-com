<template>
  <div v-editable="blok">
    <NavLink :to="blok.link.cached_url || blok.link.url">
      {{ blok.label }}
    </NavLink>
  </div>
</template>

<script setup>
defineProps({ blok: Object })
</script>
