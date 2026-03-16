<template>
  <section
    class="relative flex flex-col items-center justify-center text-center px-4 min-h-[85vh] bg-gradient-to-br from-white to-gray-50 overflow-hidden"
  >
    <!-- Background image -->
    <div
      v-if="bgImage"
      class="absolute inset-0 bg-cover bg-center bg-no-repeat"
      :style="{ backgroundImage: `url(${bgImage})` }"
    />
    <!-- Overlay when bg image is set -->
    <div v-if="bgImage" class="absolute inset-0 bg-black/50" />
    <!-- Content -->
    <div class="relative z-10 flex flex-col items-center">
      <BaseHeading
        tag="h1"
        size="xl"
        :class="['mb-4', bgImage ? 'text-white' : 'text-gray-900']"
      >
        {{ headline }}
      </BaseHeading>

      <p
        v-if="subline"
        :class="[
          'text-xl max-w-2xl mb-10 leading-relaxed',
          bgImage ? 'text-white/80' : 'text-gray-500',
        ]"
      >
        {{ subline }}
      </p>

      <component
        :is="isExternal ? 'a' : resolvedLink ? NuxtLink : 'span'"
        v-if="ctaLabel"
        :href="isExternal ? resolvedLink : undefined"
        :to="!isExternal && resolvedLink ? resolvedLink : undefined"
        :target="isExternal ? '_blank' : undefined"
        :rel="isExternal ? 'noopener noreferrer' : undefined"
        class="inline-block"
      >
        <BaseButton :variant="ctaVariant">
          {{ ctaLabel }}
        </BaseButton>
      </component>
    </div>
  </section>
</template>

<script setup>
import { NuxtLink } from "#components";

const props = defineProps({
  headline: {
    type: String,
    required: true,
  },
  subline: {
    type: String,
    default: "",
  },
  ctaLabel: {
    type: String,
    default: "",
  },
  resolvedLink: {
    type: String,
    default: "",
  },
  ctaVariant: {
    type: String,
    default: "primary", // primary | secondary | outline
  },
  bgImage: {
    type: String,
    default: "",
  },
});

const isExternal = computed(
  () =>
    props.resolvedLink.startsWith("http") ||
    props.resolvedLink.startsWith("mailto:"),
);
</script>
