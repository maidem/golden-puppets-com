<template>
  <article v-editable="blok" class="max-w-3xl mx-auto px-4 py-16">
    <!-- Hero-Bild -->
    <div
      v-if="blok.image?.filename"
      class="rounded-2xl overflow-hidden mb-10 aspect-video"
    >
      <img
        :src="blok.image.filename"
        :alt="blok.headline"
        class="w-full h-full object-cover"
      />
    </div>

    <BaseHeading tag="h1" size="lg" class="mb-6 text-gray-900">
      {{ blok.headline }}
    </BaseHeading>

    <!-- Richtext Body -->
    <div
      v-if="bodyHtml"
      class="text-gray-700 leading-relaxed space-y-4 [&_h2]:text-2xl [&_h2]:font-bold [&_h2]:mt-8 [&_h2]:mb-3 [&_h3]:text-xl [&_h3]:font-semibold [&_h3]:mt-6 [&_h3]:mb-2 [&_ul]:list-disc [&_ul]:pl-6 [&_ol]:list-decimal [&_ol]:pl-6 [&_a]:text-[#00b3b0] [&_a]:underline [&_a:hover]:text-[#009a97]"
      v-html="bodyHtml"
    />
  </article>
</template>

<script setup>
const props = defineProps({ blok: Object })

const bodyHtml = computed(() =>
  props.blok?.body ? renderRichText(props.blok.body) : '',
)
</script>
