<template>
  <!-- Mobile: eine gelbe Flaeche mit nativem Swipe -->
  <section
    class="md:hidden bg-[#FFE600] border-t-2 border-b-2 border-black px-4 sm:px-5 pt-7 pb-7"
  >
    <BaseHeading tag="h2" size="lg" class="text-black mb-6">
      {{ headline }}
    </BaseHeading>

    <div
      v-if="articles.length"
      ref="mobileTrack"
      class="flex gap-3 sm:gap-4 overflow-x-auto snap-x snap-mandatory pb-2 scrollbar-hide cursor-grab active:cursor-grabbing"
      @scroll.passive="onMobileScroll"
      @mousedown="onMobileDragStart"
      @mousemove="onMobileDragMove"
      @mouseup="onMobileDragEnd"
      @mouseleave="onMobileDragEnd"
    >
      <NewsCard
        v-for="(article, i) in articles"
        :key="article.slug"
        :ref="(el) => (mobileCards[i] = el)"
        :image="article.image"
        :headline="article.headline"
        :teaser-text="article.teaserText"
        :link="article.slug"
        class="shrink-0 w-[85vw] max-w-[330px] snap-start"
      />
    </div>

    <div v-if="articles.length > 1" class="mt-3 flex items-center gap-2 px-0.5">
      <button
        v-for="(_, i) in articles"
        :key="`mobile-dot-${i}`"
        type="button"
        class="w-2.5 h-2.5 rounded-full border-2 border-black transition-colors duration-200"
        :class="mobileActiveIndex === i ? 'bg-black' : 'bg-transparent'"
        :aria-label="`Gehe zu News ${i + 1}`"
        @click="scrollMobileTo(i)"
      />
    </div>

    <p v-else class="text-gray-700 text-sm">Keine Beitraege vorhanden.</p>
  </section>

  <!-- Desktop: aufwendiger Split-Layout-Slider -->
  <section
    class="hidden md:block relative bg-black border-t-2 border-b-2 border-black overflow-hidden"
  >
    <!-- Gelber Hintergrund rechts -->
    <div
      class="absolute inset-y-0 left-[38.2%] right-0 bg-[#FFE600] rounded-tl-[1.75rem] rounded-bl-[1.75rem] pointer-events-none"
    ></div>

    <!-- Slide-Viewport: beginnt am Trenner, überschüssige Cards laufen rechts raus -->
    <div
      class="absolute left-[38.2%] top-0 right-0 bottom-0"
      :class="dragging ? 'cursor-grabbing' : 'cursor-grab'"
      @mousedown="onDragStart"
      @mousemove="onDragMove"
      @mouseup="onDragEnd"
      @mouseleave="onDragEnd"
      @touchstart.passive="onTouchStart"
      @touchmove.passive="onTouchMove"
      @touchend="onTouchEnd"
    >
      <!-- Inner Strip: bewegt sich per translateX -->
      <div
        ref="strip"
        class="absolute top-12 flex gap-6 select-none"
        :style="{
          left: PEEK + 'px',
          transform: `translateX(${offsetX}px)`,
          transition: animating
            ? 'transform 0.35s cubic-bezier(0.25,0.46,0.45,0.94)'
            : 'none',
        }"
      >
        <NewsCard
          v-for="(article, i) in articles"
          :key="article.slug"
          :image="article.image"
          :headline="article.headline"
          :teaser-text="article.teaserText"
          :link="article.slug"
          class="shrink-0 w-72"
          :style="{ pointerEvents: dragging ? 'none' : 'auto' }"
        />
        <div v-if="!articles.length" class="text-gray-500 text-lg py-4">
          Keine Beiträge vorhanden.
        </div>
      </div>
    </div>

    <!-- Spacer für Höhe -->
    <div
      class="ml-[38.2%] pt-12 pb-10 invisible pointer-events-none flex gap-6"
    >
      <div class="shrink-0 w-72 aspect-[4/5]"></div>
    </div>

    <!-- Dots -->
    <div
      v-if="articles.length > 1"
      class="relative flex gap-3 pb-10 pt-0 pl-8 ml-[38.2%]"
    >
      <button
        v-for="(_, i) in articles"
        :key="i"
        class="w-3 h-3 rounded-full border-2 border-black transition-colors duration-200"
        :class="activeIndex === i ? 'bg-black' : 'bg-transparent'"
        @click="snapTo(i)"
      />
    </div>

    <!-- Linker Panel obendrauf -->
    <div
      class="absolute left-0 top-0 bottom-0 w-[38.2%] bg-[#FFE600] rounded-tr-[1.75rem] rounded-br-[1.75rem] z-10 flex items-start pt-12 px-8 md:px-16 pointer-events-none"
    >
      <BaseHeading tag="h2" size="lg" class="text-black pointer-events-auto">
        {{ headline }}
      </BaseHeading>
    </div>

    <!-- 2px Trenner -->
    <div
      class="absolute left-[38.2%] top-0 bottom-0 w-[2px] bg-black z-10 pointer-events-none"
    ></div>
  </section>
</template>

<script setup>
import { ref } from "vue";

const props = defineProps({
  headline: { type: String, default: "Aktuelles" },
  articles: { type: Array, default: () => [] },
});

// Karte w-72 = 288px, gap-6 = 24px
// PEEK: wie viel von der vorherigen Karte unter dem Panel herausschaut
const CARD = 288;
const GAP = 24;
const PEEK = 40;

const strip = ref(null);
const activeIndex = ref(0);
const offsetX = ref(0);
const animating = ref(false);
const dragging = ref(false);

const mobileTrack = ref(null);
const mobileCards = ref([]);
const mobileActiveIndex = ref(0);
let mobileDragging = false;
let mobileDragStartX = 0;
let mobileDragStartScroll = 0;

// snapPos(i): wie weit der Strip nach links verschoben wird
// i=0 → 0 (erste Karte bei PEEK px vom Trenner)
// i>0 → -(i*(CARD+GAP) - PEEK) → vorherige Karte ragt PEEK px heraus
function snapPos(i) {
  return i === 0 ? 0 : -(i * (CARD + GAP) - PEEK);
}

function snapTo(i) {
  const maxI = Math.max(0, props.articles.length - 1);
  const clamped = Math.max(0, Math.min(i, maxI));
  animating.value = true;
  offsetX.value = snapPos(clamped);
  activeIndex.value = clamped;
}

function snapToNearest() {
  const current = offsetX.value;
  let closest = 0;
  let minDist = Infinity;
  props.articles.forEach((_, i) => {
    const d = Math.abs(snapPos(i) - current);
    if (d < minDist) {
      minDist = d;
      closest = i;
    }
  });
  snapTo(closest);
}

// Mouse drag
let dragStartX = 0;
let dragStartOffset = 0;

function onDragStart(e) {
  dragging.value = true;
  animating.value = false;
  dragStartX = e.clientX;
  dragStartOffset = offsetX.value;
}
function onDragMove(e) {
  if (!dragging.value) return;
  offsetX.value = dragStartOffset + (e.clientX - dragStartX);
}
function onDragEnd() {
  if (!dragging.value) return;
  dragging.value = false;
  snapToNearest();
}

// Touch
let touchStartX = 0;
let touchStartOffset = 0;

function onTouchStart(e) {
  dragging.value = true;
  animating.value = false;
  touchStartX = e.touches[0].clientX;
  touchStartOffset = offsetX.value;
}
function onTouchMove(e) {
  offsetX.value = touchStartOffset + (e.touches[0].clientX - touchStartX);
}
function onTouchEnd() {
  dragging.value = false;
  snapToNearest();
}

function onMobileScroll() {
  if (!mobileTrack.value || !mobileCards.value.length) return;
  const scrollLeft = mobileTrack.value.scrollLeft;
  let closest = 0;
  let minDist = Infinity;

  mobileCards.value.forEach((card, i) => {
    const el = card?.$el ?? card;
    if (!el) return;
    const dist = Math.abs(el.offsetLeft - scrollLeft);
    if (dist < minDist) {
      minDist = dist;
      closest = i;
    }
  });

  mobileActiveIndex.value = closest;
}

function scrollMobileTo(i) {
  const card = mobileCards.value[i];
  const el = card?.$el ?? card;
  if (!mobileTrack.value || !el) return;
  mobileTrack.value.scrollTo({ left: el.offsetLeft, behavior: "smooth" });
  mobileActiveIndex.value = i;
}

function onMobileDragStart(e) {
  if (!mobileTrack.value) return;
  mobileDragging = true;
  mobileDragStartX = e.clientX;
  mobileDragStartScroll = mobileTrack.value.scrollLeft;
}

function onMobileDragMove(e) {
  if (!mobileDragging || !mobileTrack.value) return;
  e.preventDefault();
  mobileTrack.value.scrollLeft =
    mobileDragStartScroll - (e.clientX - mobileDragStartX);
}

function onMobileDragEnd() {
  mobileDragging = false;
  onMobileScroll();
}
</script>
