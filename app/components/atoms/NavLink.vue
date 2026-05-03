<template>
  <NuxtLink
    :to="to"
    class="text-black hover:opacity-60 transition-opacity duration-200 font-medium px-4 py-2 text-[1.5rem]"
    active-class="opacity-60"
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
