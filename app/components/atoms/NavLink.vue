<template>
  <a
    :href="to"
    class="relative inline-block text-white font-semibold px-3 py-2 text-[0.82rem] md:text-[0.9rem] tracking-[0.02em] pointer-events-auto after:absolute after:bottom-0 after:left-3 after:right-3 after:h-[2px] after:bg-white after:transition-all after:duration-300 after:scale-x-0 after:origin-left hover:after:scale-x-100"
  >
    <slot />
  </a>
</template>

<script setup>
defineProps({
  to: {
    type: String,
    required: true,
  },
});
</script>
