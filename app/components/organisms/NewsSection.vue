<template>
  <section class="bg-[#f5f4f2] py-10 md:py-14 px-5 md:px-16">
    <div class="max-w-[1400px] mx-auto">
      <h2
        class="text-2xl md:text-4xl font-bold leading-[1.1] tracking-tight text-black mb-7"
      >
        {{ headline }}<span class="text-[#D0992A]">.</span>
      </h2>

      <div v-if="articles.length">
        <!-- Horizontal scroll track -->
        <div
          ref="trackRef"
          style="
            display: flex;
            flex-direction: row;
            gap: 1.5rem;
            overflow-x: auto;
            overflow-y: visible;
            padding-bottom: 0.5rem;
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            cursor: grab;
            user-select: none;
          "
          @scroll="onScroll"
          @mousedown="onMouseDown"
          @mousemove="onMouseMove"
          @mouseup="onMouseUp"
          @mouseleave="onMouseUp"
        >
          <div
            v-for="(article, i) in articles"
            :key="`${article.slug || 'article'}-${i}`"
            style="flex: 0 0 19rem; scroll-snap-align: start"
          >
            <NewsCard
              :image="article.image"
              :headline="article.headline"
              :teaser-text="article.teaserText"
              :link="article.slug"
            />
          </div>
          <div style="flex: 0 0 1px" aria-hidden="true" />
        </div>
      </div>

      <p v-else class="text-gray-600 text-sm">Keine Beiträge vorhanden.</p>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from "vue";
import NewsCard from "~/components/molecules/NewsCard.vue";
import BaseHeading from "~/components/atoms/BaseHeading.vue";

const props = defineProps({
  headline: { type: String, default: "Aktuelles" },
  articles: { type: Array, default: () => [] },
});

const trackRef = ref(null);
const activeIndex = ref(0);
const cardWidth = ref(0);

// Drag-to-scroll state
const isDragging = ref(false);
const dragStartX = ref(0);
const dragScrollLeft = ref(0);

onMounted(() => {
  if (trackRef.value) {
    const firstCard = trackRef.value.children[0];
    if (firstCard) {
      cardWidth.value = firstCard.offsetWidth + 24;
    }
  }
});

function onScroll() {
  if (!trackRef.value || !cardWidth.value) return;
  activeIndex.value = Math.round(trackRef.value.scrollLeft / cardWidth.value);
}

function onMouseDown(e) {
  isDragging.value = true;
  dragStartX.value = e.pageX - trackRef.value.offsetLeft;
  dragScrollLeft.value = trackRef.value.scrollLeft;
  trackRef.value.style.cursor = "grabbing";
  trackRef.value.style.scrollSnapType = "none";
}

function onMouseMove(e) {
  if (!isDragging.value) return;
  e.preventDefault();
  const x = e.pageX - trackRef.value.offsetLeft;
  const walk = (x - dragStartX.value) * 1.2;
  trackRef.value.scrollLeft = dragScrollLeft.value - walk;
}

function onMouseUp() {
  if (!isDragging.value) return;
  isDragging.value = false;
  trackRef.value.style.cursor = "grab";
  trackRef.value.style.scrollSnapType = "x mandatory";
}

function goTo(index) {
  if (!trackRef.value) return;
  const w = cardWidth.value || 0;
  trackRef.value.scrollTo({ left: index * w, behavior: "smooth" });
}
</script>

<style scoped>
div::-webkit-scrollbar {
  display: none;
}
</style>
