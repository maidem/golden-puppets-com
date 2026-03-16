<template>
  <button :class="buttonStyles" @click="$emit('click')">
    <slot />
  </button>
</template>

<script setup>
const props = defineProps({
  variant: {
    type: String,
    default: 'primary' // primary, secondary, outline
  }
})

defineEmits(['click'])

const buttonStyles = computed(() => {
  const base = 'px-6 py-3 rounded-full font-medium transition-all duration-300 transform hover:scale-105 active:scale-95'
  const variants = {
    primary: 'bg-[#00b3b0] text-white hover:bg-[#009a97] shadow-lg',
    secondary: 'bg-gray-800 text-white hover:bg-gray-900',
    outline: 'border-2 border-[#00b3b0] text-[#00b3b0] hover:bg-[#00b3b0] hover:text-white'
  }
  return `${base} ${variants[props.variant] || variants.primary}`
})
</script>
