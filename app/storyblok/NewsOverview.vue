<template>
  <section
    v-editable="blok"
    :id="blok.section_id || 'aktuelles'"
    :class="['px-5 md:px-16', sectionSpacingClass]"
  >
    <div class="max-w-[1400px] mx-auto">
      <!-- Headline -->
      <div :class="isHomepageMode ? 'mb-7' : 'mb-8'">
        <h2
          v-if="isHomepageMode"
          class="text-2xl md:text-4xl font-bold leading-[1.1] tracking-tight text-black mb-7"
        >
          {{ blok.headline || "Aktuelles"
          }}<span class="text-[#D0992A]">.</span>
        </h2>
        <h1
          v-else
          class="text-3xl md:text-5xl font-bold leading-[1.1] tracking-tight text-black"
        >
          {{ blok.headline || "Alle Beiträge"
          }}<span class="text-[#D0992A]">.</span>
        </h1>
        <p v-if="blok.subline" class="mt-3 text-gray-600 text-base">
          {{ blok.subline }}
        </p>
      </div>

      <!-- Filter Section - nur im Überseite-Modus -->
      <div
        v-if="!isHomepageMode && blok.enable_filters && filterTags.length > 0"
        class="mb-10"
      >
        <div class="flex flex-wrap gap-2">
          <button
            @click="selectedTag = null"
            :class="[
              'px-4 py-2 rounded-full font-semibold transition-colors duration-200',
              selectedTag === null
                ? 'bg-[#231F21] text-white'
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200',
            ]"
          >
            Alle
          </button>
          <button
            v-for="tag in filterTags"
            :key="tag"
            @click="selectedTag = tag"
            :class="[
              'px-4 py-2 rounded-full font-semibold transition-colors duration-200',
              selectedTag === tag
                ? 'bg-[#231F21] text-white'
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200',
            ]"
          >
            {{ tag }}
          </button>
        </div>
      </div>

      <!-- Articles Grid -->
      <div
        v-if="displayedArticles.length > 0"
        :class="
          isHomepageMode
            ? 'hidden md:grid grid-cols-3 gap-6'
            : 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6'
        "
      >
        <div
          v-for="article in displayedArticles"
          :key="article.slug"
          v-editable="article.blok"
        >
          <NewsCard
            :image="article.image"
            :headline="article.headline"
            :teaser-text="article.teaserText"
            :link="article.slug"
          />
        </div>
      </div>

      <!-- Mobile Swiper - nur im Homepage-Modus -->
      <div v-if="isHomepageMode && mappedArticles.length > 0" class="md:hidden">
        <!-- Navigation oben rechts -->
        <div class="flex justify-end items-center gap-2 mb-3">
          <button
            @click="newsMobileSwiper?.slidePrev()"
            class="w-9 h-9 flex items-center justify-center rounded-full bg-[#231F21] text-white font-bold text-lg hover:bg-black transition-colors"
            aria-label="Vorheriger Artikel"
          >
            ‹
          </button>
          <button
            @click="newsMobileSwiper?.slideNext()"
            class="w-9 h-9 flex items-center justify-center rounded-full bg-[#231F21] text-white font-bold text-lg hover:bg-black transition-colors"
            aria-label="Nächster Artikel"
          >
            ›
          </button>
        </div>
        <Swiper
          v-if="mappedArticles.length > 0"
          :loop="mappedArticles.length > 1"
          :grab-cursor="true"
          :slides-per-view="1"
          @swiper="onNewsMobileSwiper"
        >
          <SwiperSlide
            v-for="(article, i) in mappedArticles"
            :key="`${article.slug || 'article'}-${i}`"
          >
            <div v-editable="article.blok">
              <NewsCard
                :image="article.image"
                :headline="article.headline"
                :teaser-text="article.teaserText"
                :link="article.slug"
              />
            </div>
          </SwiperSlide>
        </Swiper>
      </div>

      <!-- Pagination - nur im Übersichts-Modus -->
      <div
        v-if="!isHomepageMode && totalPages > 1"
        class="mt-10 flex justify-center items-center gap-2"
      >
        <button
          @click="currentPage--"
          :disabled="currentPage === 1"
          class="w-9 h-9 flex items-center justify-center rounded-full bg-[#231F21] text-white font-bold text-lg disabled:opacity-30 hover:bg-black transition-colors"
          aria-label="Vorherige Seite"
        >
          ‹
        </button>
        <button
          v-for="p in totalPages"
          :key="p"
          @click="currentPage = p"
          :class="[
            'w-9 h-9 flex items-center justify-center rounded-full font-semibold text-sm transition-colors',
            p === currentPage
              ? 'bg-[#231F21] text-white'
              : 'bg-gray-100 text-gray-700 hover:bg-gray-200',
          ]"
        >
          {{ p }}
        </button>
        <button
          @click="currentPage++"
          :disabled="currentPage === totalPages"
          class="w-9 h-9 flex items-center justify-center rounded-full bg-[#231F21] text-white font-bold text-lg disabled:opacity-30 hover:bg-black transition-colors"
          aria-label="Nächste Seite"
        >
          ›
        </button>
      </div>

      <!-- Show more button - nur im Homepage-Modus -->
      <div
        v-if="isHomepageMode && mappedArticles.length > displayLimit"
        class="mt-10 md:mt-12 flex justify-center"
      >
        <NuxtLink
          to="/#aktuelles"
          class="inline-flex items-center gap-2 bg-[#231F21] text-white font-bold text-base px-8 py-3 rounded-full border-2 border-[#231F21] hover:bg-black hover:border-black transition-colors duration-200"
        >
          Alle Beiträge anschauen
        </NuxtLink>
      </div>

      <!-- No Results -->
      <div v-else-if="displayedArticles.length === 0" class="text-center py-12">
        <p class="text-gray-600 text-lg">Keine Beiträge gefunden.</p>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed, ref, watch } from "vue";
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";
import NewsCard from "~/components/molecules/NewsCard.vue";

const props = defineProps({ blok: Object });
const route = useRoute();

const storyblokApi = useStoryblokApi();
const runtimeConfig = useRuntimeConfig();
const selectedTag = ref(null);
const currentPage = ref(1);
const newsMobileSwiper = ref(null);

const ITEMS_PER_PAGE = 10;
const displayLimit = 3;

function onNewsMobileSwiper(swiper) {
  newsMobileSwiper.value = swiper;
}

// Determine if this is homepage mode or overview page mode
const isHomepageMode = computed(() => props.blok.is_overview_page !== true);

const isHomeAktuellesSection = computed(() => {
  const isHomeRoute = route.path === "/";
  const isAktuellesBlock =
    (props.blok.section_id || "") === "aktuelles" ||
    (props.blok.folder_slug || "") === "aktuelles";

  return isHomeRoute && isAktuellesBlock;
});

const sectionSpacingClass = computed(() => {
  if (isHomeAktuellesSection.value) {
    return "relative -mt-12 md:-mt-16 pt-8 pb-16 md:pt-12 md:pb-24 bg-[#f5f4f2]";
  }

  return isHomepageMode.value
    ? "py-10 md:py-16 bg-[#f5f4f2]"
    : "pt-8 pb-16 md:pt-12 md:pb-24 bg-[#f5f4f2]";
});

// Fetch all articles (per_page: 100 to avoid default limit)
const { data } = useAsyncData(
  `news-overview-${props.blok.folder_slug || "aktuelles"}`,
  () =>
    storyblokApi.get("cdn/stories", {
      version: runtimeConfig.public.storyblokVersion,
      starts_with: props.blok.folder_slug || "aktuelles",
      content_type: "news_article",
      sort_by: "created_at:desc",
      per_page: 100,
    }),
);

// Fetch categories from datasource (single source of truth)
const { data: datasourceData } = useAsyncData(
  "news-kategorien-datasource",
  () =>
    storyblokApi.get("cdn/datasource_entries", {
      datasource: "news-kategorien",
      per_page: 100,
      version: runtimeConfig.public.storyblokVersion,
    }),
);

const mappedArticles = computed(() =>
  (data.value?.data?.stories || []).map((story) => ({
    slug: story.full_slug,
    image: story.content.image?.filename || "",
    headline: story.content.headline || story.name,
    teaserText: story.content.teaser_text || "",
    tags: story.content.tags?.length
      ? story.content.tags
      : story.tag_list || [],
    blok: story.content,
  })),
);

// Filter-Tags: Datasource hat Vorrang, dann available_tags-Override, dann Auto-Detect
const filterTags = computed(() => {
  // 1. Override via available_tags textarea (für Sonderfälle)
  if (props.blok.available_tags) {
    return props.blok.available_tags
      .split(",")
      .map((t) => t.trim())
      .filter(Boolean);
  }
  // 2. Aus Datasource "news-kategorien"
  const dsEntries = datasourceData.value?.data?.datasource_entries;
  if (dsEntries?.length) {
    return dsEntries.map((e) => e.value).filter(Boolean);
  }
  // 3. Fallback: auto-detect from article tags
  const tags = new Set();
  mappedArticles.value.forEach((a) => a.tags.forEach((t) => tags.add(t)));
  return Array.from(tags).sort();
});

// Filter articles
const filteredArticles = computed(() => {
  if (!selectedTag.value) return mappedArticles.value;
  return mappedArticles.value.filter((a) => a.tags.includes(selectedTag.value));
});

// Reset page on filter change (no scroll to top)
watch(selectedTag, () => {
  currentPage.value = 1;
});

const totalPages = computed(() =>
  Math.max(1, Math.ceil(filteredArticles.value.length / ITEMS_PER_PAGE)),
);

const paginatedArticles = computed(() => {
  const start = (currentPage.value - 1) * ITEMS_PER_PAGE;
  return filteredArticles.value.slice(start, start + ITEMS_PER_PAGE);
});

// Display articles
const displayedArticles = computed(() => {
  if (isHomepageMode.value)
    return filteredArticles.value.slice(0, displayLimit);
  return paginatedArticles.value;
});
</script>

<style scoped></style>
