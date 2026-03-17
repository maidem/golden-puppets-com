<template>
  <div v-editable="blok">
    <NewsSection
      :headline="blok.headline || 'Aktuelles'"
      :articles="mappedArticles"
    />
  </div>
</template>

<script setup>
const props = defineProps({ blok: Object })

const storyblokApi = useStoryblokApi()
const runtimeConfig = useRuntimeConfig()

const { data } = await useAsyncData(
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
