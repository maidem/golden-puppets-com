<template>
  <figure v-editable="blok" class="flex flex-col items-center text-center">
    <div class="w-32 h-32 mb-4 overflow-hidden rounded-full bg-gray-100">
      <img
        v-if="blok.image?.filename"
        :src="blok.image.filename"
        :alt="blok.image.alt || blok.name"
        class="w-full h-full object-cover"
        loading="lazy"
      />
    </div>
    <figcaption>
      <p class="font-semibold text-gray-900">{{ blok.name }}</p>
      <p v-if="blok.role" class="text-sm text-gray-500 mt-1">{{ blok.role }}</p>
    </figcaption>
  </figure>
</template>

<script setup>
defineProps({ blok: Object });
</script>
