<template>
  <details v-editable="blok" class="group border-t-2 border-black">
    <summary
      class="w-full flex items-center gap-8 py-8 text-left px-16 list-none cursor-pointer"
    >
      <!-- Nummer -->
      <span
        class="shrink-0 w-[640px] text-4xl font-black text-black tabular-nums hidden md:block"
      >
        {{ String((blok._index ?? 0) + 1).padStart(2, "0") }}
      </span>

      <span class="flex-1 text-lg font-semibold text-gray-900">
        {{ blok.question }}
      </span>

      <!-- Kreuz mit gelbem Hover-Kreis -->
      <span
        class="shrink-0 w-10 h-10 flex items-center justify-center rounded-full group-hover:bg-[#FFE600]"
        :style="{ transition: 'transform 0.3s ease, background-color 0.2s' }"
      >
        <svg
          class="w-5 h-5"
          viewBox="0 0 20 20"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="square"
        >
          <line x1="10" y1="0" x2="10" y2="20" />
          <line x1="0" y1="10" x2="20" y2="10" />
        </svg>
      </span>
    </summary>

    <!-- Antwort: gleiche Flex-Struktur wie Summary, startet bei 704px -->
    <div class="flex gap-8 px-16 pb-8 animate-faq-open">
      <span class="shrink-0 w-[640px] hidden md:block" aria-hidden="true" />
      <div
        class="flex-1 max-w-[992px] text-gray-600 leading-relaxed prose prose-sm"
        v-html="renderedAnswer"
      />
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

.group[open] summary span:last-child {
  transform: rotate(45deg);
  background-color: #ffe600;
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
