<template>
  <footer v-editable="blok" class="border-t border-gray-100 bg-white mt-24">
    <div
      class="container mx-auto px-4 py-12 flex flex-col md:flex-row md:items-center md:justify-between gap-6"
    >
      <p v-if="blok.tagline" class="text-sm text-gray-500 max-w-md">
        {{ blok.tagline }}
      </p>

      <nav v-if="blok.nav_menu?.length" class="flex flex-wrap gap-x-6 gap-y-2">
        <StoryblokComponent
          v-for="item in blok.nav_menu"
          :key="item._uid"
          :blok="item"
        />
      </nav>
    </div>

    <div class="border-t border-gray-100">
      <p class="container mx-auto px-4 py-4 text-xs text-gray-400">
        {{ blok.copyright || "© Golden Puppets" }}
      </p>
    </div>
  </footer>
</template>

<script setup>
defineProps({ blok: Object });
</script>
