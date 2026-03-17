<template>
  <main>
    <div v-if="story">
      <StoryblokComponent :blok="story.content" />
    </div>
    <div v-else-if="error" class="p-12 text-center text-red-500">
      {{ error }}
    </div>
    <div v-else class="p-12 text-center text-gray-500">Lade...</div>
  </main>
</template>

<script setup lang="ts">
const storyblokApi = useStoryblokApi();
const route = useRoute();
const slug = route.params.slug;
const url = slug && slug.length > 0 ? slug.join("/") : "home";

const runtimeConfig = useRuntimeConfig();
const version = runtimeConfig.public.storyblokVersion as "draft" | "published";

const { data } = await useAsyncData(`story-${url}`, async () => {
  try {
    const response = await storyblokApi.get(`cdn/stories/${url}`, { version });
    return response.data.story;
  } catch (e: any) {
    return null;
  }
});

const story = ref(data.value);

onMounted(() => {
  if (story.value) {
    useStoryblokBridge(story.value.id, (newStory) => (story.value = newStory));
  }
});
</script>
