<template>
  <NuxtLink
    :to="to"
    class="relative text-black font-medium px-4 py-2 text-[1.5rem] after:absolute after:bottom-0 after:left-0 after:h-[2px] after:w-0 after:bg-black after:transition-all after:duration-300 hover:after:w-full"
    active-class="after:w-full"
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
