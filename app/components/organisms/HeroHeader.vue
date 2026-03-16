<template>
  <section class="relative flex flex-col items-center justify-center text-center px-4 py-24 md:py-36 bg-gradient-to-br from-white to-gray-50">
    <BaseHeading tag="h1" size="xl" class="mb-4 text-gray-900">
      {{ headline }}
    </BaseHeading>

    <p v-if="subline" class="text-xl text-gray-500 max-w-2xl mb-10 leading-relaxed">
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
  </section>
</template>

<script setup>
import { NuxtLink } from '#components'

const props = defineProps({
  headline: {
    type: String,
    required: true
  },
  subline: {
    type: String,
    default: ''
  },
  ctaLabel: {
    type: String,
    default: ''
  },
  resolvedLink: {
    type: String,
    default: ''
  },
  ctaVariant: {
    type: String,
    default: 'primary' // primary | secondary | outline
  }
})

const isExternal = computed(() =>
  props.resolvedLink.startsWith('http') || props.resolvedLink.startsWith('mailto:')
)
</script>
