<template>
  <div v-editable="blok">
    <!-- Event-Leiste (über der Navigation) -->
    <div v-if="showEventBar" class="bg-gray-900 text-white text-xs">
      <div
        class="container mx-auto px-4 py-2 flex flex-wrap items-center gap-x-6 gap-y-1"
      >
        <StoryblokComponent
          v-for="event in visibleEvents"
          :key="event._uid"
          :blok="event"
        />
      </div>
    </div>

    <header
      class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-100 py-4"
    >
      <div class="container mx-auto flex items-center justify-between px-4">
        <!-- Logo -->
        <NuxtLink to="/" class="flex items-center gap-2">
          <img
            v-if="blok.logo?.filename"
            :src="blok.logo.filename"
            :alt="blok.logo.alt || 'Logo'"
            class="h-10 w-auto"
          />
          <span
            v-else
            class="text-2xl font-black tracking-tighter text-[#00b3b0]"
          >
            GOLDEN<span class="text-gray-900">PUPPETS</span>
          </span>
        </NuxtLink>

        <!-- Navigation -->
        <nav class="hidden md:flex items-center space-x-2">
          <StoryblokComponent
            v-for="navItem in blok.nav_menu"
            :key="navItem._uid"
            :blok="navItem"
          />
        </nav>

        <!-- Mobile Button Placeholder -->
        <div class="md:hidden">
          <BaseButton variant="outline" class="px-4 py-2">Menu</BaseButton>
        </div>
      </div>
    </header>
  </div>
</template>

<script setup>
const props = defineProps({ blok: Object });

const visibleEvents = computed(() => {
  const items = Array.isArray(props.blok?.event_bar)
    ? props.blok.event_bar
    : [];
  return items.filter((e) => e?.visible !== false);
});

const showEventBar = computed(
  () =>
    props.blok?.event_bar_visible !== false && visibleEvents.value.length > 0,
);
</script>
