<template>
  <div v-editable="blok" class="py-24 px-4 text-center bg-gray-50 flex flex-col items-center">
    <div class="max-w-4xl mx-auto">
      <!-- Headline -->
      <h1 class="text-4xl md:text-7xl font-black text-[#00b3b0] tracking-tight mb-8 leading-none">
        {{ blok.headline }}
      </h1>

      <!-- Teaser Text -->
      <p
        v-if="blok.teaser_text"
        class="text-xl md:text-2xl text-gray-600 font-medium leading-relaxed max-w-2xl mx-auto"
      >
        {{ blok.teaser_text }}
      </p>

      <!-- Subtile Trennung am Ende -->
      <div class="mt-12 w-24 h-1.5 bg-[#00b3b0] rounded-full opacity-20"></div>
    </div>
  </div>
</template>

<script setup>
defineProps({ blok: Object })
</script>
