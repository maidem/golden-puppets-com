<template>
  <div v-editable="blok" class="border-t-2 border-black">
    <button
      class="w-full flex items-center gap-8 py-8 text-left group px-16"
      :aria-expanded="open"
      @click="toggle($event)"
    >
      <!-- Nummer -->
      <span
        class="shrink-0 w-[640px] text-sm font-bold text-black tabular-nums hidden md:block"
      >
        {{ String((blok._index ?? 0) + 1).padStart(2, "0") }}
      </span>

      <span class="flex-1 text-lg font-semibold text-gray-900">
        {{ blok.question }}
      </span>

      <!-- Kreuz mit gelbem Hover-Kreis -->
      <span
        class="shrink-0 w-10 h-10 flex items-center justify-center rounded-full group-hover:bg-[#FFE600]"
        :style="{
          transform: open ? 'rotate(45deg)' : 'rotate(0deg)',
          backgroundColor: open ? '#FFE600' : '',
          transition: 'transform 0.3s ease, background-color 0.2s',
        }"
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
    </button>

    <!-- Smooth accordion — pl-56 = w-48 (192px) + gap-8 (32px) = 224px, bündig mit Fragetext -->
    <div
      class="overflow-hidden"
      :style="
        open
          ? 'max-height: 800px; opacity: 1; transition: max-height 0.4s ease, opacity 0.3s ease;'
          : 'max-height: 0; opacity: 0; transition: max-height 0.3s ease, opacity 0.2s ease;'
      "
    >
      <!-- Antwort: gleiche Flex-Struktur wie Button, startet bei 704px -->
      <div class="flex gap-8 px-16 pb-8">
        <span class="shrink-0 w-[640px] hidden md:block" aria-hidden="true" />
        <div
          class="flex-1 max-w-[992px] text-gray-600 leading-relaxed prose prose-sm"
          v-html="renderedAnswer"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({ blok: Object });
const open = ref(false);
const renderedAnswer = computed(() => renderRichText(props.blok?.answer ?? {}));

function toggle(e) {
  open.value = !open.value;
  if (!open.value) e.currentTarget.blur();
}
</script>
