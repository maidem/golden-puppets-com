<template>
  <header v-editable="blok" class="sticky top-0 z-50 bg-black">
    <div class="flex items-stretch min-h-[160px] pb-[2px]">
      <!-- Logo: gelbe Box, nur rechts unten abgerundet (links = Screenrand) -->
      <NuxtLink
        to="/"
        class="flex items-center justify-center shrink-0 bg-[#FFE600] rounded-br-[1.75rem]"
        style="width: 704px"
      >
        <img
          v-if="blok.logo?.filename"
          :src="blok.logo.filename"
          :alt="blok.logo.alt || 'Logo'"
          class="h-12 w-auto"
        />
        <span v-else class="text-2xl font-black tracking-tighter text-black">
          GOLDEN<span class="text-gray-800">PUPPETS</span>
        </span>
      </NuxtLink>

      <!-- 2px schwarzer Spalt in der Mitte -->
      <div class="w-[2px] shrink-0"></div>

      <!-- Navigation: gelbe Box, nur links unten abgerundet (rechts = Screenrand) -->
      <nav
        class="hidden md:flex flex-1 items-center justify-end px-8 gap-8 bg-[#FFE600] rounded-bl-[1.75rem]"
      >
        <div v-for="navItem in blok.nav_menu" :key="navItem._uid">
          <NavItem v-if="navItem.component === 'nav_item'" :blok="navItem" />
          <StoryblokComponent v-else :blok="navItem" />
        </div>
      </nav>

      <!-- Mobile Button -->
      <div class="md:hidden flex items-center px-4 bg-[#FFE600]">
        <BaseButton variant="outline" class="px-4 py-2 text-sm"
          >Menu</BaseButton
        >
      </div>
    </div>
  </header>
</template>

<script setup>
defineProps({ blok: Object });
</script>
