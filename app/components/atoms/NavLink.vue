<template>
  <NuxtLink
    :to="to"
    class="relative inline-block text-white font-medium px-4 py-2 text-[1.125rem] md:text-[1.5rem] after:absolute after:bottom-0 after:left-4 after:right-4 after:h-[2px] after:bg-white after:transition-all after:duration-300 after:scale-x-0 after:origin-left hover:after:scale-x-100"
    active-class="after:scale-x-100"
  >
    <slot />
  </NuxtLink>
</template>

<script setup>
defineProps({
  to: {
    type: String,
    required: true,
  },
});
</script>
