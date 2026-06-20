<template>
  <section
    v-editable="blok"
    :id="blok.section_id || 'home'"
    class="relative flex items-center justify-center min-h-[90vh] overflow-hidden bg-black"
  >
    <!-- Full-width B&W background image (img statt CSS-BG für Browser-Preload & WebP) -->
    <img
      v-if="blok.bg_image?.filename"
      :src="heroBgSrc"
      :alt="blok.bg_image.alt || ''"
      class="absolute inset-0 w-full h-full object-cover object-center"
      style="filter: grayscale(1) contrast(1.2) brightness(0.45)"
      loading="eager"
      fetchpriority="high"
      decoding="async"
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

    <!-- Logo with glow effect -->
    <div class="relative z-10 flex items-center justify-center">
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


const logoSrc = computed(() =>
  toStoryblokImage(props.blok?.logo?.filename || "", 600),
);

const heroBgSrc = computed(() =>
  toStoryblokImage(props.blok?.bg_image?.filename || "", 1920),
);
</script>

<style scoped>
.logo-ring {
  position: relative;
  z-index: 20;
  width: 220px;
  height: 220px;
  border-radius: 50%;
  overflow: hidden;
  background-color: #231f21;
  border: 3px solid rgba(255, 255, 255, 0.35);
  box-shadow:
    /* left-side far glow */
    -40px 0 80px rgba(208, 153, 42, 0.25),
    /* left-side deceleration glow */ -18px 0 40px rgba(208, 153, 42, 0.45),
    -6px 0 16px rgba(255, 233, 160, 0.3),
    /* general ring glow */ 0 0 0 1px rgba(208, 153, 42, 0.45),
    0 0 60px rgba(0, 0, 0, 0.6),
    0 0 120px rgba(208, 153, 42, 0.12);
}

@media (min-width: 768px) {
  .logo-ring {
    z-index: 20;
    width: 380px;
    height: 380px;
    border-width: 4px;
    box-shadow:
      -50px 0 100px rgba(208, 153, 42, 0.2),
      -28px 0 60px rgba(208, 153, 42, 0.5),
      -10px 0 24px rgba(255, 233, 160, 0.35),
      0 0 0 1px rgba(208, 153, 42, 0.45),
      0 0 80px rgba(0, 0, 0, 0.6),
      0 0 160px rgba(208, 153, 42, 0.12);
  }
}
</style>
