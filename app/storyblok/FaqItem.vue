<template>
  <details v-editable="blok" class="group bg-white rounded-2xl overflow-hidden">
    <summary
      class="w-full px-5 py-5 md:px-6 md:py-6 text-left list-none cursor-pointer select-none"
    >
      <div class="flex items-center justify-between gap-4">
        <span
          class="text-base md:text-lg font-semibold text-gray-900 leading-snug"
        >
          {{ blok.question }}
        </span>

        <span
          class="faq-toggle shrink-0 w-9 h-9 flex items-center justify-center rounded-full bg-black text-white transition-transform duration-300"
        >
          <svg
            class="w-4 h-4"
            viewBox="0 0 16 16"
            fill="none"
            stroke="currentColor"
            stroke-width="2.5"
            stroke-linecap="round"
          >
            <line x1="8" y1="1" x2="8" y2="15" />
            <line x1="1" y1="8" x2="15" y2="8" />
          </svg>
        </span>
      </div>
    </summary>

    <div
      class="px-5 pb-5 md:px-6 md:pb-6 text-gray-600 text-sm leading-relaxed animate-faq-open"
    >
      <div class="prose prose-sm max-w-none" v-html="renderedAnswer" />
    </div>
  </details>
</template>

<script setup>
const props = defineProps({ blok: Object });
const renderedAnswer = computed(() => renderRichText(props.blok?.answer ?? {}));
</script>

<style scoped>
summary::-webkit-details-marker {
  display: none;
}

.group[open] summary .faq-toggle {
  transform: rotate(45deg);
}

.animate-faq-open {
  animation: faq-open 0.25s ease-out;
}

@keyframes faq-open {
  from {
    opacity: 0;
    transform: translateY(-4px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
