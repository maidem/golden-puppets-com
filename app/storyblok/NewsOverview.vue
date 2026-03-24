<template>
  <div v-editable="blok" class="py-20 px-4 bg-gray-50/50">
    <div class="max-w-7xl mx-auto">
      <!-- Sektions-Überschrift -->
      <h2 class="text-3xl md:text-5xl font-black text-center mb-16 text-gray-900 tracking-tight">
        {{ blok.headline || 'Aktuelles' }}
      </h2>

      <!-- News-Grid -->
      <div
        v-if="mappedArticles.length"
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10"
      >
        <article
          v-for="article in mappedArticles"
          :key="article.slug"
          class="bg-white rounded-[2rem] shadow-2xl hover:shadow-3xl transition-all duration-300 border border-gray-100/50 overflow-hidden flex flex-col group cursor-pointer"
        >
          <!-- Bild-Bereich -->
          <div class="aspect-video overflow-hidden bg-gray-100 flex-shrink-0">
            <img
              v-if="article.image"
              :src="article.image"
              :alt="article.headline"
              class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
            />
            <div
              v-else
              class="w-full h-full bg-gradient-to-br from-[#00b3b0]/10 to-[#00b3b0]/5 flex items-center justify-center"
            >
              <span class="text-[#00b3b0]/50 text-5xl transition-transform duration-500 group-hover:scale-125">🎭</span>
            </div>
          </div>

          <!-- Content-Bereich -->
          <div class="p-8 flex flex-col flex-grow">
            <h3 class="text-xl md:text-2xl font-black mb-4 text-gray-900 group-hover:text-[#00b3b0] transition-colors duration-300 leading-tight">
              {{ article.headline }}
            </h3>
            <p
              v-if="article.teaserText"
              class="text-gray-600/80 text-base leading-relaxed mb-8 flex-grow line-clamp-3"
            >
              {{ article.teaserText }}
            </p>

            <div class="mt-auto pt-2">
              <NuxtLink :to="`/${article.slug}`" class="inline-flex items-center gap-2 text-[#00b3b0] font-black group-hover:translate-x-1 transition-transform duration-300">
                <span>Mehr erfahren</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
              </NuxtLink>
            </div>
          </div>
        </article>
      </div>

      <!-- Leere-Meldung -->
      <p v-else class="text-center text-gray-400 text-xl font-medium animate-pulse">
        Gerade sind keine aktuellen Beiträge vorhanden…
      </p>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({ blok: Object })

const storyblokApi = useStoryblokApi()
const runtimeConfig = useRuntimeConfig()

const { data } = useAsyncData(
  `news-${props.blok.folder_slug || 'aktuelles'}`,
  () =>
    storyblokApi.get('cdn/stories', {
      version: runtimeConfig.public.storyblokVersion,
      starts_with: props.blok.folder_slug || 'aktuelles',
      content_type: 'news_article',
      sort_by: 'created_at:desc',
    }),
)

const mappedArticles = computed(() =>
  (data.value?.data?.stories || []).map((story) => ({
    slug: story.full_slug,
    image: story.content.image?.filename || '',
    headline: story.content.headline || story.name,
    teaserText: story.content.teaser_text || '',
  })),
)
</script>
