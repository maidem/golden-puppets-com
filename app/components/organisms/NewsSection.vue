<template>
  <section class="bg-[#f5f4f2] py-10 md:py-14 px-5 md:px-16 overflow-hidden">
    <div class="max-w-[1400px] mx-auto">
      <h2
        class="text-2xl md:text-4xl font-bold leading-[1.1] tracking-tight text-black mb-7"
      >
        {{ headline }}<span class="text-[#D0992A]">.</span>
      </h2>

      <div v-if="articles.length">
        <!-- Mobile: Stacked cards with drag (Swiper) -->
        <div class="md:hidden">
          <Swiper
            :modules="mobileModules"
            effect="cards"
            :cards-effect="{
              perSlideRotate: 0,
              perSlideOffset: 10,
              slideShadows: false,
            }"
            :grab-cursor="true"
            @slideChange="onMobileSlideChange"
          >
            <SwiperSlide
              v-for="(article, i) in articles"
              :key="`${article.slug || 'article'}-${i}`"
            >
              <NewsCard
                :image="article.image"
                :headline="article.headline"
                :teaser-text="article.teaserText"
                :link="article.slug"
              />
            </SwiperSlide>
          </Swiper>

          <!-- Mobile: Progress line -->
          <div
            v-if="articles.length > 1"
            class="mt-5"
            :style="{ '--segments': String(articles.length) }"
          >
            <div class="news-line-indicator">
              <span
                class="news-line-progress"
                :style="{
                  width: `${lineFill}%`,
                }"
              />
            </div>
          </div>
        </div>

        <!-- Desktop: Grid layout (3 columns) -->
        <div class="hidden md:grid grid-cols-3 gap-6">
          <NewsCard
            v-for="(article, i) in articlesToShow"
            :key="`${article.slug || 'article'}-${i}`"
            :image="article.image"
            :headline="article.headline"
            :teaser-text="article.teaserText"
            :link="article.slug"
          />
        </div>

        <!-- Show more button -->
        <div
          v-if="articles.length > displayLimit"
          class="mt-10 md:mt-12 flex justify-center"
        >
          <NuxtLink
            to="/#aktuelles"
            class="inline-flex items-center gap-2 bg-[#231F21] text-white font-bold text-base px-8 py-3 rounded-full border-2 border-[#231F21] hover:bg-black hover:border-black transition-colors duration-200"
          >
            Alle Beiträge anschauen
          </NuxtLink>
        </div>
      </div>

      <p v-else class="text-gray-600 text-sm">Keine Beiträge vorhanden.</p>
    </div>
  </section>
</template>

<script setup>
import { computed, ref, onMounted } from "vue";
import { EffectCards } from "swiper/modules";
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";
import "swiper/css/effect-cards";
import NewsCard from "~/components/molecules/NewsCard.vue";
import BaseHeading from "~/components/atoms/BaseHeading.vue";

const props = defineProps({
  headline: { type: String, default: "Aktuelles" },
  articles: { type: Array, default: () => [] },
});

const trackRef = ref(null);
const activeIndex = ref(0);
const cardWidth = ref(0);
const displayLimit = 6; // Show 6 articles on desktop by default
const mobileModules = [EffectCards];

// Drag-to-scroll state
const isDragging = ref(false);
const dragStartX = ref(0);
const dragScrollLeft = ref(0);

const articlesToShow = computed(() => {
  return props.articles.slice(0, displayLimit);
});

const lineFill = computed(() => {
  if (props.articles.length <= 1) return 0;
  return ((activeIndex.value + 1) / props.articles.length) * 100;
});

function onMobileSlideChange(swiper) {
  activeIndex.value = swiper.activeIndex;
}

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
  activeIndex.value = Math.min(activeIndex.value, props.articles.length - 1);
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

.news-line-indicator {
  position: relative;
  height: 4px;
  width: min(22rem, 100%);
  border-radius: 999px;
  background:
    repeating-linear-gradient(
      to right,
      rgba(0, 0, 0, 0.16) 0,
      rgba(0, 0, 0, 0.16) 1px,
      transparent 1px,
      transparent calc(100% / var(--segments))
    ),
    rgba(0, 0, 0, 0.12);
}

.news-line-progress {
  position: absolute;
  left: 0;
  top: 0;
  height: 100%;
  border-radius: 999px;
  background: linear-gradient(90deg, #7c5a14 0%, #d0992a 100%);
  transition: transform 0.22s ease;
}
</style>
