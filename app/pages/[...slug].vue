<template>
  <main>
    <div v-if="story">
      <StoryblokComponent :blok="story.content" />
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
const url = slugSegments.length > 0 ? slugSegments.join("/") : "home";

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

onMounted(() => {
  if (story.value) {
    useStoryblokBridge(story.value.id, (newStory) => (story.value = newStory));
  }
});
</script>
