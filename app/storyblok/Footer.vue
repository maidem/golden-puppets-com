<template>
  <footer class="border-t border-gray-100 bg-white mt-24">
    <div class="max-w-[1400px] mx-auto px-5 md:px-16">
      <div
        class="py-10 md:py-12 flex flex-col items-start md:flex-row md:items-center md:justify-end gap-6 md:gap-8"
      >
        <div class="h-10 w-10 hidden" aria-hidden="true"></div>

        <nav
          v-if="blok.nav_menu?.length"
          class="flex flex-col items-start gap-y-2 md:flex-row md:flex-wrap md:gap-x-2 footer-nav"
        >
          <a
            v-for="item in blok.nav_menu"
            :key="item._uid"
            :href="navHref(item)"
            class="footer-link"
          >
            {{ item.label }}
          </a>
        </nav>
      </div>
    </div>
  </footer>
</template>

<script setup>
const props = defineProps({ blok: Object });

function navHref(item) {
  if (item?.section_id) return `/#${item.section_id}`;
  const url = item?.link?.cached_url || item?.link?.url || "";
  if (!url || url === "home") return "/#home";
  if (url.startsWith("#")) return `/${url}`;
  return url.startsWith("/") ? url : `/${url}`;
}
</script>

<style scoped>
.footer-link {
  color: black;
  font-size: 0.85rem;
  font-weight: 500;
  padding: 0.25rem 0.75rem;
  text-decoration: none;
  transition: color 0.2s;
}

.footer-link:hover {
  color: #d0992a;
}

@media (max-width: 767px) {
  .footer-link {
    font-size: 1.05rem;
    line-height: 1.35;
    padding: 0.25rem 0;
  }
}
</style>
