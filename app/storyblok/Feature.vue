<template>
  <div v-editable="blok" class="flex flex-col h-full bg-white p-8 rounded-[2.5rem] border border-gray-100/50 shadow-xl hover:shadow-2xl transition-all duration-300 group">
    <!-- Icon / Bild -->
    <div 
      v-if="blok.icon?.filename" 
      class="w-16 h-16 mb-8 rounded-2xl bg-gray-50 flex items-center justify-center p-3 group-hover:scale-110 group-hover:bg-[#00b3b0]/10 transition-all duration-300"
    >
      <img 
        :src="blok.icon.filename" 
        :alt="blok.name" 
        class="w-full h-full object-contain"
      />
    </div>

    <!-- Überschrift -->
    <h3 class="text-2xl font-black mb-4 text-gray-900 leading-tight">
      {{ blok.name }}
    </h3>

    <!-- Beschreibung -->
    <p v-if="blok.description" class="text-gray-500 font-medium leading-relaxed">
      {{ blok.description }}
    </p>

    <!-- Kleine Trennung am Ende für mehr Hochwertigkeit -->
    <div class="mt-auto pt-8">
      <div class="w-10 h-1 bg-[#00b3b0]/20 rounded-full group-hover:w-16 transition-all duration-500"></div>
    </div>
  </div>
</template>

<script setup>
defineProps({ blok: Object })
</script>
