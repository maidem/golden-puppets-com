<template>
  <section v-editable="blok" class="py-10 md:py-16">
    <h2
      v-if="blok.headline"
      class="text-3xl md:text-5xl font-black tracking-tight mb-8 md:mb-12 px-5 md:px-16"
    >
      {{ blok.headline }}
    </h2>

    <div class="px-5 md:px-16">
      <div class="border-b-2 border-black">
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
