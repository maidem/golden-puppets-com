<template>
  <div v-editable="blok" class="py-24 text-center bg-gray-50">
    <BaseHeading tag="h1" size="xl" class="text-[#00b3b0]">
      {{ blok.headline }}
    </BaseHeading>
  </div>
</template>

<script setup>
defineProps({ blok: Object })
</script>
