<template>
  <section
    v-editable="blok"
    class="relative flex items-center justify-center min-h-[80vh] bg-black"
  >
    <!-- Background image -->
    <div
      v-if="blok.bg_image?.filename"
      class="absolute inset-0 bg-cover bg-center bg-no-repeat grayscale contrast-125"
      :style="{ backgroundImage: `url(${blok.bg_image.filename})` }"
    />
    <!-- Gradient overlay -->
    <div class="absolute inset-0 bg-black/55" />

    <!-- Content: centered -->
    <div
      class="relative z-10 w-full px-5 md:px-16 py-[10vh] flex flex-col items-center text-center"
    >
      <!-- Yellow label / eyebrow -->
      <div
        v-if="blok.eyebrow"
        class="inline-block bg-[#FFE600] text-black text-xs font-bold tracking-widest uppercase px-3 py-1 mb-5 rounded-sm"
      >
        {{ blok.eyebrow }}
      </div>

      <!-- Headline -->
      <h1
        class="font-black leading-[1.05] tracking-tight mb-6 max-w-5xl mx-auto"
        style="font-size: clamp(1.75rem, 5vw, 4.5rem)"
      >
        <span class="text-white block">{{ blok.headline }}</span>
        <span v-if="blok.headline_accent" class="text-[#D0992A] block">{{
          blok.headline_accent
        }}</span>
      </h1>

      <!-- Subline -->
      <p
        v-if="blok.subline"
        class="text-white text-base md:text-xl font-medium leading-relaxed max-w-md mb-10 [text-shadow:0_2px_12px_rgba(0,0,0,0.8)]"
      >
        {{ blok.subline }}
      </p>

      <!-- CTA -->
      <div v-if="blok.cta_label">
        <NuxtLink
          v-if="resolvedLink"
          :to="resolvedLink"
          :target="isExternal ? '_blank' : undefined"
          :rel="isExternal ? 'noopener noreferrer' : undefined"
          class="inline-flex items-center gap-2 bg-[#D0992A] text-[#231F21] font-bold text-base px-7 py-3.5 rounded-full border-2 border-[#D0992A] hover:bg-transparent hover:text-[#D0992A] transition-colors duration-200"
        >
          {{ blok.cta_label }}
        </NuxtLink>
        <span
          v-else
          class="inline-flex items-center gap-2 bg-[#D0992A] text-[#231F21] font-bold text-base px-7 py-3.5 rounded-full border-2 border-transparent"
        >
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
</script>
