<template>
  <article v-editable="blok" class="bg-[#f6f5f2] pb-4 pt-20 md:pb-6 md:pt-24">
    <div class="mx-auto w-[min(100%,72rem)] px-4 md:px-8">
      <!-- Back to overview -->
      <div class="mb-8">
        <NuxtLink
          to="/"
          class="inline-flex h-10 cursor-pointer items-center gap-2 rounded-full border border-black/15 bg-white/80 px-4 text-xs font-bold uppercase tracking-[0.14em] text-black transition-colors hover:bg-white"
        >
          <span aria-hidden="true">&larr;</span>
          Alle Beiträge
        </NuxtLink>
      </div>

      <!-- Prev / Next navigation -->
      <div class="mb-8 flex items-center justify-between gap-4">
        <NuxtLink
          v-if="prevArticle"
          :to="'/' + prevArticle.full_slug"
          class="inline-flex h-10 items-center gap-2 rounded-full border border-black/15 bg-white/80 px-4 text-xs font-bold uppercase tracking-[0.14em] text-black transition-colors hover:bg-white"
        >
          <span aria-hidden="true">&larr;</span>
          Vorheriger Beitrag
        </NuxtLink>
        <span v-else class="block h-10 w-40" />

        <NuxtLink
          v-if="nextArticle"
          :to="'/' + nextArticle.full_slug"
          class="inline-flex h-10 items-center gap-2 rounded-full border border-black/15 bg-white/80 px-4 text-xs font-bold uppercase tracking-[0.14em] text-black transition-colors hover:bg-white"
        >
          Nächster Beitrag
          <span aria-hidden="true">&rarr;</span>
        </NuxtLink>
        <span v-else class="block h-10 w-40" />
      </div>

      <section
        class="overflow-hidden rounded-[1.8rem] border border-black/10 bg-white shadow-[0_30px_70px_rgba(0,0,0,0.08)]"
      >
        <div class="relative">
          <div
            v-if="blok.image?.filename"
            class="aspect-[16/8] w-full overflow-hidden bg-black"
          >
            <img
              :src="heroImage"
              :alt="blok.headline || 'News Bild'"
              class="h-full w-full object-cover"
              loading="eager"
            />
          </div>
          <div
            class="pointer-events-none absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-black/45 to-transparent"
          />
        </div>

        <div class="px-6 pb-6 pt-7 md:px-12 md:pb-8 md:pt-10">
          <h1
            class="max-w-4xl text-2xl font-black leading-tight tracking-tight text-black md:text-4xl"
          >
            {{ blok.headline }}
            <span class="text-[#D0992A]">.</span>
          </h1>

          <div
            v-if="bodyHtml"
            class="prose-news mt-8 max-w-none text-[1.07rem] leading-relaxed text-[#1b1b1b]"
            v-html="bodyHtml"
          />

          <section
            v-if="galleryImages.length"
            class="mt-12 border-t border-black/10 pt-10"
          >
            <div class="mb-6 flex items-end justify-between gap-4">
              <h2
                class="text-2xl font-black tracking-tight text-black md:text-3xl"
              >
                Bildergalerie
                <span class="text-[#D0992A]">.</span>
              </h2>
              <span
                class="rounded-full bg-[#231F21] px-3 py-1 text-xs font-bold uppercase tracking-[0.08em] text-white"
              >
                {{ galleryImages.length }} Fotos
              </span>
            </div>

            <div class="mobile-gallery md:hidden">
              <Swiper
                :modules="mobileGalleryModules"
                effect="cards"
                :cards-effect="{
                  perSlideRotate: 0,
                  perSlideOffset: 10,
                  slideShadows: false,
                }"
                :grab-cursor="true"
                @slideChange="onMobileGallerySlideChange"
              >
                <SwiperSlide
                  v-for="(image, index) in galleryImages"
                  :key="`mobile-${image.filename || index}`"
                >
                  <div
                    class="aspect-[4/3] w-full overflow-hidden rounded-2xl bg-[#ece7db]"
                  >
                    <img
                      :src="toStoryblokImage(image.filename, 1400)"
                      :alt="
                        image.alt ||
                        `${blok.headline || 'News'} Bild ${index + 1}`
                      "
                      class="h-full w-full object-cover"
                      loading="lazy"
                    />
                  </div>
                </SwiperSlide>
              </Swiper>

              <div
                class="gallery-line mt-5"
                :style="{ '--segments': String(galleryImages.length || 1) }"
              >
                <span
                  class="gallery-line-progress"
                  :style="{
                    width: `${galleryLineFill}%`,
                  }"
                />
              </div>
            </div>

            <div
              class="hidden grid-cols-1 gap-4 md:grid md:grid-cols-2 lg:grid-cols-3"
            >
              <figure
                v-for="(image, index) in galleryImages"
                :key="image.filename || index"
                class="group relative overflow-hidden rounded-2xl"
              >
                <button
                  type="button"
                  class="aspect-[4/3] w-full overflow-hidden rounded-2xl bg-[#ece7db] text-left"
                  @click="openLightbox(index)"
                >
                  <img
                    :src="toStoryblokImage(image.filename, 1200)"
                    :alt="
                      image.alt ||
                      `${blok.headline || 'News'} Bild ${index + 1}`
                    "
                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                    loading="lazy"
                  />
                </button>

                <span
                  class="pointer-events-none absolute right-3 top-3 z-20 inline-flex h-7 w-7 items-center justify-center rounded-full border border-white/70 bg-white/70 text-sm font-semibold leading-none text-black/70 backdrop-blur-[1px] transition-colors duration-200 group-hover:border-[#D0992A] group-hover:text-[#8a6316]"
                  aria-hidden="true"
                >
                  +
                </span>
              </figure>
            </div>
          </section>
        </div>
      </section>
    </div>

    <Teleport to="body">
      <div
        v-if="isLightboxOpen"
        class="news-lightbox fixed inset-0 z-[120] bg-black/90"
        @click.self="closeLightbox"
      >
        <div class="flex h-full flex-col">
          <div class="flex items-center justify-between px-4 py-4 md:px-8">
            <span
              class="rounded-full bg-white/10 px-3 py-1 text-xs font-bold uppercase tracking-[0.1em] text-white"
            >
              {{ activeSlideIndex + 1 }} / {{ galleryImages.length }}
            </span>
            <button
              type="button"
              class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/25 text-white transition-colors hover:bg-white/15"
              aria-label="Galerie schliessen"
              @click="closeLightbox"
            >
              &times;
            </button>
          </div>

          <div class="min-h-0 flex-1 px-2 pb-6 md:px-8 md:pb-8">
            <Swiper
              class="h-full w-full"
              :modules="swiperModules"
              :navigation="true"
              :keyboard="{ enabled: true }"
              :zoom="true"
              :grab-cursor="true"
              :initial-slide="activeSlideIndex"
              @swiper="onLightboxSwiper"
              @slideChange="onSlideChange"
            >
              <SwiperSlide
                v-for="(image, index) in galleryImages"
                :key="`overlay-${image.filename || index}`"
              >
                <div
                  class="swiper-zoom-container flex h-full w-full items-center justify-center px-2 md:px-12"
                >
                  <img
                    :src="toStoryblokImage(image.filename, 2200)"
                    :alt="
                      image.alt ||
                      `${blok.headline || 'News'} Bild ${index + 1}`
                    "
                    class="max-h-[82vh] max-w-full select-none object-contain"
                    draggable="false"
                  />
                </div>
              </SwiperSlide>
            </Swiper>
          </div>
        </div>
      </div>
    </Teleport>
  </article>
</template>

<script setup>
import { EffectCards, Keyboard, Navigation, Zoom } from "swiper/modules";
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";
import "swiper/css/effect-cards";
import "swiper/css/navigation";
import "swiper/css/zoom";

const props = defineProps({ blok: Object });

const storyblokApi = useStoryblokApi();
const runtimeConfig = useRuntimeConfig();
const route = useRoute();

// Fetch all articles for prev/next navigation
const { data: allStoriesData } = await useAsyncData("news-article-nav", () =>
  storyblokApi.get("cdn/stories", {
    version: runtimeConfig.public.storyblokVersion,
    starts_with: "aktuelles",
    content_type: "news_article",
    sort_by: "created_at:desc",
  }),
);

const allArticles = computed(() => allStoriesData.value?.data?.stories || []);

const currentIndex = computed(() => {
  const slug = route.params.slug;
  const fullSlug = Array.isArray(slug) ? slug.join("/") : slug || "";
  return allArticles.value.findIndex((s) => s.full_slug === fullSlug);
});

const prevArticle = computed(() =>
  currentIndex.value > 0 ? allArticles.value[currentIndex.value - 1] : null,
);
const nextArticle = computed(() =>
  currentIndex.value >= 0 && currentIndex.value < allArticles.value.length - 1
    ? allArticles.value[currentIndex.value + 1]
    : null,
);

function toStoryblokImage(url, width) {
  if (!url) return "";
  if (!url.includes("a.storyblok.com") && !url.includes("img2.storyblok.com")) {
    return url;
  }
  return `${url}/m/${width}x0/filters:quality(85):format(webp)`;
}

const bodyHtml = computed(() =>
  props.blok?.body ? renderRichText(props.blok.body) : "",
);

const heroImage = computed(() =>
  toStoryblokImage(props.blok?.image?.filename || "", 1800),
);

const galleryImages = computed(() => {
  const items = props.blok?.gallery;
  return Array.isArray(items) ? items.filter((image) => image?.filename) : [];
});

const swiperModules = [Navigation, Keyboard, Zoom];
const mobileGalleryModules = [EffectCards];
const isLightboxOpen = ref(false);
const activeSlideIndex = ref(0);
const activeMobileGalleryIndex = ref(0);
const lightboxSwiper = ref(null);

const galleryLineFill = computed(() => {
  const total = galleryImages.value.length;
  if (!total) return 0;
  return ((activeMobileGalleryIndex.value + 1) / total) * 100;
});

const handleOverlayKeydown = (event) => {
  if (event.key === "Escape") {
    closeLightbox();
  }
};

function openLightbox(index) {
  if (!process.client || window.innerWidth < 768) return;

  activeMobileGalleryIndex.value = index;
  activeSlideIndex.value = index;
  isLightboxOpen.value = true;
  nextTick(() => {
    if (lightboxSwiper.value) {
      lightboxSwiper.value.slideTo(index, 0);
    }
  });
}

function closeLightbox() {
  isLightboxOpen.value = false;
}

function onLightboxSwiper(swiper) {
  lightboxSwiper.value = swiper;
  swiper.slideTo(activeSlideIndex.value, 0);
}

function onSlideChange(swiper) {
  activeSlideIndex.value = swiper.activeIndex;
  activeMobileGalleryIndex.value = swiper.activeIndex;
}

function onMobileGallerySlideChange(swiper) {
  activeMobileGalleryIndex.value = swiper.activeIndex;
}

watch(isLightboxOpen, (isOpen) => {
  document.body.style.overflow = isOpen ? "hidden" : "";
  if (isOpen) {
    window.addEventListener("keydown", handleOverlayKeydown);
    return;
  }
  window.removeEventListener("keydown", handleOverlayKeydown);
});

onBeforeUnmount(() => {
  window.removeEventListener("keydown", handleOverlayKeydown);
  document.body.style.overflow = "";
});

watch(galleryImages, (images) => {
  if (!images.length) {
    activeMobileGalleryIndex.value = 0;
    return;
  }
  activeMobileGalleryIndex.value = Math.min(
    activeMobileGalleryIndex.value,
    images.length - 1,
  );
});
</script>

<style scoped>
.prose-news :deep(h2) {
  margin-top: 2.2rem;
  margin-bottom: 0.8rem;
  font-size: clamp(1.5rem, 2.2vw, 2rem);
  font-weight: 800;
  line-height: 1.2;
  color: #111;
}

.prose-news :deep(h3) {
  margin-top: 1.8rem;
  margin-bottom: 0.6rem;
  font-size: clamp(1.2rem, 1.7vw, 1.45rem);
  font-weight: 700;
  line-height: 1.3;
  color: #111;
}

.prose-news :deep(p) {
  margin-bottom: 1rem;
}

.prose-news :deep(ul),
.prose-news :deep(ol) {
  margin-bottom: 1.2rem;
  padding-left: 1.4rem;
}

.prose-news :deep(ul) {
  list-style: disc;
}

.prose-news :deep(ol) {
  list-style: decimal;
}

.prose-news :deep(a) {
  color: #986b14;
  text-underline-offset: 3px;
  text-decoration-thickness: 1.5px;
}

.prose-news :deep(a:hover) {
  color: #d0992a;
}

.news-lightbox :deep(.swiper-button-prev),
.news-lightbox :deep(.swiper-button-next) {
  color: #fff;
  width: 2.7rem;
  height: 2.7rem;
  border-radius: 999px;
  border: 1px solid rgba(255, 255, 255, 0.35);
  background: rgba(0, 0, 0, 0.35);
  backdrop-filter: blur(4px);
}

.news-lightbox :deep(.swiper-button-prev:hover),
.news-lightbox :deep(.swiper-button-next:hover) {
  border-color: rgba(208, 153, 42, 0.7);
  color: #d0992a;
}

.news-lightbox :deep(.swiper-button-prev::after),
.news-lightbox :deep(.swiper-button-next::after) {
  font-size: 1rem;
  font-weight: 800;
}

.mobile-gallery {
  margin-inline: auto;
  max-width: 24rem;
}

.mobile-gallery :deep(.swiper) {
  overflow: visible;
}

.mobile-gallery :deep(.swiper-slide) {
  overflow: hidden;
  border-radius: 1rem;
  box-shadow: 0 14px 28px rgba(0, 0, 0, 0.14);
}

.gallery-line {
  position: relative;
  height: 4px;
  width: 100%;
  border-radius: 999px;
  background:
    repeating-linear-gradient(
      to right,
      rgba(0, 0, 0, 0.18) 0,
      rgba(0, 0, 0, 0.18) 1px,
      transparent 1px,
      transparent calc(100% / var(--segments))
    ),
    rgba(0, 0, 0, 0.12);
}

.gallery-line-progress {
  position: absolute;
  top: 0;
  left: 0;
  height: 100%;
  border-radius: 999px;
  background: linear-gradient(90deg, #7c5a14 0%, #d0992a 100%);
  transition: width 0.25s ease;
}
</style>
