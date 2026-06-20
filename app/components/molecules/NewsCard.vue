<template>
  <article
    class="bg-white rounded-2xl overflow-hidden flex flex-col h-[25rem] group"
  >
    <!-- Bild -->
    <div class="aspect-video overflow-hidden bg-gray-100">
      <img
        v-if="image"
        :src="optimizedImage"
        :alt="headline"
        loading="lazy"
        decoding="async"
        class="w-full h-full object-cover"
      />
      <div
        v-else
        class="w-full h-full bg-gradient-to-br from-teal-50 to-teal-100 flex items-center justify-center"
      >
        <span class="text-[#00b3b0] text-5xl">🎭</span>
      </div>
    </div>

    <!-- Inhalt -->
    <div class="p-6 flex flex-col flex-grow">
      <BaseHeading
        tag="h3"
        size="sm"
        class="mb-3 text-black tracking-tight font-semibold"
      >
        {{ headline }}
      </BaseHeading>
      <p
        v-if="teaserText"
        class="text-gray-700 text-sm leading-relaxed mb-6 flex-grow line-clamp-3"
      >
        {{ teaserText }}
      </p>

      <div class="mt-auto pt-2">
        <NuxtLink :to="`/${link}`" class="inline-block">
          <span
            class="inline-flex items-center gap-2 bg-[#231F21] text-white font-bold text-sm px-5 py-2.5 rounded-full border-2 border-[#231F21] hover:bg-black hover:border-black transition-colors duration-200"
            >Mehr&nbsp;...</span
          >
        </NuxtLink>
      </div>
    </div>
  </article>
</template>

<script setup>
import BaseHeading from "~/components/atoms/BaseHeading.vue";

const props = defineProps({
  image: { type: String, default: "" },
  headline: { type: String, required: true },
  teaserText: { type: String, default: "" },
  link: { type: String, required: true },
});


const optimizedImage = computed(() => toStoryblokImage(props.image, 800));
</script>
