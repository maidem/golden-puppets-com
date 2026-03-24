<template>
  <article v-editable="blok" class="max-w-4xl mx-auto px-4 py-20">
    <!-- Hero-Bild -->
    <div
      v-if="blok.image?.filename"
      class="rounded-[2.5rem] overflow-hidden mb-16 shadow-2xl aspect-[21/9] group"
    >
      <img
        :src="blok.image.filename"
        :alt="blok.headline"
        class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105"
      />
    </div>

    <!-- Überschrift -->
    <header class="mb-12">
      <h1 class="text-4xl md:text-7xl font-black mb-8 text-gray-900 tracking-tighter leading-[1.1]">
        {{ blok.headline }}
      </h1>
      <div v-if="blok.teaser_text" class="text-xl md:text-2xl text-gray-500 font-medium leading-relaxed border-l-4 border-[#00b3b0] pl-8">
        {{ blok.teaser_text }}
      </div>
    </header>

    <!-- Richtext Body -->
    <div
      v-if="bodyHtml"
      class="prose prose-xl prose-teal max-w-none text-gray-700 leading-relaxed mb-20
             [&_h2]:text-4xl [&_h2]:font-black [&_h2]:mt-16 [&_h2]:mb-8 [&_h2]:text-gray-900
             [&_h3]:text-2xl [&_h3]:font-bold [&_h3]:mt-10 [&_h3]:mb-4
             [&_p]:mb-8 [&_p]:text-lg md:[&_p]:text-xl
             [&_ul]:mb-10 [&_ul]:list-disc [&_ul]:pl-8
             [&_blockquote]:py-4 [&_blockquote]:border-l-8 [&_blockquote]:border-[#00b3b0]/30 [&_blockquote]:pl-8 [&_blockquote]:italic [&_blockquote]:text-2xl [&_blockquote]:text-gray-500"
      v-html="bodyHtml"
    />

    <!-- Bildergalerie -->
    <section v-if="blok.gallery?.length" class="mt-24 border-t border-gray-100 pt-20">
      <h2 class="text-3xl font-black mb-12 text-gray-900 flex items-center gap-4">
        <span>Impressionen</span>
        <span class="w-12 h-1 bg-[#00b3b0] rounded-full"></span>
      </h2>
      
      <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        <button 
          v-for="(img, index) in blok.gallery" 
          :key="img.id"
          class="aspect-square rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 group relative border-2 border-transparent hover:border-[#00b3b0]"
          @click="openLightbox(index)"
        >
          <img 
            :src="img.filename + '/m/400x400/smart'" 
            :alt="img.alt" 
            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
          />
          <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-300 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 scale-50 group-hover:scale-100 transition-all duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
            </svg>
          </div>
        </button>
      </div>
    </section>

    <!-- Lightbox Overlay (Vue Teleport for clean DOM) -->
    <Teleport to="body">
      <Transition 
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0 backdrop-blur-0"
        enter-to-class="opacity-100 backdrop-blur-xl"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100 backdrop-blur-xl"
        leave-to-class="opacity-0 backdrop-blur-0"
      >
        <div 
          v-if="lightbox.isOpen" 
          class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/95 p-4 md:p-12 touch-none select-none"
          @keydown.esc="closeLightbox"
          @click.self="closeLightbox"
        >
          <!-- Schließen Button -->
          <button @click="closeLightbox" class="absolute top-10 right-10 text-white hover:text-[#00b3b0] transition-colors p-4 z-10">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>

          <!-- Hauptbild -->
          <Transition 
            mode="out-in"
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 translate-y-4 scale-95"
            enter-to-class="opacity-100 translate-y-0 scale-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100 translate-y-0 scale-100"
            leave-to-class="opacity-0 -translate-y-4 scale-95"
          >
            <div :key="lightbox.currentIndex" class="relative max-w-full max-h-full flex items-center justify-center">
              <img 
                :src="blok.gallery[lightbox.currentIndex].filename" 
                :alt="blok.gallery[lightbox.currentIndex].alt"
                class="max-w-full max-h-[85vh] object-contain shadow-2xl rounded-lg"
              />
              <div v-if="blok.gallery[lightbox.currentIndex].title" class="absolute -bottom-16 left-0 right-0 text-center text-white text-lg font-medium">
                {{ blok.gallery[lightbox.currentIndex].title }}
              </div>
            </div>
          </Transition>

          <!-- Navigation Buttons -->
          <div class="absolute inset-y-0 left-4 md:left-12 flex items-center">
            <button @click="prevImage" class="p-4 text-white hover:text-[#00b3b0] transition-all hover:scale-125 bg-white/5 hover:bg-white/10 rounded-full backdrop-blur-sm">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
              </svg>
            </button>
          </div>
          <div class="absolute inset-y-0 right-4 md:right-12 flex items-center">
            <button @click="nextImage" class="p-4 text-white hover:text-[#00b3b0] transition-all hover:scale-125 bg-white/5 hover:bg-white/10 rounded-full backdrop-blur-sm">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
              </svg>
            </button>
          </div>

          <!-- Counter -->
          <div class="absolute bottom-10 left-1/2 -translate-x-1/2 text-white/50 font-black text-sm tracking-widest uppercase">
            {{ lightbox.currentIndex + 1 }} / {{ blok.gallery.length }}
          </div>
        </div>
      </Transition>
    </Teleport>
  </article>
</template>

<script setup>
const props = defineProps({ blok: Object })

// Rich Text Rendering
const bodyHtml = computed(() =>
  props.blok?.body ? renderRichText(props.blok.body) : '',
)

// Lightbox Logik
const lightbox = reactive({
  isOpen: false,
  currentIndex: 0
})

function openLightbox(index) {
  lightbox.currentIndex = index
  lightbox.isOpen = true
  // Body-Scroll deaktivieren
  document.body.style.overflow = 'hidden'
}

function closeLightbox() {
  lightbox.isOpen = false
  // Body-Scroll wieder aktivieren
  document.body.style.overflow = ''
}

function nextImage() {
  lightbox.currentIndex = (lightbox.currentIndex + 1) % props.blok.gallery.length
}

function prevImage() {
  lightbox.currentIndex = (lightbox.currentIndex - 1 + props.blok.gallery.length) % props.blok.gallery.length
}

// Esc-Taste zum Schließen abfangen
onMounted(() => {
  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeLightbox()
    if (e.key === 'ArrowRight' && lightbox.isOpen) nextImage()
    if (e.key === 'ArrowLeft' && lightbox.isOpen) prevImage()
  })
})
</script>
