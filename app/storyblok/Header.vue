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

    <header class="sticky top-0 z-50 bg-black">
      <div class="flex items-stretch min-h-[160px] pb-[2px]">
        <!-- Logo: gelbe Box, nur rechts unten abgerundet (links = Screenrand) -->
        <NuxtLink
          to="/"
          class="flex items-center shrink-0 bg-[#FFE600] rounded-br-[1.75rem] pl-8 md:pl-16"
          style="width: 704px"
        >
          <img
            v-if="blok.logo?.filename"
            :src="blok.logo.filename"
            :alt="blok.logo.alt || 'Logo'"
            class="h-12 w-auto"
          />
          <span v-else class="text-2xl font-black tracking-tighter text-black">
            GOLDEN<span class="text-gray-800">PUPPETS</span>
          </span>
        </NuxtLink>

        <!-- 2px schwarzer Spalt in der Mitte -->
        <div class="w-[2px] shrink-0"></div>

        <!-- Navigation: gelbe Box, nur links unten abgerundet (rechts = Screenrand) -->
        <nav
          class="hidden md:flex flex-1 items-center justify-end px-8 md:px-16 gap-8 bg-[#FFE600] rounded-bl-[1.75rem]"
        >
          <StoryblokComponent
            v-for="navItem in blok.nav_menu"
            :key="navItem._uid"
            :blok="navItem"
          />
        </nav>

        <!-- Mobile Button -->
        <div class="md:hidden flex items-center px-4 bg-[#FFE600]">
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
