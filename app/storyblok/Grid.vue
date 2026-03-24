<template>
  <div v-editable="blok" class="py-24 px-4 bg-gray-50/20 overflow-hidden relative">
    <!-- Subtiles Hintergrund-Dekor für den Wow-Effekt -->
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-[#00b3b0]/5 rounded-full blur-[100px] animate-pulse"></div>
    <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-gray-200/20 rounded-full blur-[100px]"></div>

    <div class="max-w-7xl mx-auto relative z-10">
      <!-- Sektions-Überschrift & Subline -->
      <header v-if="blok.headline" class="max-w-4xl mx-auto text-center mb-20 px-8">
        <h2 class="text-4xl md:text-6xl font-black mb-8 text-black leading-tight tracking-tight">
          {{ blok.headline }}
        </h2>
        <p v-if="blok.subline" class="text-xl md:text-2xl text-gray-500 font-medium leading-relaxed max-w-2xl mx-auto">
          {{ blok.subline }}
        </p>
        <!-- Kleine Akzent-Linie -->
        <div class="mt-10 flex justify-center">
          <span class="w-16 h-1.5 bg-[#00b3b0] rounded-full shadow-[0_0_20px_rgba(0,179,176,0.3)]"></span>
        </div>
      </header>

      <!-- Dynamisches Grid -->
      <div 
        v-if="blok.columns_list?.length"
        class="grid grid-cols-1 gap-10 md:gap-12"
        :class="gridClasses"
      >
        <StoryblokComponent
          v-for="subBlok in blok.columns_list"
          :key="subBlok._uid"
          :blok="subBlok"
        />
      </div>

      <!-- Leere-Meldung in der Vorschau -->
      <div v-else class="text-center p-12 border-4 border-dashed border-[#00b3b0]/10 rounded-3xl text-gray-400">
        <p class="text-lg font-black animate-pulse">Vervollständige dein Grid mit Inhalten…</p>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({ blok: Object })

// Dynamische Auswahl der Spalten für mobile-first Responsive Design
const gridClasses = computed(() => {
  const cols = parseInt(props.blok.columns) || 3
  
  switch(cols) {
    case 2:
      return 'md:grid-cols-2 max-w-5xl mx-auto'
    case 3:
      return 'md:grid-cols-2 lg:grid-cols-3'
    case 4:
      return 'sm:grid-cols-2 lg:grid-cols-4'
    default:
      return 'md:grid-cols-2 lg:grid-cols-3'
  }
})
</script>
