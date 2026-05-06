<template>
  <section
    v-editable="blok"
    :id="blok.section_id || 'home'"
    class="relative flex items-center justify-center min-h-[90vh] overflow-hidden bg-black"
  >
    <!-- Full-width B&W background image -->
    <div
      v-if="blok.bg_image?.filename"
      class="absolute inset-0 bg-cover bg-center bg-no-repeat"
      style="filter: grayscale(1) contrast(1.2) brightness(0.45)"
      :style="{ backgroundImage: `url(${blok.bg_image.filename})` }"
    />
    <!-- vignette for premium look -->
    <div
      class="absolute inset-0"
      style="
        background: radial-gradient(
          ellipse at center,
          transparent 30%,
          rgba(0, 0, 0, 0.65) 100%
        );
      "
    />
    <div
      class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/20"
    />

    <!-- Logo with speed-from-left effect -->
    <div class="relative z-10 flex items-center justify-center">
      <!-- Speed lines trailing left -->
      <div class="speed-trail" aria-hidden="true">
        <span class="line line-1" />
        <span class="line line-2" />
        <span class="line line-3" />
        <span class="line line-4" />
        <span class="line line-5" />
      </div>

      <!-- Logo circle -->
      <div id="hero-logo-circle" class="logo-ring">
        <img
          v-if="blok.logo?.filename"
          :src="logoSrc"
          :alt="blok.logo?.alt || 'Golden Puppets'"
          class="w-full h-full object-contain p-3"
          loading="eager"
        />
        <div
          v-else
          class="w-full h-full bg-[#231F21] flex items-center justify-center"
        >
          <span class="text-[#D0992A] font-black text-4xl tracking-tight"
            >GP</span
          >
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
const props = defineProps({ blok: Object });

function toStoryblokImage(url, width) {
  if (!url) return "";
  if (!url.includes("a.storyblok.com") && !url.includes("img2.storyblok.com"))
    return url;
  return `${url}/m/${width}x0/filters:quality(85):format(webp)`;
}

const logoSrc = computed(() =>
  toStoryblokImage(props.blok?.logo?.filename || "", 600),
);
</script>

<style scoped>
.logo-ring {
  position: relative;
  width: 220px;
  height: 220px;
  border-radius: 50%;
  overflow: hidden;
  background-color: #231f21;
  border: 3px solid rgba(255, 255, 255, 0.35);
  box-shadow:
    0 0 0 1px rgba(208, 153, 42, 0.4),
    0 0 60px rgba(0, 0, 0, 0.6),
    0 0 120px rgba(208, 153, 42, 0.08);
}

@media (min-width: 768px) {
  .logo-ring {
    width: 380px;
    height: 380px;
    border-width: 4px;
  }
}

/* Speed trail container: positioned to the left of the circle */
.speed-trail {
  position: absolute;
  right: 100%;
  top: 50%;
  transform: translateY(-50%);
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 7px;
  padding-right: 2px;
  pointer-events: none;
}

.line {
  display: block;
  height: 2px;
  border-radius: 999px;
  background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.75));
  animation: streak 2.4s ease-in-out infinite;
}

.line-1 {
  width: 160px;
  opacity: 0.85;
  animation-delay: 0s;
}
.line-2 {
  width: 260px;
  height: 1px;
  opacity: 0.45;
  animation-delay: 0.15s;
}
.line-3 {
  width: 110px;
  height: 3px;
  opacity: 0.9;
  animation-delay: 0.05s;
}
.line-4 {
  width: 200px;
  height: 1px;
  opacity: 0.3;
  animation-delay: 0.25s;
}
.line-5 {
  width: 140px;
  opacity: 0.6;
  animation-delay: 0.1s;
}

@keyframes streak {
  0%,
  100% {
    opacity: var(--base-opacity, 0.5);
    transform: scaleX(1);
  }
  50% {
    opacity: calc(var(--base-opacity, 0.5) * 0.4);
    transform: scaleX(0.85);
  }
}
</style>
