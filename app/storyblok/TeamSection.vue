<template>
  <section
    v-editable="blok"
    :id="blok.section_id || 'team'"
    class="py-10 md:py-16 px-5 md:px-16"
  >
    <div class="max-w-[1400px] mx-auto">
      <!-- Headline links-ausgerichtet wie FAQ/Kontakt -->
      <div v-if="blok.headline || blok.subline" class="mb-7">
        <h2
          v-if="blok.headline"
          class="text-3xl md:text-5xl font-black leading-[1.1] tracking-tight text-black"
        >
          {{ blok.headline }}<span class="text-[#D0992A]">.</span>
        </h2>
        <p v-if="blok.subline" class="mt-2 text-gray-500 text-base">
          {{ blok.subline }}
        </p>
      </div>

      <!-- Mobile: horizontal drag-scroll; Desktop: grid -->
      <div
        ref="scrollEl"
        class="md:hidden flex gap-4 overflow-x-auto pb-4 cursor-grab active:cursor-grabbing"
        style="
          scroll-snap-type: x mandatory;
          -webkit-overflow-scrolling: touch;
          scrollbar-width: none;
        "
        @mousedown="onMouseDown"
        @mousemove="onMouseMove"
        @mouseup="onMouseUp"
        @mouseleave="onMouseUp"
      >
        <StoryblokComponent
          v-for="member in blok.members"
          :key="member._uid"
          :blok="member"
          style="scroll-snap-align: start; flex: 0 0 72vw; max-width: 280px"
        />
      </div>

      <!-- Desktop grid -->
      <div :class="['hidden md:grid gap-6', gridCols]">
        <StoryblokComponent
          v-for="member in blok.members"
          :key="member._uid"
          :blok="member"
        />
      </div>
    </div>
  </section>
</template>

<script setup>
const props = defineProps({ blok: Object });

const gridCols = computed(() => {
  const n = props.blok?.columns;
  const map = {
    2: "grid-cols-2",
    3: "grid-cols-3",
    4: "grid-cols-2 lg:grid-cols-4",
    5: "grid-cols-3 lg:grid-cols-5",
  };
  const count = (props.blok?.members || []).length;
  if (map[n]) return map[n];
  if (count <= 3) return "grid-cols-3";
  if (count === 4) return "grid-cols-2 lg:grid-cols-4";
  return "grid-cols-3 lg:grid-cols-5";
});

const scrollEl = ref(null);
let isDragging = false;
let startX = 0;
let scrollLeft = 0;

function onMouseDown(e) {
  isDragging = true;
  startX = e.pageX - scrollEl.value.offsetLeft;
  scrollLeft = scrollEl.value.scrollLeft;
  scrollEl.value.style.scrollSnapType = "none";
}
function onMouseMove(e) {
  if (!isDragging) return;
  e.preventDefault();
  const x = e.pageX - scrollEl.value.offsetLeft;
  scrollEl.value.scrollLeft = scrollLeft - (x - startX);
}
function onMouseUp() {
  isDragging = false;
  if (scrollEl.value) scrollEl.value.style.scrollSnapType = "x mandatory";
}
</script>
