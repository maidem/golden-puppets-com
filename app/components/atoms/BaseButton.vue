<template>
  <button :class="buttonStyles" @click="$emit('click')">
    <slot />
  </button>
</template>

<script setup>
const props = defineProps({
  variant: {
    type: String,
    default: "primary", // primary, secondary, outline
  },
});

defineEmits(["click"]);

const buttonStyles = computed(() => {
  const base =
    "px-6 py-3 rounded-full font-medium transition-all duration-300 transform hover:scale-105 active:scale-95";
  const variants = {
    primary: "bg-[#231F21] text-white hover:bg-black shadow-lg",
    secondary: "bg-[#231F21] text-white hover:bg-black",
    outline:
      "border-2 border-[#231F21] text-[#231F21] hover:bg-[#231F21] hover:text-white",
    contrast:
      "px-5 py-2.5 text-sm tracking-wide bg-black text-white border-2 border-gray-300 hover:bg-[#151515] shadow-lg shadow-black/40",
  };
  return `${base} ${variants[props.variant] || variants.primary}`;
});
</script>
