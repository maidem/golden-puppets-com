<template>
  <div>
    <!-- Event-Leiste -->
    <div v-if="showEventBar" class="bg-black text-white text-xs">
      <div
        class="max-w-[1400px] mx-auto px-4 md:px-16 py-2 flex flex-wrap items-center gap-x-6 gap-y-1"
      >
        <StoryblokComponent
          v-for="event in visibleEvents"
          :key="event._uid"
          :blok="event"
        />
      </div>
    </div>

    <!-- Desktop Header -->
    <header
      class="fixed top-0 inset-x-0 z-50 hidden md:block bg-[#231F21] px-5 md:px-16"
    >
      <div
        class="max-w-[1400px] mx-auto h-16 flex items-center justify-between"
      >
        <!-- Logo: erscheint wenn Hero-Logo aus Viewport -->
        <Transition name="logo-drop">
          <NuxtLink
            v-if="shouldShowNavLogo"
            to="/"
            class="flex items-center shrink-0"
          >
            <img
              v-if="blok.logo?.filename"
              :src="logoSrcDesktop"
              :alt="blok.logo.alt || 'Golden Puppets'"
              class="h-9 w-auto"
              loading="eager"
            />
            <span
              v-else
              class="text-white font-black tracking-widest text-sm uppercase"
            >
              Golden <span class="text-[#D0992A]">Puppets</span>
            </span>
          </NuxtLink>
        </Transition>

        <!-- Nav rechts -->
        <nav class="flex items-center gap-3 ml-auto text-white">
          <NuxtLink
            v-for="navItem in blok.nav_menu"
            :key="navItem._uid"
            :to="navHref(navItem)"
            class="relative inline-block text-white font-semibold px-3 py-2 text-[0.82rem] md:text-[0.9rem] tracking-[0.02em] pointer-events-auto after:absolute after:bottom-0 after:left-3 after:right-3 after:h-[2px] after:bg-white after:transition-all after:duration-300 after:scale-x-0 after:origin-left hover:after:scale-x-100"
            >{{ navItem.label }}</NuxtLink
          >
        </nav>
      </div>
    </header>

    <!-- Mobile Header -->
    <header class="fixed top-0 inset-x-0 z-50 md:hidden bg-[#231F21]">
      <div class="h-14 px-5 flex items-center justify-between">
        <!-- Logo -->
        <NuxtLink v-if="shouldShowNavLogo" to="/" class="flex items-center">
          <img
            v-if="blok.logo?.filename"
            :src="logoSrcMobile"
            :alt="blok.logo.alt || 'Golden Puppets'"
            class="h-8 w-auto"
            loading="eager"
          />
          <span
            v-else
            class="text-white font-black tracking-widest text-sm uppercase"
          >
            GP
          </span>
        </NuxtLink>
        <div v-else class="h-8 w-16" aria-hidden="true" />

        <!-- Hamburger -->
        <button
          type="button"
          class="flex flex-col justify-center gap-[5px] w-8 h-8"
          :aria-expanded="menuOpen"
          aria-label="Navigation umschalten"
          @click="menuOpen = !menuOpen"
        >
          <span
            class="block h-[2px] bg-white rounded-full transition-all duration-300 origin-center"
            :class="menuOpen ? 'rotate-45 translate-y-[7px]' : ''"
          />
          <span
            class="block h-[2px] bg-white rounded-full transition-all duration-300"
            :class="menuOpen ? 'opacity-0 scale-x-0' : ''"
          />
          <span
            class="block h-[2px] bg-white rounded-full transition-all duration-300 origin-center"
            :class="menuOpen ? '-rotate-45 -translate-y-[7px]' : ''"
          />
        </button>
      </div>

      <!-- Mobile Full-Page Menu Overlay -->
      <Teleport to="body">
        <div
          v-if="menuOpen"
          class="fixed inset-0 z-[49] md:hidden bg-[#FFFFFF] flex items-end"
          @click="menuOpen = false"
        >
          <nav
            class="w-full pb-8 pr-5 flex flex-col gap-2 items-end pointer-events-auto"
            @click.stop
          >
            <NuxtLink
              v-for="navItem in blok.nav_menu"
              :key="navItem._uid"
              :to="navHref(navItem)"
              class="relative inline-block text-black font-semibold px-6 py-3 text-lg tracking-[0.02em] pointer-events-auto hover:text-[#D0992A] transition-colors duration-200"
              @click="menuOpen = false"
              >{{ navItem.label }}</NuxtLink
            >
          </nav>
        </div>
      </Teleport>
    </header>
  </div>
</template>

<script setup>
const route = useRoute();
const props = defineProps({ blok: Object });
const menuOpen = ref(false);
const showNavLogoByHero = ref(false);
let heroObserver = null;

const isHomePage = computed(() => route.path === "/");
const shouldShowNavLogo = computed(
  () => !isHomePage.value || showNavLogoByHero.value,
);

const visibleEvents = computed(() => {
  const items = Array.isArray(props.blok?.event_bar)
    ? props.blok.event_bar
    : [];
  return items.filter((e) => e?.visible !== false);
});

const logoSrc = computed(() => props.blok?.logo?.filename || "");

function toStoryblokImage(url, width) {
  if (!url) return "";
  if (!url.includes("a.storyblok.com") && !url.includes("img2.storyblok.com"))
    return url;
  return `${url}/m/${width}x0/filters:quality(85):format(webp)`;
}

const logoSrcDesktop = computed(() => toStoryblokImage(logoSrc.value, 240));
const logoSrcMobile = computed(() => toStoryblokImage(logoSrc.value, 160));

const showEventBar = computed(
  () =>
    props.blok?.event_bar_visible !== false && visibleEvents.value.length > 0,
);

function navHref(item) {
  if (item?.section_id) return `/#${item.section_id}`;
  const url = item?.link?.cached_url || item?.link?.url || "";
  if (!url || url === "home") return "/";
  if (url.startsWith("#")) return `/${url}`;
  return url.startsWith("/") ? url : `/${url}`;
}

function disconnectHeroObserver() {
  if (heroObserver) {
    heroObserver.disconnect();
    heroObserver = null;
  }
}

function syncHeroObserver() {
  disconnectHeroObserver();

  if (!isHomePage.value) {
    showNavLogoByHero.value = true;
    return;
  }

  showNavLogoByHero.value = false;

  nextTick(() => {
    const heroLogo = document.getElementById("hero-logo-circle");
    if (!heroLogo) return;
    heroObserver = new IntersectionObserver(
      ([entry]) => {
        showNavLogoByHero.value = !entry.isIntersecting;
      },
      { threshold: 0, rootMargin: "-64px 0px 0px 0px" },
    );
    heroObserver.observe(heroLogo);
  });
}

onMounted(syncHeroObserver);

watch(menuOpen, (isOpen) => {
  if (process.client) document.body.style.overflow = isOpen ? "hidden" : "";
});

watch(
  () => route.fullPath,
  () => {
    menuOpen.value = false;
    syncHeroObserver();
  },
);

onBeforeUnmount(() => {
  disconnectHeroObserver();
  if (process.client) document.body.style.overflow = "";
});
</script>

<style scoped>
/* Logo drop-in from top */
.logo-drop-enter-active {
  transition:
    transform 0.45s cubic-bezier(0.2, 1.4, 0.4, 1),
    opacity 0.3s ease;
}
.logo-drop-leave-active {
  transition:
    transform 0.2s ease,
    opacity 0.2s ease;
}
.logo-drop-enter-from {
  transform: translateY(-140%);
  opacity: 0;
}
.logo-drop-leave-to {
  transform: translateY(-60%);
  opacity: 0;
}

/* Nav link styling */
header :deep(a) {
  color: #fff !important;
  font-size: 0.78rem !important;
  font-weight: 600;
  letter-spacing: 0.04em;
  text-transform: uppercase;
}

/* Router-Link active-Klasse soll keinen extra Stil zeigen */
header :deep(a.router-link-active),
header :deep(a.router-link-exact-active) {
  color: #fff !important;
}

/* Mobile links larger */
nav.flex-col :deep(a) {
  font-size: 1.1rem !important;
  padding: 0.6rem 0 !important;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  display: block;
  width: 100%;
}
</style>
