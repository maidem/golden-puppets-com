<template>
  <footer v-editable="blok" class="border-t-2 border-black bg-white mt-24">
    <div
      class="px-5 md:px-16 py-10 md:py-12 flex flex-col items-start md:flex-row md:items-center md:justify-end gap-6 md:gap-8"
    >
      <div class="h-10 w-10 hidden" aria-hidden="true"></div>

      <nav
        v-if="blok.nav_menu?.length"
        class="flex flex-col items-start gap-y-2 md:flex-row md:flex-wrap md:gap-x-2 footer-nav"
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
.footer-nav :deep(a) {
  color: black;
}

.footer-nav :deep(a::after) {
  display: none;
}

@media (max-width: 767px) {
  .footer-nav :deep(a) {
    font-size: 1.05rem;
    line-height: 1.35;
    padding: 0.25rem 0;
  }
}
</style>
