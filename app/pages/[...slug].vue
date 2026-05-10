<template>
  <main>
    <div v-if="story">
      <StoryblokComponent :blok="storyContentForRender" />
      <StoryblokComponent
        v-if="showHomepageNewsOverview"
        :blok="homepageNewsOverviewBlok"
      />
    </div>
    <div v-else-if="loadError" class="p-12 text-center text-red-500">
      {{ loadError }}
    </div>
    <div v-else class="p-12 text-center text-gray-500">Lade...</div>
  </main>
</template>

<script setup lang="ts">
const storyblokApi = useStoryblokApi();
const route = useRoute();
const slugParam = route.params.slug;
const slugSegments = Array.isArray(slugParam)
  ? slugParam
  : slugParam
    ? [slugParam]
    : [];
const isRootRoute = slugSegments.length === 0;
const url = slugSegments.length > 0 ? slugSegments.join("/") : "home";

if (url === "news") {
  await navigateTo("/#aktuelles", { redirectCode: 301 });
}

const runtimeConfig = useRuntimeConfig();
const version = runtimeConfig.public.storyblokVersion as "draft" | "published";

const { data, error: loadError } = await useAsyncData(
  `story-${url}`,
  async () => {
    const response = await storyblokApi.get(`cdn/stories/${url}`, { version });
    return response.data.story;
  },
);

const story = ref(data.value);

const defaultFilterTags = "Verein,Senioren,Sonstiges";

const storyContentForRender = computed(() => {
  const content = story.value?.content;
  if (!content || !isRootRoute) return content;

  const body = Array.isArray(content.body) ? content.body : [];
  const patchedBody = body.map((blok) => {
    if (blok?.component !== "news_overview") return blok;
    return {
      ...blok,
      section_id: blok.section_id || "aktuelles",
      is_overview_page: true,
      enable_filters: true,
      available_tags: blok.available_tags || defaultFilterTags,
    };
  });

  return {
    ...content,
    body: patchedBody,
  };
});

const homepageNewsOverviewBlok = {
  _uid: "home-news-overview-auto",
  component: "news_overview",
  headline: "Aktuelles",
  subline: "",
  folder_slug: "aktuelles",
  section_id: "aktuelles",
  is_overview_page: true,
  enable_filters: true,
  available_tags: defaultFilterTags,
};

const showHomepageNewsOverview = computed(() => {
  if (!isRootRoute || !story.value?.content) return false;

  const body = Array.isArray(story.value.content.body)
    ? story.value.content.body
    : [];

  const hasNewsOverviewInBody = body.some(
    (blok) => blok?.component === "news_overview",
  );

  return !hasNewsOverviewInBody;
});

onMounted(() => {
  if (story.value) {
    useStoryblokBridge(story.value.id, (newStory) => (story.value = newStory));
  }
});
</script>
