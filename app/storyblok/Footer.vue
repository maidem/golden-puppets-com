<template>
  <footer v-editable="blok" class="border-t-2 border-black bg-white mt-24">
    <div
      class="px-8 md:px-16 py-12 flex flex-col md:flex-row md:items-center md:justify-end gap-8"
    >
      <nav
        v-if="blok.nav_menu?.length"
        class="flex flex-wrap gap-x-2 gap-y-2 footer-nav"
      >
        <StoryblokComponent
          v-for="item in blok.nav_menu"
          :key="item._uid"
          :blok="item"
        />
      </nav>
    </div>
  </footer>
</template>

<script setup>
defineProps({ blok: Object });
</script>

<style scoped>
.footer-nav :deep(a::after) {
  display: none;
}
</style>
