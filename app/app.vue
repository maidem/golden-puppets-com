<template>
  <div class="flex min-h-screen flex-col">
    <!-- Header/Navigation -->
    <StoryblokComponent v-if="config?.content" :blok="config.content" />

    <!-- Main Page Content -->
    <main :class="['flex-1', route.path !== '/' ? 'pt-14 md:pt-16' : '']">
      <NuxtPage />
    </main>

    <!-- Global Footer (aus config.content.footer[0]) -->
    <StoryblokComponent
      v-if="footerBlok"
      :key="footerBlok._uid"
      :blok="footerBlok"
    />
  </div>
</template>

<script setup lang="ts">
const storyblokApi = useStoryblokApi();
const route = useRoute();

// Fetch global configuration (Header, Footer, etc.)
// Wir nutzen useAsyncData um sicherzustellen, dass es auch bei SSR geladen wird
const runtimeConfig = useRuntimeConfig();
const version = runtimeConfig.public.storyblokVersion as "draft" | "published";

// SEO: No-Index auf der Preview-Seite (Netlify) + Canonical Link zur Live-Seite
useHead({
  meta: [
    {
      name: "robots",
      content: version === "draft" ? "noindex, nofollow" : "index, follow",
    },
  ],
  link: [
    {
      rel: "canonical",
      href: `https://golden-puppets.com${route.path}`,
    },
  ],
});

const { data } = await useAsyncData("storyblok-config", async () => {
  try {
    const response = await storyblokApi.get("cdn/stories/config", {
      version,
      resolve_links: "url",
    });
    return response.data.story;
  } catch (e) {
    console.error("Fehler beim Laden der Config-Story:", e);
    return null;
  }
});

const config = ref(data.value);

const footerBlok = computed(() => {
  const footer = config.value?.content?.footer;
  return Array.isArray(footer) && footer.length ? footer[0] : null;
});

// Bridge for real-time updates of the config
onMounted(() => {
  if (config.value) {
    useStoryblokBridge(config.value.id, (newConfig) => {
      config.value = newConfig;
    });
  }
});
</script>
