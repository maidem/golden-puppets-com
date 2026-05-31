<template>
  <main>
    <div v-if="story">
      <StoryblokComponent :blok="story.content" />
    </div>
    <div v-else-if="loadError" class="p-12 text-center text-red-500">
      {{ loadError }}
    </div>
    <div v-else class="p-12 text-center text-gray-500">
      Seite nicht gefunden.
    </div>
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
    try {
      const response = await storyblokApi.get(`cdn/stories/${url}`, {
        version,
      });
      return response.data.story;
    } catch (e: any) {
      if (e?.response?.status === 404 || e?.status === 404) return null;
      throw e;
    }
  },
);

const story = ref(data.value);

// Sync story when navigating client-side (Storyblok editor navigates the iframe
// programmatically, so the component is reused — data updates but story & bridge don't).
if (version === "draft") {
  watch(data, (newData) => {
    if (newData?.id && newData.id !== story.value?.id) {
      story.value = newData;
      if (process.client) {
        useStoryblokBridge(newData.id, (evStory) => {
          story.value = evStory;
        });
      }
    }
  });

  onMounted(() => {
    if (story.value?.id) {
      useStoryblokBridge(
        story.value.id,
        (newStory) => (story.value = newStory),
      );
    }
  });
}
</script>
