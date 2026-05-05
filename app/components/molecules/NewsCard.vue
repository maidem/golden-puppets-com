<template>
  <article
    class="bg-[#2a2a2a] rounded-2xl border-2 border-black overflow-hidden flex flex-col group hover:shadow-2xl transition-shadow duration-300"
  >
    <!-- Bild -->
    <div class="aspect-video overflow-hidden bg-gray-100">
      <img
        v-if="image"
        :src="image"
        :alt="headline"
        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
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
        class="mb-3 text-white tracking-tight font-semibold"
      >
        {{ headline }}
      </BaseHeading>
      <p
        v-if="teaserText"
        class="text-gray-300 text-sm leading-relaxed mb-6 flex-grow line-clamp-3"
      >
        {{ teaserText }}
      </p>

      <div class="mt-auto pt-2">
        <NuxtLink :to="`/${link}`" class="inline-block">
          <span
            class="inline-flex items-center gap-2 bg-[#D0992A] text-[#231F21] font-bold text-sm px-5 py-2.5 rounded-full border-2 border-[#D0992A] hover:bg-transparent hover:text-[#D0992A] transition-colors duration-200"
            >Mehr&nbsp;…</span
          >
        </NuxtLink>
      </div>
    </div>
  </article>
</template>

<script setup>
defineProps({
  image: { type: String, default: "" },
  headline: { type: String, required: true },
  teaserText: { type: String, default: "" },
  link: { type: String, required: true },
});
</script>
