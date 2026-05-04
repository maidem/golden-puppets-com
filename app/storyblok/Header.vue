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
      <!-- Desktop -->
      <div class="hidden md:flex items-stretch min-h-[160px] pb-[2px]">
        <NuxtLink
          to="/"
          class="flex items-center shrink-0 w-[38.2%] bg-[#FFE600] rounded-br-[1.75rem] pl-8 md:pl-16"
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

        <div class="w-[2px] shrink-0"></div>

        <nav
          class="flex-1 items-center justify-end px-8 md:px-16 gap-8 bg-[#FFE600] rounded-bl-[1.75rem] flex"
        >
          <StoryblokComponent
            v-for="navItem in blok.nav_menu"
            :key="navItem._uid"
            :blok="navItem"
          />
        </nav>
      </div>

      <!-- Mobile -->
      <div class="md:hidden relative">
        <div
          class="bg-[#FFE600] min-h-[78px] rounded-b-[1.5rem] border-b-2 border-black px-4 flex items-center justify-between"
        >
          <NuxtLink to="/" class="flex items-center gap-3 min-w-0">
            <span
              class="w-9 h-9 rounded-full border-2 border-black flex items-center justify-center shrink-0"
            >
              <svg
                viewBox="0 0 24 24"
                class="w-5 h-5"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <path d="M4 12h16" />
                <path d="M12 4v16" />
              </svg>
            </span>

            <img
              v-if="blok.logo?.filename"
              :src="blok.logo.filename"
              :alt="blok.logo.alt || 'Logo'"
              class="h-8 w-auto"
            />
            <span
              v-else
              class="text-lg font-black tracking-tight text-black truncate"
            >
              GOLDEN<span class="text-gray-800">PUPPETS</span>
            </span>
          </NuxtLink>

          <button
            type="button"
            class="w-11 h-11 rounded-full border-2 border-black flex items-center justify-center"
            :aria-expanded="menuOpen"
            aria-controls="mobile-nav-overlay"
            aria-label="Navigation umschalten"
            @click="menuOpen = !menuOpen"
          >
            <span class="sr-only">Menue</span>
            <svg
              v-if="!menuOpen"
              viewBox="0 0 24 24"
              class="w-5 h-5"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <path d="M3 6h18" />
              <path d="M3 12h18" />
              <path d="M3 18h18" />
            </svg>
            <svg
              v-else
              viewBox="0 0 24 24"
              class="w-5 h-5"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <path d="M6 6l12 12" />
              <path d="M18 6l-12 12" />
            </svg>
          </button>
        </div>

        <Transition name="mobile-menu">
          <div
            v-if="menuOpen"
            id="mobile-nav-overlay"
            class="fixed inset-0 z-[80] bg-[#FFE600] mobile-overlay-full"
          >
            <div
              class="h-[78px] border-b-2 border-black px-4 flex items-center justify-between"
            >
              <div class="flex items-center gap-3 min-w-0">
                <span
                  class="w-9 h-9 rounded-full border-2 border-black flex items-center justify-center shrink-0"
                >
                  <svg
                    viewBox="0 0 24 24"
                    class="w-5 h-5"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                  >
                    <path d="M4 12h16" />
                    <path d="M12 4v16" />
                  </svg>
                </span>
                <span
                  class="text-lg font-black tracking-tight text-black truncate"
                  >GOLDENPUPPETS</span
                >
              </div>

              <button
                type="button"
                class="w-11 h-11 rounded-full border-2 border-black flex items-center justify-center"
                aria-label="Navigation schliessen"
                @click="menuOpen = false"
              >
                <svg
                  viewBox="0 0 24 24"
                  class="w-5 h-5"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <path d="M6 6l12 12" />
                  <path d="M18 6l-12 12" />
                </svg>
              </button>
            </div>

            <nav class="px-7 pt-8" @click="menuOpen = false">
              <div class="flex flex-col items-start gap-5">
                <StoryblokComponent
                  v-for="navItem in blok.nav_menu"
                  :key="navItem._uid"
                  :blok="navItem"
                />
              </div>
            </nav>
          </div>
        </Transition>
      </div>
    </header>
  </div>
</template>

<script setup>
const route = useRoute();
const props = defineProps({ blok: Object });
const menuOpen = ref(false);

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

watch(menuOpen, (isOpen) => {
  if (process.client) {
    document.body.style.overflow = isOpen ? "hidden" : "";
  }
});

watch(
  () => route.fullPath,
  () => {
    menuOpen.value = false;
  },
);

onBeforeUnmount(() => {
  if (process.client) document.body.style.overflow = "";
});
</script>

<style scoped>
.mobile-menu-enter-active,
.mobile-menu-leave-active {
  transition: opacity 0.2s ease;
}

.mobile-menu-enter-active .mobile-overlay-full nav,
.mobile-menu-leave-active .mobile-overlay-full nav {
  transition:
    transform 0.28s cubic-bezier(0.2, 0.9, 0.2, 1),
    opacity 0.2s ease;
}

.mobile-menu-enter-from,
.mobile-menu-leave-to {
  opacity: 0;
}

.mobile-menu-enter-from .mobile-overlay-full nav,
.mobile-menu-leave-to .mobile-overlay-full nav {
  transform: translateY(-8px);
  opacity: 0;
}

.mobile-overlay-full :deep(a) {
  font-size: 1.25rem !important;
  line-height: 1.3;
  padding: 0 !important;
}

.mobile-overlay-full :deep(a::after) {
  left: 0 !important;
  right: 0 !important;
}
</style>
