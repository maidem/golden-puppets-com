<template>
  <div v-editable="blok" class="border-t border-gray-200 last:border-b">
    <button
      class="w-full flex items-center justify-between gap-8 py-6 text-left group"
      :aria-expanded="open"
      @click="open = !open"
    >
      <span class="text-lg font-semibold text-gray-900 group-hover:text-black transition-colors">
        {{ blok.question }}
      </span>
      <span
        class="shrink-0 w-7 h-7 flex items-center justify-center rounded-full border border-gray-300 group-hover:border-black transition-all duration-300"
        :class="open ? 'rotate-45' : 'rotate-0'"
        style="transition: transform 0.3s ease, border-color 0.2s"
      >
        <svg class="w-3.5 h-3.5" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="7" y1="0" x2="7" y2="14" />
          <line x1="0" y1="7" x2="14" y2="7" />
        </svg>
      </span>
    </button>

    <!-- Smooth accordion -->
    <div
      class="grid transition-all duration-300 ease-in-out"
      :class="open ? 'grid-rows-[1fr] opacity-100' : 'grid-rows-[0fr] opacity-0'"
    >
      <div class="overflow-hidden">
        <div class="pb-6 text-gray-600 leading-relaxed prose prose-sm max-w-none">
          <RichTextRenderer v-if="blok.answer" :document="blok.answer" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { RichTextRenderer } from "@storyblok/nuxt";

defineProps({ blok: Object });
const open = ref(false);
</script>
