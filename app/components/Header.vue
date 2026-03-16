<template>
  <header v-editable="blok" class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-100 py-4">
    <div class="container mx-auto flex items-center justify-between px-4">
      <!-- Logo -->
      <NuxtLink to="/" class="flex items-center gap-2">
        <img
          v-if="blok.logo?.filename"
          :src="blok.logo.filename"
          :alt="blok.logo.alt || 'Logo'"
          class="h-10 w-auto"
        />
        <span v-else class="text-2xl font-black tracking-tighter text-[#00b3b0]">
          GOLDEN<span class="text-gray-900">PUPPETS</span>
        </span>
      </NuxtLink>

      <!-- Navigation -->
      <nav class="hidden md:flex items-center space-x-2">
        <div v-for="navItem in blok.nav_menu" :key="navItem._uid">
          <!-- Fallback für Schlangen-Case Problematik in Nuxt 4 -->
          <NavItem v-if="navItem.component === 'nav_item'" :blok="navItem" />
          <StoryblokComponent v-else :blok="navItem" />
        </div>
      </nav>

      <!-- Mobile Button Placeholder -->
      <div class="md:hidden">
        <BaseButton variant="outline" class="px-4 py-2 text-sm">Menu</BaseButton>
      </div>
    </div>
  </header>
</template>

<script setup>
defineProps({ blok: Object })
</script>
