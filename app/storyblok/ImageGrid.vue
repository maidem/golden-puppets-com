<template>
  <section
    v-editable="blok"
    :id="blok.section_id || 'ueber-uns'"
    class="py-16 px-4"
  >
    <div class="max-w-6xl mx-auto">
      <h2
        v-if="blok.headline"
        class="text-3xl md:text-4xl font-bold text-gray-900 mb-10 text-center"
      >
        {{ blok.headline }}
      </h2>

      <ul :class="['grid gap-3', gridCols]">
        <li
          v-for="(img, index) in images"
          :key="img.id || img.filename || index"
          class="aspect-square overflow-hidden bg-gray-100 cursor-zoom-in group"
        >
          <button
            type="button"
            class="block w-full h-full focus:outline-none focus:ring-2 focus:ring-gray-900"
            :aria-label="`Bild ${index + 1} öffnen`"
            @click="open(index)"
          >
            <img
              :src="toStoryblokImage(img.filename, 800)"
              :alt="img.alt || ''"
              loading="lazy"
              decoding="async"
              class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
            />
          </button>
        </li>
      </ul>
    </div>

    <!-- Lightbox-Overlay -->
    <Teleport to="body">
      <div
        v-if="activeIndex !== null"
        class="fixed inset-0 z-[100] bg-black/90 flex items-center justify-center select-none"
        role="dialog"
        aria-modal="true"
        :aria-label="`Bild ${activeIndex + 1} von ${images.length}`"
        @click.self="close"
      >
        <button
          type="button"
          class="absolute top-4 right-4 text-white/80 hover:text-white text-3xl leading-none p-2"
          aria-label="Schließen"
          @click="close"
        >
          &times;
        </button>

        <button
          v-if="images.length > 1"
          type="button"
          class="absolute left-4 md:left-8 top-1/2 -translate-y-1/2 text-white/80 hover:text-white text-4xl p-2"
          aria-label="Vorheriges Bild"
          @click.stop="prev"
        >
          &#8249;
        </button>

        <figure
          class="max-w-[90vw] max-h-[85vh] flex flex-col items-center"
          @pointerdown="onPointerDown"
          @pointermove="onPointerMove"
          @pointerup="onPointerUp"
          @pointercancel="onPointerUp"
        >
          <img
            :src="toStoryblokImage(current.filename, 1920)"
            :alt="current.alt || ''"
            class="max-w-[90vw] max-h-[80vh] object-contain"
            :style="dragStyle"
            draggable="false"
          />
          <figcaption
            v-if="current.alt || images.length > 1"
            class="mt-3 text-sm text-white/70"
          >
            <span v-if="current.alt">{{ current.alt }}</span>
            <span v-if="images.length > 1" class="ml-2">
              {{ activeIndex + 1 }} / {{ images.length }}
            </span>
          </figcaption>
        </figure>

        <button
          v-if="images.length > 1"
          type="button"
          class="absolute right-4 md:right-8 top-1/2 -translate-y-1/2 text-white/80 hover:text-white text-4xl p-2"
          aria-label="Nächstes Bild"
          @click.stop="next"
        >
          &#8250;
        </button>
      </div>
    </Teleport>
  </section>
</template>

<script setup>
const props = defineProps({ blok: Object });


const images = computed(() =>
  Array.isArray(props.blok?.images) ? props.blok.images : [],
);

const gridCols = computed(() => {
  const map = {
    2: "grid-cols-2",
    3: "grid-cols-2 md:grid-cols-3",
    4: "grid-cols-2 md:grid-cols-4",
  };
  return map[props.blok?.columns] || map["3"];
});

const activeIndex = ref(null);
const current = computed(() =>
  activeIndex.value !== null ? images.value[activeIndex.value] || {} : {},
);

function open(i) {
  activeIndex.value = i;
}
function close() {
  activeIndex.value = null;
  resetDrag();
}
function next() {
  if (!images.value.length) return;
  activeIndex.value = (activeIndex.value + 1) % images.value.length;
  resetDrag();
}
function prev() {
  if (!images.value.length) return;
  activeIndex.value =
    (activeIndex.value - 1 + images.value.length) % images.value.length;
  resetDrag();
}

// Tastatur-Steuerung
function onKey(e) {
  if (activeIndex.value === null) return;
  if (e.key === "Escape") close();
  else if (e.key === "ArrowRight") next();
  else if (e.key === "ArrowLeft") prev();
}

onMounted(() => window.addEventListener("keydown", onKey));
onBeforeUnmount(() => window.removeEventListener("keydown", onKey));

// Drag/Swipe-Steuerung (Maus + Touch via PointerEvents)
const dragStartX = ref(null);
const dragOffsetX = ref(0);
const SWIPE_THRESHOLD = 60;

const dragStyle = computed(() => ({
  transform: dragOffsetX.value ? `translateX(${dragOffsetX.value}px)` : "",
  transition: dragStartX.value === null ? "transform 0.2s ease-out" : "none",
  cursor: dragStartX.value !== null ? "grabbing" : "grab",
}));

function onPointerDown(e) {
  dragStartX.value = e.clientX;
  dragOffsetX.value = 0;
  e.target.setPointerCapture?.(e.pointerId);
}
function onPointerMove(e) {
  if (dragStartX.value === null) return;
  dragOffsetX.value = e.clientX - dragStartX.value;
}
function onPointerUp() {
  if (dragStartX.value === null) return;
  if (dragOffsetX.value > SWIPE_THRESHOLD) prev();
  else if (dragOffsetX.value < -SWIPE_THRESHOLD) next();
  resetDrag();
}
function resetDrag() {
  dragStartX.value = null;
  dragOffsetX.value = 0;
}
</script>
