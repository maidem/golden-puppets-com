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
        <NewsCard
          v-for="article in displayedArticles"
          :key="article.slug"
          :image="article.image"
          :headline="article.headline"
          :teaser-text="article.teaserText"
          :link="article.slug"
        />
      </div>

      <!-- Mobile Swiper - nur im Homepage-Modus (alle Artikel für korrektes Loop) -->
      <div v-if="isHomepageMode && mappedArticles.length > 0" class="md:hidden">
        <Swiper
          v-if="mappedArticles.length >= 3"
          :modules="mobileModules"
          effect="cards"
          :cards-effect="{
            perSlideRotate: 0,
            perSlideOffset: 10,
            slideShadows: false,
          }"
          :loop="true"
          :grab-cursor="true"
        >
          <SwiperSlide
            v-for="(article, i) in mappedArticles"
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
import { computed, ref } from "vue";
import { EffectCards } from "swiper/modules";
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";
import "swiper/css/effect-cards";
import NewsCard from "~/components/molecules/NewsCard.vue";

const props = defineProps({ blok: Object });
const route = useRoute();

const storyblokApi = useStoryblokApi();
const runtimeConfig = useRuntimeConfig();
const selectedTag = ref(null);

const displayLimit = 3;
const mobileModules = [EffectCards];

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
    : "pt-24 pb-16 md:pt-32 md:pb-24 bg-[#f5f4f2]";
});

// Fetch all articles
const { data } = useAsyncData(
  `news-overview-${props.blok.folder_slug || "aktuelles"}`,
  () =>
    storyblokApi.get("cdn/stories", {
      version: runtimeConfig.public.storyblokVersion,
      starts_with: props.blok.folder_slug || "aktuelles",
      content_type: "news_article",
      sort_by: "created_at:desc",
    }),
);

const mappedArticles = computed(() =>
  (data.value?.data?.stories || []).map((story) => ({
    slug: story.full_slug,
    image: story.content.image?.filename || "",
    headline: story.content.headline || story.name,
    teaserText: story.content.teaser_text || "",
    // Native Storyblok story tags (set per story in the Storyblok editor)
    tags: story.tag_list || [],
  })),
);

// Filter-Tags: from block textarea field (comma-separated) or auto-detect from articles
const filterTags = computed(() => {
  if (props.blok.available_tags) {
    return props.blok.available_tags
      .split(",")
      .map((t) => t.trim())
      .filter(Boolean);
  }
  // Fallback: auto-detect from story tags
  const tags = new Set();
  mappedArticles.value.forEach((a) => a.tags.forEach((t) => tags.add(t)));
  return Array.from(tags).sort();
});

// Filter articles based on selected tag
const filteredArticles = computed(() => {
  if (!selectedTag.value) return mappedArticles.value;
  return mappedArticles.value.filter((a) => a.tags.includes(selectedTag.value));
});

// Display articles - limited on homepage, all on overview page
const displayedArticles = computed(() => {
  if (isHomepageMode.value) {
    return filteredArticles.value.slice(0, displayLimit);
  }
  return filteredArticles.value;
});
</script>

<style scoped>
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
  margin-left: auto;
  margin-right: auto;
}

.news-line-progress {
  position: absolute;
  left: 0;
  top: 0;
  height: 100%;
  border-radius: 999px;
  background: linear-gradient(90deg, #7c5a14 0%, #d0992a 100%);
  transition: width 0.25s ease;
}
</style>
