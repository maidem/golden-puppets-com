<template>
  <section v-editable="blok" class="py-16 px-4">
    <div class="max-w-6xl mx-auto">
      <header v-if="blok.headline || blok.subline" class="mb-12 text-center">
        <h2
          v-if="blok.headline"
          class="text-3xl md:text-4xl font-bold text-gray-900"
        >
          {{ blok.headline }}
        </h2>
        <p v-if="blok.subline" class="mt-3 text-gray-500">{{ blok.subline }}</p>
      </header>

      <div :class="['grid gap-10', gridCols]">
        <StoryblokComponent
          v-for="member in blok.members"
          :key="member._uid"
          :blok="member"
        />
      </div>
    </div>
  </section>
</template>

<script setup>
const props = defineProps({ blok: Object });

const gridCols = computed(() => {
  const map = {
    2: "grid-cols-1 sm:grid-cols-2",
    3: "grid-cols-1 sm:grid-cols-2 md:grid-cols-3",
    4: "grid-cols-2 md:grid-cols-4",
  };
  return map[props.blok?.columns] || map["3"];
});
</script>
