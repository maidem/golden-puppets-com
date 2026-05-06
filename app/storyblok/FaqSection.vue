<template>
  <section
    v-editable="blok"
    :id="blok.section_id || 'faq'"
    class="py-10 md:py-16 bg-[#f5f4f2] px-5 md:px-16"
  >
    <div class="max-w-[1400px] mx-auto">
      <h2
        v-if="blok.headline"
        class="text-2xl md:text-4xl font-bold tracking-tight mb-3"
      >
        {{ blok.headline }}<span class="text-[#D0992A]">.</span>
      </h2>
      <p
        v-if="blok.subline"
        class="text-gray-500 text-base mb-8 md:mb-10 max-w-md"
      >
        {{ blok.subline }}
      </p>

      <div class="flex flex-col gap-3">
        <StoryblokComponent
          v-for="(item, index) in blok.items"
          :key="item._uid"
          :blok="{ ...item, _index: index }"
        />
      </div>
    </div>
  </section>
</template>

<script setup>
defineProps({ blok: Object });
</script>
