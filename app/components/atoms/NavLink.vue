<template>
  <NuxtLink
    :to="to"
    class="text-gray-600 hover:text-[#00b3b0] transition-colors duration-300 font-medium px-4 py-2"
    active-class="text-[#00b3b0]"
  >
    <slot />
  </NuxtLink>
</template>

<script setup>
defineProps({
  to: {
    type: String,
    required: true
  }
})
</script>
