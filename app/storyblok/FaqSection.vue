<template>
  <section v-editable="blok" class="py-16 px-4 max-w-4xl mx-auto">
    <h2 v-if="blok.headline" class="text-6xl font-black tracking-tight mb-12">
      {{ blok.headline }}
    </h2>

    <div>
      <StoryblokComponent
        v-for="item in blok.items"
        :key="item._uid"
        :blok="item"
      />
    </div>
  </section>
</template>

<script setup>
defineProps({ blok: Object });
</script>
