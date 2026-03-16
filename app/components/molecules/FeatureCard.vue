<template>
  <div class="p-8 bg-white rounded-2xl shadow-xl hover:shadow-2xl transition-shadow duration-300 border border-gray-100 flex flex-col items-center text-center">
    <BaseHeading tag="h3" size="sm" class="mb-4 text-gray-800">
      {{ title }}
    </BaseHeading>
    <p class="text-gray-600 mb-6">
      Dies ist ein beispielhafter Text für das Feature-Element.
    </p>
    <BaseButton variant="outline">
      Mehr erfahren
    </BaseButton>
  </div>
</template>

<script setup>
defineProps({
  title: String
})
</script>
