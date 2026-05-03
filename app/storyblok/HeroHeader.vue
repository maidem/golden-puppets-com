<template>
  <section
    v-editable="blok"
    class="relative flex flex-col items-center justify-center text-center px-4 min-h-[65vh] bg-gradient-to-br from-white to-gray-50 overflow-hidden"
  >
    <!-- Background image -->
    <div
      v-if="blok.bg_image?.filename"
      class="absolute inset-0 bg-cover bg-center bg-no-repeat grayscale contrast-125 brightness-75"
      :style="{ backgroundImage: `url(${blok.bg_image.filename})` }"
    />

    <!-- Overlay when bg image is set -->
    <div v-if="blok.bg_image?.filename" class="absolute inset-0 bg-black/40" />

    <!-- Content Container -->
    <div class="relative z-10 flex flex-col items-center max-w-4xl">
      <h1
        :class="[
          'mb-6 text-5xl md:text-7xl font-black tracking-tight',
          blok.bg_image?.filename
            ? 'text-white drop-shadow-md'
            : 'text-gray-900',
        ]"
      >
        {{ blok.headline }}
      </h1>

      <p
        v-if="blok.subline"
        :class="[
          'text-lg md:text-xl mb-10 leading-relaxed font-medium',
          blok.bg_image?.filename
            ? 'text-white/90 drop-shadow-sm'
            : 'text-gray-600',
        ]"
      >
        {{ blok.subline }}
      </p>

      <div v-if="blok.cta_label" class="flex flex-wrap justify-center gap-4">
        <NuxtLink
          v-if="resolvedLink"
          :to="resolvedLink"
          :target="isExternal ? '_blank' : undefined"
          :rel="isExternal ? 'noopener noreferrer' : undefined"
          :class="buttonStyles"
        >
          {{ blok.cta_label }}
        </NuxtLink>
        <span v-else :class="buttonStyles">
          {{ blok.cta_label }}
        </span>
      </div>
    </div>
  </section>
</template>

<script setup>
const props = defineProps({ blok: Object });

// Link resolution logic for Storyblok Links (URL, Story, Email)
const resolvedLink = computed(() => {
  const link = props.blok?.cta_link;
  if (!link || (!link.url && !link.cached_url && !link.email)) return "";

  if (link.linktype === "email") return `mailto:${link.email}`;
  if (link.linktype === "url") return link.url || "";

  // Handling internal stories
  const url = link.cached_url || "";
  if (!url) return "";
  return url.startsWith("/") ? url : `/${url}`;
});

const isExternal = computed(() => {
  return (
    resolvedLink.value.startsWith("http") ||
    resolvedLink.value.startsWith("mailto:")
  );
});

const buttonStyles = computed(() => {
  const base =
    "px-8 py-4 rounded-full font-bold transition-all duration-300 transform hover:scale-105 active:scale-95 shadow-lg";
  const variants = {
    primary: "bg-black text-white hover:bg-gray-900",
    secondary: "bg-gray-800 text-white hover:bg-gray-900",
    outline:
      "border-2 border-[#00b3b0] text-[#00b3b0] hover:bg-[#00b3b0] hover:text-white backdrop-blur-sm",
  };
  return `${base} ${variants[props.blok.cta_variant] || variants.primary}`;
});
</script>
