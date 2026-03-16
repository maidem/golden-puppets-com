<template>
  <main>
    <div v-if="story">
       <StoryblokComponent :blok="story.content" />
    </div>
    <div v-else-if="error" class="p-12 text-center text-red-500">
      {{ error }}
    </div>
    <div v-else class="p-12 text-center text-gray-500">
      Lade...
    </div>
  </main>
</template>

<script setup>
const storyblokApi = useStoryblokApi()
const route = useRoute()
const slug = route.params.slug
const url = slug && slug.length > 0 ? slug.join('/') : 'home'

const { data } = await storyblokApi.get(`cdn/stories/${url}`, {
  version: 'draft',
})

const story = ref(data.story)

onMounted(() => {
  useStoryblokBridge(story.value.id, (newStory) => (story.value = newStory))
})
</script>
