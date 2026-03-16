<template>
  <div>
    <!-- Header/Navigation -->
    <header v-if="config?.content" class="sticky top-0 z-50 bg-white shadow-sm">
       <StoryblokComponent :blok="config.content" />
    </header>

    <!-- Main Page Content -->
    <NuxtPage />
  </div>
</template>

<script setup>
const storyblokApi = useStoryblokApi()

// Fetch global configuration (Header, Footer, etc.)
// Wir nutzen useAsyncData um sicherzustellen, dass es auch bei SSR geladen wird
const { data } = await useAsyncData('storyblok-config', async () => {
  try {
    const response = await storyblokApi.get('cdn/stories/config', {
      version: 'draft',
      resolve_links: 'url'
    })
    return response.data.story
  } catch (e) {
    console.error("Fehler beim Laden der Config-Story:", e)
    return null
  }
})

const config = ref(data.value)

// Bridge for real-time updates of the config
onMounted(() => {
  if (config.value) {
    useStoryblokBridge(config.value.id, (newConfig) => {
      config.value = newConfig
    })
  }
})
</script>
