<template>
  <component :is="tag" :class="headingStyles">
    <slot />
  </component>
</template>

<script setup>
const props = defineProps({
  tag: {
    type: String,
    default: 'h2'
  },
  size: {
    type: String,
    default: 'md' // sm, md, lg, xl
  }
})

const headingStyles = computed(() => {
  const sizes = {
    sm: 'text-xl font-semibold',
    md: 'text-3xl font-bold',
    lg: 'text-5xl font-extrabold',
    xl: 'text-7xl font-black'
  }
  return sizes[props.size] || sizes.md
})
</script>
